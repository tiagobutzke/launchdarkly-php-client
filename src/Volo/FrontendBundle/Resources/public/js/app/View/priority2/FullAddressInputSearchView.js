VOLO = VOLO || {};
VOLO.FullAddressInputSearchView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);
        this.autocomplete = options.autocomplete || new VOLO.Geocoding.Autocomplete();
        this.appConfig = options.appConfig;
        this.tooltipPlacement = options.tooltipPlacement;
        this.places = options.places || new VOLO.Geocoding.Places(options.appConfig, document.createElement('div'));
        this.deserializer =  options.deserializer || new VOLO.Geocoding.PlaceDeserializer();
        this.geocoder = options.geocoder || new VOLO.Geocoding.Geocoder(this.appConfig);

        this.mapModalView = new VOLO.MapModalView({
            el: '.modal-dialogs',
            appConfig: this.appConfig
        });

        this.listenTo(this.autocomplete, 'autocomplete-search:place-found', this._placeFound);
        this.listenTo(this.autocomplete, 'autocomplete-search:place-without-location', this.submitGeocode);
    },

    events: {
        'keydown': '_handleKeyDown'
    },

    submitGeocode: function() {
        this.hideTooltip();
        this._parseUserAddress()
            .then(this._geocode)
            .then(this._parseGeocodedAddress, this._displayError);

        return false;
    },

    showInputMsg: function (text) {
        if (this.$el.hasClass('hide')) {
            return;
        }

        console.log('showInputMsg ', this.cid);
        this.$el.tooltip({
            placement: this._getTooltipPlacement(),
            html: true,
            trigger: 'manual',
            title: text,
            animation: false
        });
        this.tooltipAlignLeft(this.$el);
        this.$el.tooltip('show');
    },

    hideTooltip: function () {
        this.$el.tooltip('destroy');
    },

    _handleKeyDown: function(e) {
        if (e.keyCode === 9) { //tab
            this._geocode().then(this._updateInputFromAddress, this._displayError);
        } else if (e.keyCode !== 13 ) { //everything except enter and tab
            this.hideTooltip();
        } else { //enter
            this._parseUserAddress();
        }
    },

    _parseUserAddress: function() {
        var deferred = $.Deferred(),
            $input = this.$el,
            userAddress = this.$el.val();

        if (!userAddress) {
            this.showInputMsg($input.data('msg_error_empty'));
            deferred.reject();
        } else if (!this._isValidUserInput(userAddress, this.appConfig)) {
            this.showInputMsg($input.data('msg_error_postal_code'));
            deferred.reject();
        } else {
            deferred.resolve();
        }

        return deferred;
    },

    _parseGeocodedAddress: function(address) {
        var userAddress = this.$el.val();

        this.$el.val(address.formattedAddress);
        this._checkForStreetDuplicity(address, userAddress);
    },

    _isValidUserInput: function(value, appConfig) {
        var validAddressRegex = _.get(appConfig, 'address_config.valid_address_input'),
            trimmedValue = value.trim();

        return trimmedValue.match(validAddressRegex) !== null;
    },

    _displayError: function(error) {
        if (error === 'ZERO_RESULTS') {
            this.showInputMsg(this.$el.data('msg_error_not_found'));
        } else {
            console.log(error);
            trackJs.console.error('FullAddressInputSearchError:' + error);
        }
    },

    _geocode: function() {
        var firstResultValue = this.autocomplete.getFirstResultValue(),
            geocoder = this.geocoder,
            deferred = $.Deferred();

        geocoder.geocodeAddress(firstResultValue)
            .then(this._getAddressFromPlace, deferred.reject)
            .then(deferred.resolve);

        return deferred;
    },

    _getAddressFromPlace: function(place) {
        var address = this.deserializer.deserialize(place[0], this.appConfig),
            deferred = $.Deferred();

        return deferred.resolve(address);
    },

    _placeFound: function(geocodingPlace) {
        var address = this.deserializer.deserialize(geocodingPlace, this.appConfig);
        console.log('VOLO.homeSearch - autocomplete address', address);

        this._checkForStreetDuplicity(address, this.$el.val());
    },

    _checkForStreetDuplicity: function (address, userAddress) {
        var locationModel = new VOLO.FullAddressLocationModel(address, this.appConfig);

        this.places.isAddressUnique(userAddress, address).then(function(isUnique) {
            address.isUnique = isUnique;

            if (isUnique && locationModel.isValid()) {
                this._submitInput(address);
            } else {
                this._openMapModalDialog(address, userAddress);
            }
        }.bind(this), function(e) {
            console.log('Places error:', e);
            this._openMapModalDialog(address, userAddress);
        }.bind(this));
    },

    _updateInputFromAddress: function(address) {
        this.$el.val(address.formattedAddress);
    },

    _openMapModalDialog: function(address, userAddress) {
        this.autocomplete.unbind();
        this.mapModalView.show(address);
        this.trigger('home-search-view:gtm-open-map', {
            event: 'openMap',
            userAddress: userAddress,
            address: address.formattedAddress
        });

        this.listenToOnce(this.mapModalView, 'map-dialog:hide', this._dialogHide);
        this.listenToOnce(this.mapModalView, 'map-dialog:address-submit', this._submitMap);
    },

    _dialogHide: function(address) {
        this.$el.val(address.formattedAddress);
        this.stopListening(this.mapModalView, 'map-dialog:address-submit');
        this._startAutocomplete();
    },

    _getTooltipPlacement: function() {
        if (this.tooltipPlacement) {
            return this.tooltipPlacement;
        } else {
            return this.isBelowMediumScreen() ? 'top' : 'bottom';
        }
    },

    render: function() {
        this._startAutocomplete();
        this.mapModalView.render();

        var $input = this.$el;
        $input.val(this.model.get('address'));
        if (!this.isIE() && !$input.val()) {
            $input.focus();
        }

        return this;
    },

    _startAutocomplete: function() {
        this.autocomplete.init(this.$el, this.appConfig);
    },

    _submitMap: function(address) {
        this.trigger('home-search-view:gtm-submit', {
            event: 'submitMap',
            fullAddress: address.formattedAddress
        });
        this._submitAddress(address);
    },

    _submitInput: function(address) {
        this.trigger('home-search-view:gtm-submit', {
            event: 'submitInput',
            fullAddress: address.formattedAddress
        });
        this._submitAddress(address);
    },

    _submitAddress: function(address) {
        this.$el.val(address.formattedAddress);
        this.trigger('full-address-search:submit', address);
    },

    unbind: function() {
        this.autocomplete.unbind();
        this.mapModalView && this.mapModalView.unbind();
        this.mapModalView && this.mapModalView.hide();

        this.stopListening();
        this.undelegateEvents();
    }
});

_.extend(VOLO.FullAddressInputSearchView.prototype, VOLO.TooltipAlignMixin);
_.extend(VOLO.FullAddressInputSearchView.prototype, VOLO.DetectScreenSizeMixin);
_.extend(VOLO.FullAddressInputSearchView.prototype, VOLO.DetectIE);
