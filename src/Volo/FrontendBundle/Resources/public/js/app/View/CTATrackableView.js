var CTATrackableView = Backbone.View.extend({
    initialize: function (options) {
        console.log('HomeView.initialize ', this.cid);
        _.bindAll(this);
    },

    events: {
        'click [data-gtm-cta]': '_ctaClicked'
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    },

    _ctaClicked: function (Event) {
        this.trigger('ctaTrackable:ctaClicked', {
            'name': $(Event.currentTarget).data('gtm-cta')
        });
    }
});
