var CheckoutButtonView = Backbone.View.extend({
    events: {
        "click #finish-and-pay": "placeOrder"
    },

    initialize: function () {
        _.bindAll(this);

        this.vendorCode = this.$el.data().vendor_code;
        this.vendorId = this.$el.data().vendor_id;

        this.spinner = new Spinner();

        console.log('is guest user', this.$el.data().is_guest_user);

        this.model.set('is_guest_user', this.$el.data().is_guest_user);

        this.listenTo(this.model, 'change:address_id', this.render, this);
        this.listenTo(this.model, 'change:credit_card_id', this.render, this);
        this.listenTo(this.model, 'change:adyen_encrypted_data', this.render, this);
        this.listenTo(this.model, 'change:payment_type_id', this.render, this);
        this.listenTo(this.model, 'change:payment_type_code', this.render, this);
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
        this.spinner.spin(this.$('#finish-and-pay')[0]);
        this.model.placeOrder(this.vendorCode, this.vendorId);
        this.$('.error_msg').addClass('hide');
    },

    handlePaymentSuccess: function (data) {
        if (_.isNull(data.hosted_payment_page_redirect)) {
            this.model.cartModel.emptyCart(this.vendorId);
            Turbolinks.visit(Routing.generate('order_tracking', {orderCode: data.code}));
        } else {
            window.location.replace(data.hosted_payment_page_redirect.url);
        }
        this.spinner.stop();
    },

    handlePaymentError: function (data) {
        this.$('.error_msg').removeClass('hide');

        if (_.isObject(data) && _.isString(data.error.errors.message)) {
            this.$('.error_msg').html(data.error.errors.message);
        }
        this.spinner.stop();
    }
});
