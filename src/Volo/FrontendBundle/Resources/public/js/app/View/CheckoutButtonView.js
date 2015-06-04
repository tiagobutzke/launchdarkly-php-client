var CheckoutButtonView = Backbone.View.extend({
    events: {
        "click #finish-and-pay": "placeOrder"
    },

    initialize: function () {
        _.bindAll(this);

        this.vendorCode = this.$el.data().vendor_code;
        console.log('is guest user', this.$el.data().is_guest_user);

        this.model.set('is_guest_user', this.$el.data().is_guest_user);

        this.listenTo(this.model, 'change:address_id', this.render, this);
        this.listenTo(this.model, 'change:credit_card_id', this.render, this);
        this.listenTo(this.model, 'change:adyen_encrypted_data', this.render, this);
        this.listenTo(this.model, 'change:cart_dirty', this.render, this);
        this.listenTo(this.model, 'change:placing_order', this.render, this);

        this.listenTo(this.model, 'payment:success', this.handlePaymentSuccess, this);
        this.listenTo(this.model, 'payment:error', this.handlePaymentError, this);
    },

    render: function () {
        console.log('CheckoutButtonView:render', this.model.isValid());

        if (this.model.isValid() && !this.model.get('cart_dirty') && !this.model.get('placing_order')) {
            this.$(".button").prop('disabled', '');
            this.$(".button").css({opacity: 1});
        } else {
            this.$(".button").prop('disabled', 'disabled');
            this.$(".button").css({opacity: 0.5});
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    placeOrder: function () {
        this.model.placeOrder(this.vendorCode);
        this.$('.error_msg').addClass('hide');
    },

    handlePaymentSuccess: function (data) {
        Turbolinks.visit(Routing.generate('checkout_success', {orderCode: data.code}));
    },

    handlePaymentError: function (data) {
        this.$('.error_msg').removeClass('hide');
        this.$('.error_msg').html(data.error.errors.developer_message);
    }
});
