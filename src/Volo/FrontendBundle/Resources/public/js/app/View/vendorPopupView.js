VOLO.VendorPopupView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this);
    },

    render: function() {
        var source = this.$el.data('utm-source'),
            $title = this.$('.home__titles__main-title'),
            $subtitle = this.$('.home__titles__subtitle');

        $title.html($title.data(source));
        $subtitle.html($subtitle.data(source));

        this.$el.modal('show');

        return this;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});
