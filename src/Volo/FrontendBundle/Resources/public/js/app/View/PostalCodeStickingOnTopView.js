var VOLO = VOLO || {};

VOLO.PostalCodeStickingOnTopView = Backbone.View.extend({
    initialize: function(options) {
        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.domObjects.$container = options.$container;
        this.domObjects.$window = options.$window;

        this.stickOnTopSearch = new StickOnTop({
            $container: this.domObjects.$container,
            stickOnTopValueGetter: function() {
                var stickOnTopValue = this.domObjects.$header.outerHeight();

                if ($('body').hasClass('show-ios-smart-banner')) {
                    stickOnTopValue += $('.ios-smart-banner').outerHeight();
                }

                return stickOnTopValue;
            }.bind(this),
            startingPointGetter: function() {
                return this.$el.offset().top;
            }.bind(this)
        });
        this.stickOnTopSearch.init(this.$el);
    },

    updateStickOnTopCoordinates: function() {
        this.stickOnTopSearch.updateCoordinates();
    },

    unbind: function() {
        this.domObjects = null;
        this.stickOnTopSearch.remove();
    }
});
