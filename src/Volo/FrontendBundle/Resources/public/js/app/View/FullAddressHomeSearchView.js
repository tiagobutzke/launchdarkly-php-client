VOLO.FullAddressHomeSearchView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);
        this.autocomplete = new VOLO.Geocoding.Autocomplete();
        this.appConfig = options.appConfig;

        this.mapModalView = new VOLO.MapModalView({
            el: '.modal-dialogs',
            appConfig: this.appConfig
        });

        this.listenTo(this.autocomplete, 'autocomplete-search:place-found', this._placeFound);
    },

    events: function() {
        return _.extend({}, CTATrackableView.prototype.events, {
            'click .restaurants-search-form__button': _.debounce(this._submitGeocode, 200, {leading: false}),
            'keydown .restaurants-search-form__input': '_handleKeyDown',
            'submit': function() {
                return false;
            }
        });
    },

    _submitGeocode: function() {
        var $input = this.$('.restaurants-search-form__input');

        this._hideTooltip();
        if ($input.val()) {
            this._geocode().then(function(address) {
                this.autocomplete.unbind();
                $input.val(address.formattedAddress);
                this._openMapModalDialog(address);
            }.bind(this), this._displayError);
        } else {
            this._showInputMsg($input.data('msg_error_empty'));
        }

        return false;
    },

    _displayError: function(error) {
        if (error === 'ZERO_RESULTS') {
            this._showInputMsg(this.$('.restaurants-search-form__input').data('msg_error_not_found'));
        } else {
            console.log(error);
        }
    },

    _geocode: function() {
        var firstResultValue = this.autocomplete.getFirstResultValue(),
            geocoder = new VOLO.Geocoding.Geocoder(),
            deferred = $.Deferred();

        geocoder.geocodeAddress(firstResultValue)
            .then(this._getAddressFromPlace, deferred.reject)
            .then(deferred.resolve);

        return deferred;
    },

    _getAddressFromPlace: function(place) {
        var deserializer = new VOLO.Geocoding.PlaceDeserializer(),
            address = deserializer.deserialize(place[0], this.appConfig);

        console.log('VOLO.homeSearch - geocoder address', address);

        return address;
    },

    _placeFound: function(geocodingPlace) {
        var deserializer = new VOLO.Geocoding.PlaceDeserializer(),
            address = deserializer.deserialize(geocodingPlace, this.appConfig);

        this.autocomplete.unbind();
        this._openMapModalDialog(address);

        console.log('VOLO.homeSearch - autocomplete address', address);
    },

    _handleKeyDown: function(e) {
        this._hideTooltip();

        if(e.keyCode === 9) {
            this._geocode().then(this._updateInputFromAddress, this._displayError);
        }
    },

    _updateInputFromAddress: function(address) {
        this.$('.restaurants-search-form__input').val(address.formattedAddress);
    },

    _hideTooltip: function () {
        this.$('.restaurants-search-form__input').tooltip('destroy');
    },

    _openMapModalDialog: function(address) {
        this.mapModalView.show(address);

        this.listenToOnce(this.mapModalView, 'map-dialog:hide', this._dialogHide);
        this.listenToOnce(this.mapModalView, 'map-dialog:address-submit', this._submitAddress);
    },

    _dialogHide: function(address) {
        this.$('.restaurants-search-form__input').val(address.formattedAddress);
        this._startAutocomplete();
    },

    _showInputMsg: function (text) {
        var $postalIndexFormInput = this.$('#delivery-information-postal-index');

        if (!$postalIndexFormInput.hasClass('hide')) {
            console.log('_showInputMsg ', this.cid);
            $postalIndexFormInput.tooltip({
                placement: this._getTooltipPlacement(),
                html: true,
                trigger: 'manual',
                title: text,
                animation: false
            });
            this.tooltipAlignLeft($postalIndexFormInput);
            $postalIndexFormInput.tooltip('show');
        }
    },

    _getTooltipPlacement: function() {
        return this.isBelowMediumScreen() ? 'top' : 'bottom';
    },

    render: function() {
        this._startAutocomplete();
        this.mapModalView.render();

        var $input = this.$('.restaurants-search-form__input');
        if (!this.isIE() && !$input.val()) {
            $input.focus();
        }

        return this;
    },

    _startAutocomplete: function() {
        var $node = this.$('.restaurants-search-form__input');
        this.autocomplete.init($node, this.appConfig);
    },

    _submitAddress: function(address) {
        this.model.set(address);
        this.$('.restaurants-search-form__input').val(address.formattedAddress);
        Turbolinks.visit(this._getVendorsRoute(address));
    },

    _getVendorsRoute: function(address) {
        return Routing.generate('volo_location_search_vendors_by_gps', {
            city: address.city,
            address: encodeURIComponent(address.address),
            longitude: address.longitude,
            latitude: address.latitude,
            postcode: address.postcode,
            street: encodeURIComponent(address.street),
            building: encodeURIComponent(address.building)
        });
    },

    unbind: function() {
        this.autocomplete.unbind();
        this.mapModalView && this.mapModalView.unbind();
        this.mapModalView && this.mapModalView.hide();

        this.stopListening();
        this.undelegateEvents();
    }
});

_.extend(VOLO.FullAddressHomeSearchView.prototype, VOLO.TooltipAlignMixin);
_.extend(VOLO.FullAddressHomeSearchView.prototype, VOLO.DetectScreenSizeMixin);
_.extend(VOLO.FullAddressHomeSearchView.prototype, VOLO.DetectIE);
