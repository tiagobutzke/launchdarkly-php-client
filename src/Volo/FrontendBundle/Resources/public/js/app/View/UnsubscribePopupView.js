VOLO.UnsubscribePopupView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this);
    },

    render: function() {
        this.$el.modal('show');

        return this;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});
