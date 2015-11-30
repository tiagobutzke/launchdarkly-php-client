VOLO = VOLO || {};
VOLO.FullAddressHomeSearchView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.fullAddressSearchView = new VOLO.FullAddressInputSearchView({
            model: this.model,
            el: this.$('.restaurants-search-form__input'),
            appConfig: options.appConfig
        });
        this.listenTo(this.fullAddressSearchView, 'full-address-search:submit', this._submitAddress);
    },

    events: function() {
        return {
            'click .restaurants-search-form__button': this._submit,
            'submit': function() {
                return false;
            },
            'click *[data-gtm-cta]': '_ctaClicked'
        };
    },

    _submit: function() {
        this.fullAddressSearchView.submitGeocode();

        return false;
    },

    render: function() {
        this.fullAddressSearchView.render();

        return this;
    },

    _submitAddress: function(address) {
        this.model.set(address);
        VOLO.vendorsRoute.navigateToVendorsList(address);
    },

    unbind: function() {
        this.fullAddressSearchView.unbind();
        this.stopListening();
    }
});

_.extend(VOLO.FullAddressHomeSearchView.prototype, VOLO.CTAActionMixin);
