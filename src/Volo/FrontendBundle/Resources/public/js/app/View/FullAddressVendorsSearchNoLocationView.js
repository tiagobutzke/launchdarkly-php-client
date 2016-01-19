VOLO = VOLO || {};

VOLO.FullAddressVendorsSearchNoLocationView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.fullAddressSearchView = new VOLO.FullAddressInputSearchView({
            model: this.model,
            el: this.$('.restaurants-search-form__input'),
            appConfig: options.appConfig,
            tooltipPlacement: 'bottom'
        });

        this.domObjects = {};
        this.domObjects.$window = options.$window;

        this.domObjects.$window.off('scroll', this._hidePlaceSuggestions).on('scroll', this._hidePlaceSuggestions);
        this.domObjects.$window.off('resize', this._hidePlaceSuggestions).on('resize', this._hidePlaceSuggestions);

        this.listenTo(this.fullAddressSearchView, 'full-address-search:submit', this._submitAddress);
    },

    events: {
        'submit .restaurants-search-form': function() {
            return false;
        },
        'click .restaurants-search-form__button': '_submit'
    },

    _submit: function() {
        this.fullAddressSearchView.submitGeocode();

        return false;
    },

    _submitAddress: function(address) {
        this.model.set(address);
        VOLO.vendorsRoute.navigateToVendorsList(address);
    },

    render: function() {
        this.fullAddressSearchView.render();

        return this;
    },

    unbind: function() {
        this.domObjects.$window.off('scroll', this._hidePlaceSuggestions);
        this.domObjects.$window.off('resize', this._hidePlaceSuggestions);
        this.domObjects = {};

        this.stopListening();
        this.fullAddressSearchView.unbind();
    },

    _hidePlaceSuggestions: function() {
        if (this.$el.hasClass('sticking-on-top')) {
            $('.pac-container').hide();
        }
    }
});
