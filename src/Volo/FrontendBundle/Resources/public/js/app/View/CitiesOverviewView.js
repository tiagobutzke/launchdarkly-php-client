var VOLO = VOLO || {};

VOLO.CitiesOverviewView = Backbone.View.extend({
    events: {
        'click *[data-gtm-cta]': '_ctaClicked'
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});

_.extend(VOLO.CitiesOverviewView.prototype, VOLO.CTAActionMixin);
