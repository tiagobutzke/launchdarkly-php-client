VOLO.MapModalView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.template = _.template($('#template-map-modal-dialog').html());
        this.appConfig = options.appConfig;
        this.deserializer = new VOLO.Geocoding.PlaceDeserializer();
        this.model = new VOLO.FullAddressLocationModel({}, {
            appConfig: options.appConfig
        });
    },

    events: function() {
        return {
            'keydown .map-modal__autocomplete__input': '_onAutocompleteKeyDown',
            'submit .map-modal__autocomplete': '_onAutocompleteSubmit',
            'click .map-modal__submit': _.throttle(this._submit, 400, {trailing: false})
        };
    },

    _initListeners: function () {
        this.listenTo(this.model, 'invalid', this._displayErrorMessage);
        this.listenTo(this.model, 'invalid', this._disableSubmitButton);
        this.listenTo(this.model, 'change', this._enableSubmitButton);
    },

    _onAutocompleteKeyDown: function(e) {
        this._hideInputError();
        if (e.keyCode === 9) { // tab
            this._updatePositionFromInput();
        }
    },

    _onAutocompleteSubmit: function() {
        this._updatePositionFromInput();

        return false;
    },

    _updatePositionFromInput: function() {
        var firstResultValue = this.autocomplete.getFirstResultValue(),
            geocoder = new VOLO.Geocoding.Geocoder(this.appConfig),
            deferred = $.Deferred(),
            $input = this.$('.map-modal__autocomplete__input');

        if (this.model.get('formattedAddress') === firstResultValue) {
            deferred.resolve(this.model.toJSON());
        } else if ($input.val()) {
            geocoder.geocodeAddress(firstResultValue).then(function(addresses) {
                this._centerMapOnFirstAddress(addresses);
                deferred.resolve(this.deserializer.deserialize(addresses[0]));
            }.bind(this), function(error) {
                console.log('submit error', error);
                deferred.reject($input.data('msg_error_not_found'));
            }.bind(this));
        } else {
            console.log('empty input');
            deferred.reject($input.data('msg_error_empty'));
        }

        return deferred;
    },

    _initAutocomplete: function() {
        if (!this.autocomplete) {
            this.autocomplete = new VOLO.Geocoding.Autocomplete();
            this.autocomplete.init(this.$('.map-modal__autocomplete__input'), this.appConfig);

            this.listenTo(this.autocomplete, 'autocomplete-search:place-found', this._placeFound);
            this.listenTo(this.autocomplete, 'geocoder-search:place-found', this._placeFound);
            this.$('.map-modal__autocomplete__input').blur();
        }
    },

    _centerMapOnFirstAddress: function(addresses) {
        var location = addresses[0].geometry.location;

        this._updateAddress(this.deserializer.deserialize(addresses[0]));
        this.map.setCenter(location.lat(), location.lng());
    },

    _submit: function() {
        this._updatePositionFromInput().then(function() {
            if (!this.model.validationError) {
                this.trigger('map-dialog:address-submit', this.model.toJSON());
            } else {
                this._displayErrorMessage(this.model, this.model.validationError);
            }
        }.bind(this), this.showInputError);

        return false;
    },

    _displayErrorMessage: function(model, error) {
        var data = this.$('.map-modal__autocomplete__input').data(),
            errorMessage = _.get(data, 'msg_error_' + error, data.msg_error_not_found);

        this.trigger('map-dialog:gtm-error-shown', {
            event: 'errorMap',
            errorCode: errorMessage
        });
        this.showInputError(errorMessage);

        if (error === 'building' && _.get(this.appConfig, 'address_config.update_map_input')) {
            this._updateTitle(error);
            this._updateInputField();
        }
    },

    _updateTitle: function(titleId) {
        var titleMsg = this.$('.map-modal__autocomplete__input').data('msg_title_' + titleId),
            $title = this.$('.map-modal__header-content');

        if (!titleMsg) {
            titleMsg = this.$('.map-modal__autocomplete__input').data('msg_title_default');
        }

        $title.text(titleMsg);
        this.map.resize();
    },

    _updateInputField: function() {
        var $input = this.$('.map-modal__autocomplete__input'),
            inputVal = $input.val().split(',')[0].trim() + ' ';

        $input.val(inputVal);
    },

    showInputError: function(text) {
        this.$('.map-modal__error-message').html(text).removeClass('hide');
    },

    _hideInputError: function() {
        this.$('.map-modal__error-message').addClass('hide');
    },

    render: function() {
        if (!this.$('.map-modal').length) {
            this.$el.append(this.template());
        }
        if (!this.map) {
            this.map = new VOLO.Geocoding.Map();
            this.map.insert(this.$('.map-modal__map'), {
                zoom: 16,
                disableDefaultUI: true,
                streetViewControl: false,
                rotateControlOptions: false,
                mapTypeControl: false,
                noClear: true,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM,
                    style: google.maps.ZoomControlStyle.SMALL
                },
                scrollwheel: false
            });
        }

        return this;
    },

    show: function(address) {
        var modalDialog = this.$('.map-modal');
        modalDialog.focus();

        this.curriedDialogShown = function() {
            _.curry(this._dialogShown)(address);
        }.bind(this);

        this._initListeners();
        modalDialog.on('shown.bs.modal', this.curriedDialogShown);
        modalDialog.on('hide.bs.modal', this._dialogHide);
        modalDialog.modal();

        if (this.model.validationError === 'building' && _.get(this.appConfig, 'address_config.update_map_input')) {
            this._hideInputError();
            this._updateTitle(this.model.validationError);
            this._updateInputField();

            this.$('.map-modal__autocomplete__input').focus();
        }
    },

    isVisible: function() {
        return this.$('.map-modal').is(':visible');
    },

    hide: function() {
        this.$('.map-modal').modal('hide');
    },

    _disableSubmitButton: function() {
        this.$('.map-modal__submit__button').addClass('button--disabled');
    },

    _enableSubmitButton: function() {
        this.$('.map-modal__submit__button').removeClass('button--disabled');
    },

    isOpen: function() {
        return this.$('.map-modal').hasClass('in');
    },

    _dialogShown: function(address) {
        this._updateTitle('default');
        this.map.resize(); //because of maps bug
        this.map.setCenter(address.latitude, address.longitude);
        this._initAutocomplete();

        this.listenTo(this.map, 'map:center-changed', _.debounce(this._centerChanged, 400, {leading: false}));
        this.listenTo(this.map, 'map:drag-start', this._onMapDragStart);


        this._updateAddress(address);
    },

    _onMapDragStart: function() {
        this.$('.map-modal__autocomplete__input').blur();
    },

    _placeFound: function(place) {
        var location = place.geometry.location,
            address = this.deserializer.deserialize(place, this.appConfig);

        this.map.setCenter(location.lat(), location.lng());
        this._updateAddress(address);
    },

    _updateAddress: function(address) {
        console.log('MapDialog._updateAddress', address);
        this.$('.map-modal__autocomplete__input').val(address.formattedAddress);
        this.model.set(address, {validate: true});

        if (_.isNull(this.model.validationError)) {
            this._updateTitle('default');
            this._hideInputError();
            this._enableSubmitButton();
        }
    },

    _centerChanged: function(center) {
        this._hideInputError();

        this._getCenterAddress(center).then(function(address) {
            var $input = this.$('.map-modal__autocomplete__input');
            $input.val(address.formattedAddress);
            $input.focus().blur(); //update google autocomplete :(

            address.latitude = center.lat();
            address.longitude = center.lng();

            this._updateAddress(address);
        }.bind(this));
    },

    _getCenterAddress: function(center) {
        var geocoder = new VOLO.Geocoding.Geocoder(),
            deferred = $.Deferred();

        geocoder.geocodeLatLng(center.lat(), center.lng()).then(function(results) {
            deferred.resolve(this.deserializer.deserialize(results[0], this.appConfig));
        }.bind(this), function(e) {
            deferred.reject(e);
            console.log(e);
        });

        return deferred;
    },

    _dialogHide: function() {
        this._unbindAutocomplete();
        this.map.cleanLocation();

        this.stopListening();
        this.$('.map-modal').off('shown.bs.modal', this.curriedDialogShown);
        this.$('.map-modal').off('hide.bs.modal', this._dialogHide);

        this.trigger('map-dialog:hide', this.model.toJSON());
    },

    _unbindAutocomplete: function() {
        this.autocomplete && this.autocomplete.unbind();
        this.autocomplete = null;
    },

    unbind: function() {
        this.map && this.map.unbind();
        this._unbindAutocomplete();
        this.stopListening();
        this.undelegateEvents();
    }
});
