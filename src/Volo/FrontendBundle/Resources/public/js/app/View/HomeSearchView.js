/**
 * model: LocationModel
 * options:
 * - geocodingService: GeocodingService
 */
var HomeSearchView = CTATrackableView.extend({
    initialize: function (options) {
        console.log('HomeSearchView.initialize ', this.cid);
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.geocodingService.init(this.$('#delivery-information-postal-index'), VOLO.configuration.address_config);

        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._applyNewLocationData);
        this.listenTo(this.geocodingService, 'autocomplete:not_found', this._notFound);

        this.postInit();
    },

    events: function() {
        return _.extend({}, CTATrackableView.prototype.events, {
            'click .home__teaser__button': '_submitPressed',
            'submit': '_submitPressed',
            'focus #delivery-information-postal-index': '_hideTooltip',
            'blur #delivery-information-postal-index': '_hideTooltip',
            'click #delivery-information-postal-index': '_scrollToInput',
            'keydown #delivery-information-postal-index': '_inputChanged'
        });
    },

    render: function() {
        var node = this.$('.home__teaser__form-input');
        var postcode = node.val();

        if (postcode !== '') {
            this.geocodingService.getLocationByZipCode(postcode);
        }

        if (!this.isIE() && postcode === '') {
            node.focus();
        }
    },

    unbind: function() {
        this.$('#delivery-information-postal-index').tooltip('destroy');
        this.geocodingService.removeListeners(this.$('#delivery-information-postal-index'));
        this.stopListening();
        this.undelegateEvents();
        this.$('#delivery-information-postal-index').val('');
    },

    postInit: function() {
        this.model.set(this.model.defaults);
    },

    _notFound: function() {
        var value = this.$('#delivery-information-postal-index').val() || '';
        if (value !== '') {
            console.log('not found');
            this._showInputPopup(this.$('#delivery-information-postal-index').data('msg_error_not_found'));
        }

        this.model.set(this.model.defaults);

        return false;
    },

    _scrollToInput: function() {
        var md = new MobileDetect(window.navigator.userAgent);
        if (md.mobile()) {
            $('html, body').animate({
                scrollTop: this.$('#delivery-information-postal-index').offset().top - ($('.header').height() + 10)
            }, VOLO.configuration.anchorScrollSpeed);
        }
    },

    _inputChanged: function() {
        if(this.model.get('formattedAddress') !== this.$('#delivery-information-postal-index').val()) {
            this.model.set(this.model.defaults);
        }

        this._hideTooltip();
    },

    _submitPressed: function() {
        console.log('_submitPressed ', this.cid);
        console.log(this.model.toJSON());
        if (this.model.get('postcode')) {
            this._afterSubmit();
        }

        if (this.$('#delivery-information-postal-index').val() === '') {
            this._showInputPopup(this.$('#delivery-information-postal-index').data('msg_error_empty'));
        }

        return false;
    },

    _afterSubmit: function() {
        var data = this.model.toJSON();
        Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
            city: data.city,
            address: data.address,
            longitude: data.longitude,
            latitude: data.latitude,
            postcode: data.postcode,
            street: data.street,
            building: data.building
        }));
    },

    _isFullAddressAutocomplete: function() {
        return VOLO.configuration.address_config.autocomplete_type[0] === 'address';
    },

    _applyNewLocationData: function (locationMeta) {
        var data = this._getDataFromMeta(locationMeta);

        this._hideTooltip();

        this.$('#delivery-information-postal-index').val(data.formattedAddress);

        if (locationMeta.postcodeGuessed) {
            console.log('locationMeta.postcodeGuessed ', locationMeta.postcodeGuessed);
            this._showInputPopup(this.$('#delivery-information-postal-index').data('msg_you_probably_mean'));
        }

        this.model.set({
            formattedAddress: data.formattedAddress,  // address in input field
            latitude: data.lat,
            longitude: data.lng,
            postcode: data.postcode,
            city: data.city,
            address: this._isFullAddressAutocomplete() ? data.formattedAddress : data.postcode + ", " + data.city,
            building: this._isFullAddressAutocomplete() ? data.building : null,
            street: this._isFullAddressAutocomplete() ? data.street : null
        });

        dataLayer.push({
            'zipcode': data.postcode,
            'city': data.city
        });
    },

    _getDataFromMeta: function (locationMeta) {
        var formattedAddress = locationMeta.formattedAddress;

        if (!formattedAddress.match(locationMeta.postalCode.value) && !this._isFullAddressAutocomplete()) {
            formattedAddress = locationMeta.postalCode.value + ", " + locationMeta.city;
        }

        return {
            formattedAddress: formattedAddress,
            postcode: locationMeta.postalCode.value,
            lat: locationMeta.lat,
            lng: locationMeta.lng,
            city: locationMeta.city,
            street: locationMeta.street,
            building: locationMeta.building,
        };
    },

    _showInputPopup: function (text) {
        var $postalIndexFormInput = this.$('#delivery-information-postal-index'),
            placement = this.isBelowMediumScreen() ? 'top' : 'bottom';

        if (!$postalIndexFormInput.hasClass('hide')) {
            console.log('_showInputPopup ', this.cid);
            $postalIndexFormInput.tooltip({
                placement: placement,
                html: true,
                trigger: 'manual',
                title: text,
                animation: false
            });
            this.tooltipAlignLeft($postalIndexFormInput);
            $postalIndexFormInput.tooltip('show');
        }
    },

    _hideTooltip: function () {
        console.log('_hideTooltip');
        this.$('#delivery-information-postal-index').tooltip('destroy');
    },

    _enableInputNode: function () {
        var $postalIndexFormInput = this.$('#delivery-information-postal-index');

        $postalIndexFormInput.css('opacity', '1').attr('disabled', false);
        if (!this.isIE()) {
            $postalIndexFormInput.focus();
        } else {
            this._hideTooltip();
        }
    },

    _disableInputNode: function () {
        this.$('#delivery-information-postal-index').css('opacity', '.4').attr('disabled', 'true');
    }
});

_.extend(HomeSearchView.prototype, VOLO.TooltipAlignMixin);
_.extend(HomeSearchView.prototype, VOLO.DetectScreenSizeMixin);
_.extend(HomeSearchView.prototype, VOLO.DetectIE);
