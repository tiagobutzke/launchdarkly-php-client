var VOLO = VOLO || {};

VOLO.LogoutLinkView = Backbone.View.extend({
    initialize: function() {
        console.log('LogoutLinkView.initialize ', this.cid);
        _.bindAll(this);
    },

    events: {
        'click': '_clearCarts'
    },

    _clearCarts: function() {
        if (this.model) {
            this.model.vendorCarts.reset();
        }
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});
