VOLO = VOLO || {};
VOLO.FullAddressVendorSearchView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.fullAddressSearchView = new VOLO.FullAddressInputSearchView({
            model: this.model,
            el: this.$('.restaurants-search-form__input'),
            appConfig: options.appConfig,
            tooltipPlacement: 'bottom'
        });

        this.appConfig = options.appConfig;
        this.modelCart = options.modelCart;
        this.domObjects = {};
        this.domObjects.$window = options.$window;

        this.listenTo(this.modelCart, 'invalid', this._alarmNoPostcode);
        this.listenTo(this.fullAddressSearchView, 'full-address-search:submit', this._performDeliverableCheck);

        this.domObjects.$window.off('scroll resize', this._hidePlaceSuggestions).on('scroll resize', this._hidePlaceSuggestions);
        this._initPostalCodeSelectionSubview();
    },

    events: {
        'submit': function() {
            return false;
        },
        'focus .restaurants-search-form__input': '_hideTooltip',
        'click .postal-index-form-overlay': '_hideTooltip',
        'click .restaurants-search-form__button': '_submitAddress',
        'click *[data-gtm-cta]': '_ctaClicked'
    },

    render: function() {
        this.fullAddressSearchView.render();

        if (!this.model.get('latitude')) {
            this.fullAddressSearchView.showInputMsg(this.$('.restaurants-search-form__input').data('msg_error_vendor_empty_address'));
        } else {
            this._performDeliverableCheck(this.model.toJSON());
        }

        return this;
    },

    _submitAddress: function() {
        this.fullAddressSearchView.submitGeocode();
    },

    unbind: function() {
        this.fullAddressSearchView.unbind();
        this.postalCodeSelectionSubview && this.postalCodeSelectionSubview.unbind();

        this.stopListening();
    },

    _performDeliverableCheck: function(address) {
        this.model.set(address);
        this.modelCart.updateLocationIfDeliverable(address)
            .then(this._parseIsDeliverable, this._onSearchFail);
    },

    _onSearchFail: function () {
        this._showInputPopup(_.template($('#template-vendor-is-deliverable-server-error').html()));
    },

    _initPostalCodeSelectionSubview: function() {
        this.postalCodeSelectionSubview = new VOLO.PostalCodeStickingOnTopView({
            el: this.$el,
            $window: this.domObjects.$window,
            $container: $('.hero-banner-wrapper'),
            $header: $('.header')
        });
    },

    _hidePlaceSuggestions: function() {
        var isSticky = $('.hero-banner-wrapper.sticking-on-top').length > 0;
        isSticky && $('.pac-container').hide();
    },

    _alarmNoPostcode: function (event) {
        var offset, postalCodeBarOffset, $postalCodeBar;

        if (event.validationError === 'location_not_set') {
            this._showAddressForm();
            $postalCodeBar = $('.menu__postal-code-bar');
            offset = $postalCodeBar.length ? $postalCodeBar.offset().top - $('.header__wrapper').height() : 0;

            $('html, body').animate({
                scrollTop: offset
            }, this.appConfig.anchorScrollSpeed, function() {
                this._showInputPopupAndBackground();
            }.bind(this));
        }
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

    _hideTooltip: function() {
        $('.hero-banner-wrapper').removeClass('postal-index-form-overlay--shown');
        this.fullAddressSearchView.hideTooltip();
    },

    _showInputPopupAndBackground: function() {
        $('.hero-banner-wrapper').addClass('postal-index-form-overlay--shown');
        this.fullAddressSearchView.showInputMsg(this.$('.restaurants-search-form__input').data('msg_error_vendor_empty_address'));
    },

    _parseIsDeliverable: function (response) {
        var data = this.model.toJSON(),
            url, template;
        if (response.result) { // is deliverable
            $('.location__address').html(this._formatDeliveryLocation());
            this.model.save();
            this._hideAddressForm();
            this.fullAddressSearchView.mapModalView.hide();
        } else {
            url = VOLO.vendorsRoute.getVendorsRoute(data);
            template = _.template($('#template-vendor-menu-nothing-found').html());

            if (this.fullAddressSearchView.mapModalView.isOpen()) {
                this.fullAddressSearchView.mapModalView.hide();
            }

            this.fullAddressSearchView.showInputMsg(template({url: url}));
        }
    },

    _hideAddressForm: function() {
        $('.menu-wrapper').removeClass('menu__blocks--address-form-visible');
        $('.vendor__geocoding__tool-box__title').removeClass('hide');
        $('.hero-menu__info-extra').removeClass('hidden-to-user');

        this.$el.addClass('hide');

        this.postalCodeSelectionSubview && this.postalCodeSelectionSubview.updateStickOnTopCoordinates();
        this.trigger('vendor_geocoding_view:postcode_toggle');
    },

    _formatDeliveryLocation: function() {
        var format = _.get(VOLO, 'configuration.address_config.format');

        return format.replace(':building', this.model.get('building'))
            .replace(':street', this.model.get('street'))
            .replace(':plz', this.model.get('postcode'));
    }
});
