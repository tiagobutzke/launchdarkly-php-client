var HomeView = Backbone.View.extend({
    events: {
        'click *[data-gtm-cta]': '_ctaClicked'
    }
});

_.extend(HomeView.prototype, VOLO.CTAActionMixin);
