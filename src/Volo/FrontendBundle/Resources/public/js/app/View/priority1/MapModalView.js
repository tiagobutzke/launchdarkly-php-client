VOLO.MapModalView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.template = _.template($('#template-map-modal-dialog').html());
        this.appConfig = options.appConfig;
        this.deserializer = new VOLO.Geocoding.PlaceDeserializer();
    },

    events: {
        'submit .map-modal__autocomplete': '_updatePositionFromInput',
        'submit .map-modal__submit': '_submit'
    },

    _updatePositionFromInput: function() {
        var firstResultValue = this.autocomplete.getFirstResultValue(),
            geocoder = new VOLO.Geocoding.Geocoder();

        if (this.$('.map-modal__autocomplete__input').val()) {
            geocoder.geocodeAddress(firstResultValue).then(this._centerMapOnFirstAddress, function(error) {
                console.log('submit error', error);
            }.bind(this));
        } else {
            console.log('empty input');
        }
        return false;
    },

    _centerMapOnFirstAddress: function(addresses) {
        var location = addresses[0].geometry.location;

        this._updateAddress(this.deserializer.deserialize(addresses[0]));
        this.map.setCenter(location.lat(), location.lng());
    },

    _submit: function() {
        this.trigger('map-dialog:address-submit', this.address);
        this._disableSubmitButton();

        return false;
    },

    _showInputError: function (text) {
        var $input = this.$('.map-modal__autocomplete__input');

        $input.tooltip({
            placement: 'bottom',
            html: true,
            trigger: 'manual',
            title: text,
            animation: false
        });

        this.tooltipAlignLeft($input);

        $input.tooltip('show');
    },

    render: function() {
        this._enableSubmitButton();

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
        this.$('.map-modal__autocomplete__input').val(address.formattedAddress);
        this.address = address;
    },

    _centerChanged: function(center) {
        this.$('.map-modal__autocomplete__input').tooltip('destroy');
        this.cursorMoved = true;

        this._getCenterAddress(center).then(function(address) {
            this.$('.map-modal__autocomplete__input').val(address.formattedAddress);
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
        this.autocomplete.unbind();
        this.map.cleanLocation();

        this.stopListening();
        this.$('.map-modal').off('shown.bs.modal', this.curriedDialogShown);
        this.$('.map-modal').off('hide.bs.modal', this._dialogHide);

        this.trigger('map-dialog:hide', this.address);
    },

    unbind: function() {
        this.map && this.map.unbind();
        this.autocomplete && this.autocomplete.unbind();
    }
});

_.extend(VOLO.MapModalView.prototype, VOLO.TooltipAlignMixin);
