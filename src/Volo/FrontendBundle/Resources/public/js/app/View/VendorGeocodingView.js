/**
 * model: LocationModel
 * options:
 * - modelCart: VendorCartModel
 */
var VendorGeocodingView = HomeSearchView.extend({
    initialize: function (options) {
        _.bindAll(this);
        console.log('VendorGeocodingView:init', this.model);

        this.modelCart = options.modelCart;
        this.smallScreenMaxSize = options.smallScreenMaxSize;

        this.domObjects = {};
        this.domObjects.$window = options.$window;

        this.listenTo(this.modelCart, 'invalid', this._alarmNoPostcode);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this.performDeliverableCheck);

        this.domObjects.$window.off('scroll resize', this._hidePlaceSuggestions).on('scroll resize', this._hidePlaceSuggestions);

        this._initPostalCodeSelectionSubview();
        this.performDeliverableCheck();

        HomeSearchView.prototype.initialize.apply(this, arguments);
    },

    events: {
        'submit': '_submitPressed',
        'focus #delivery-information-postal-index': '_hideTooltip',
        'click .postal-index-form-overlay': '_hideTooltip',
        'click #delivery-information-postal-index': '_scrollToInput',
        'keyup #delivery-information-postal-index': '_inputChanged'
    },

    _hidePlaceSuggestions: function() {
        var isSticky = !!$('.hero-banner-wrapper.sticking-on-top').length;
        isSticky && $('.pac-container').hide();
    },

    _initPostalCodeSelectionSubview: function() {
        this.postalCodeSelectionSubview = new VOLO.PostalCodeStickingOnTopView({
            el: this.$el,
            $window: this.domObjects.$window,
            $container: $('.hero-banner-wrapper'),
            $header: $('.header')
        });
    },

    /**
     * @override
     */
    postInit: function() {
        if (this.model.isValid()) {
            this.modelCart.set('location', {
                location_type: this.model.defaults.location_type,
                latitude:  this.model.get('latitude'),
                longitude: this.model.get('longitude')
            });
        }
    },

    unbind: function() {
        this.domObjects.$window && this.domObjects.$window.off('scroll resize', this._hidePlaceSuggestions);
        this.domObjects = {};

        this.stopListening();
        this.postalCodeSelectionSubview && this.postalCodeSelectionSubview.unbind();

        HomeSearchView.prototype.unbind.apply(this, arguments);
    },

    performDeliverableCheck: function () {
        console.log('performDeliverableCheck ', this.cid);
        if (this.model.isValid()) {
            this.modelCart.updateLocationIfDeliverable(this.model.toJSON())
                .done(this._parseIsDeliverable)
                .fail(this.onSearchFail);
        } else {
            this.onSearchFail();
        }
    },

    setZipCode: function(zipCode) {
        this._showAddressForm();
        this.geocodingService.getLocationByZipCode(zipCode);
    },

    onSearchFail: function () {
        this._showInputPopup(_.template($('#template-vendor-is-deliverable-server-error').html()));
        this._enableInputNode();
    },

    _applyNewLocationData: function (locationMeta) {
        HomeSearchView.prototype._applyNewLocationData.apply(this, arguments);
        this._submitPressed();
    },

    _showInputPopupAndBackground: function () {
        $('.hero-banner-wrapper').addClass('postal-index-form-overlay--shown');
        HomeSearchView.prototype._showInputPopup.apply(this, arguments);
    },

    /**
     * @override
     */
    _hideTooltip: function () {
        $('.hero-banner-wrapper').removeClass('postal-index-form-overlay--shown');
        HomeSearchView.prototype._hideTooltip.apply(this, arguments);
    },

    _showAddressForm: function() {
        $('.menu-wrapper').addClass('menu__blocks--address-form-visible');
        $('.vendor__geocoding__tool-box__title').addClass('hide');
        $('.hero-menu__info-extra').addClass('hidden-to-user');

        this.$el.removeClass('hide');

        this._hideTooltip();
        this.postalCodeSelectionSubview && this.postalCodeSelectionSubview.updateStickOnTopCoordinates();
        this.trigger('vendor_geocoding_view:postcode_toggle');
    },

    _hideAddressForm: function() {
        $('.menu-wrapper').removeClass('menu__blocks--address-form-visible');
        $('.vendor__geocoding__tool-box__title').removeClass('hide');
        $('.hero-menu__info-extra').removeClass('hidden-to-user');

        this.$el.addClass('hide');

        this.postalCodeSelectionSubview && this.postalCodeSelectionSubview.updateStickOnTopCoordinates();
        this.trigger('vendor_geocoding_view:postcode_toggle');
    },

    /**
     * @override
     * @private
     */
    _afterSubmit: function() {
        this._disableInputNode();
        this.performDeliverableCheck();
    },

    _alarmNoPostcode: function (event) {
        var offset;

        if (event.validationError === 'location_not_set') {
            this._showAddressForm();
            offset = $('.menu__postal-code-bar').offset().top - $('.header__wrapper').height();
            $('html, body').animate({
                scrollTop: offset
            }, VOLO.configuration.anchorScrollSpeed, function() {
                this._showInputPopupAndBackground(_.template($('#template-vendor-supply-postcode').html()));
            }.bind(this));
        }
    },

    _prepareDeliveringToLabel: function () {
        if (VOLO.isFullAddressAutoComplete()) {
            return this.model.get('building') + " " + this.model.get('street') + ", " + this.model.get('postcode');
        }

        return this.model.get('postcode');
    },

    _parseIsDeliverable: function (response) {
        var data = this.model.toJSON();
        if (response.result) { // is deliverable
            $('.location__address').html(this._prepareDeliveringToLabel());
            this._hideAddressForm();
            this.model.save();
        } else {
            var url = Routing.generate('volo_location_search_vendors_by_gps', {
                city: data.city,
                latitude: data.latitude,
                longitude: data.longitude,
                address: data.postcode + ", " + data.city,
                postcode: data.postcode
            });

            var template = _.template($('#template-vendor-menu-nothing-found').html());
            this._enableInputNode();
            this._showAddressForm();
            this._showInputPopup(template({url: url}));
        }
    }
});
