var VendorsSearchNoLocationView = HomeSearchView.extend({
    /**
     * @override
     */
    initialize: function (options) {
        _.bindAll(this);
        this.domObjects = {};
        this.domObjects.$window = options.$window;

        this.domObjects.$window.off('scroll', this._hidePlaceSuggestions).on('scroll', this._hidePlaceSuggestions);
        this.domObjects.$window.off('resize', this._hidePlaceSuggestions).on('resize', this._hidePlaceSuggestions);

        HomeSearchView.prototype.initialize.apply(this, arguments);
    },

    _hidePlaceSuggestions: function() {
        if (this.$el.hasClass('sticking-on-top')) {
            $('.pac-container').hide();
        }
    },

    /**
     * @override
     */
    _getTooltipPlacement: function() {
        return 'bottom';
    },

    /**
     * @override
     */
    unbind: function() {
        this.domObjects.$window.off('scroll', this._hidePlaceSuggestions);
        this.domObjects.$window.off('resize', this._hidePlaceSuggestions);
        this.domObjects = {};

        HomeSearchView.prototype.unbind.apply(this, arguments);
    },

    /**
     * @override
     */
    _scrollToInput: $.noop
});
