var CheckoutButtonView = Backbone.View.extend({
    events: {
        "click .checkout-button": this.placeOrder
    },

    initialize: function () {
        _.bindAll();

        console.log('is guest user', this.$el.data().is_guest_user);

        this.model.set('is_guest_user', this.$el.data().is_guest_user);

        this.listenTo(this.model, 'change:address_id', this.render, this);
        this.listenTo(this.model, 'change:credit_card_id', this.render, this);
        this.listenTo(this.model, 'change:adyen_encrypted_data', this.render, this);
        this.listenTo(this.model, 'change:cart_dirty', this.render, this);
    },

    render: function () {
        console.log('CheckoutButtonView:render', this.model.isValid());

        if (this.model.isValid() && !this.model.get('cart_dirty')) {
            this.$(".button").prop('disabled', '');
            this.$el.css({opacity: 1});
        } else {
            this.$(".button").prop('disabled', 'disabled');
            this.$el.css({opacity: 0.5});
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    placeOrder: function (event) {
        if (!this.model.isValid()) {
            event.preventDefault();
        }
    }
});
