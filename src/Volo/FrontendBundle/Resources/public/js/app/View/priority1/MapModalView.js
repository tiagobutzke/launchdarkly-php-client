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

    events: {
        'keydown .map-modal__autocomplete__input': '_onAutocompleteKeyDown',
        'submit .map-modal__autocomplete': '_updatePositionFromInput',
        'click .map-modal__submit': '_submit'
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

    _updatePositionFromInput: function() {
        var firstResultValue = this.autocomplete.getFirstResultValue(),
            geocoder = new VOLO.Geocoding.Geocoder(this.appConfig);

        if (this.$('.map-modal__autocomplete__input').val()) {
            geocoder.geocodeAddress(firstResultValue).then(this._centerMapOnFirstAddress, function(error) {
                console.log('submit error', error);
                this._showInputError(this.$('.map-modal__autocomplete__input').data('msg_error_not_found'));
            }.bind(this));
        } else {
            console.log('empty input');
            this._showInputError(this.$('.map-modal__autocomplete__input').data('msg_error_empty'));
        }

        return false;
    },

    _centerMapOnFirstAddress: function(addresses) {
        var location = addresses[0].geometry.location;

        this._updateAddress(this.deserializer.deserialize(addresses[0]));
        this.map.setCenter(location.lat(), location.lng());
    },

    _submit: function() {
        if (this.model.get('formattedAddress') !== this.$('.map-modal__autocomplete__input').val()) {
            this._updatePositionFromInput();
        } else if (this.model.isValid()) {
            this.trigger('map-dialog:address-submit', this.model.toJSON());
            this.unbind();
        }

        return false;
    },
    _displayErrorMessage: function(model, error) {
        var errorMessage = _.get(this.$('.map-modal__autocomplete__input').data(), 'msg_error_' + error, 'msg_error_not_found');
        this.trigger('map-dialog:gtm-error-shown', {
            event: 'errorMap',
            errorCode: errorMessage
        });
        this._showInputError(errorMessage);
    },

    _showInputError: function(text) {
        this.$('.map-modal__error-message').text(text).removeClass('hide');
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

        this.autocomplete = new VOLO.Geocoding.Autocomplete();

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
    },

    hide: function() {
        this.$('.map-modal').modal('hide');
    },

    _disableSubmitButton: function() {
        this.$('.map-modal__submit__button').attr("disabled","disabled").addClass('button--disabled');
    },

    _enableSubmitButton: function() {
        this.$('.map-modal__submit__button').removeAttr("disabled").removeClass('button--disabled');
    },

    isOpen: function() {
        return this.$('.map-modal').hasClass('in');
    },

    _dialogShown: function(address) {
        this.map.resize(); //because of maps bug
        this.map.setCenter(address.latitude, address.longitude);
        this.listenTo(this.map, 'map:center-changed', _.debounce(this._centerChanged, 400, {leading: false}));

        this.autocomplete.init(this.$('.map-modal__autocomplete__input'), this.appConfig);
        this._updateAddress(address);

        this.listenTo(this.autocomplete, 'autocomplete-search:place-found', this._placeFound);
        this.listenTo(this.autocomplete, 'geocoder-search:place-found', this._placeFound);
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
            this._hideInputError();
            this._enableSubmitButton();
        }
    },

    _centerChanged: function(center) {
        this._hideInputError();

        this._getCenterAddress(center).then(function(address) {
            this.$('.map-modal__autocomplete__input').val(address.formattedAddress);
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
        this.autocomplete && this.autocomplete.unbind();
        this.map.cleanLocation();

        this.stopListening();
        this.$('.map-modal').off('shown.bs.modal', this.curriedDialogShown);
        this.$('.map-modal').off('hide.bs.modal', this._dialogHide);

        this.trigger('map-dialog:hide', this.model.toJSON());
    },

    unbind: function() {
        this.map && this.map.unbind();
        this.autocomplete && this.autocomplete.unbind();
        this.stopListening();
        this.undelegateEvents();
    }
});

_.extend(VOLO.MapModalView.prototype, VOLO.TooltipAlignMixin);
