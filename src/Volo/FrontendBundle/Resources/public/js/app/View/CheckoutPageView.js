var CheckoutPageView = Backbone.View.extend({
    events: {
        "click #finish-and-pay": "placeOrder"
    },

    initialize: function (options) {
        _.bindAll(this);

        this.vendorCode = this.$el.data().vendor_code;
        this.vendorId = this.$el.data().vendor_id;

        this.spinner = new Spinner();
        this.domObjects = {};
        this.domObjects.$header = options.$header;

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
        console.log('CheckoutPageView:render', this.model.isValid());

        if (this.model.isValid() && !this.model.get('cart_dirty') && !this.model.get('placing_order')) {
            this.$("#finish-and-pay").prop('disabled', '');
            this.$("#finish-and-pay").css({opacity: 1});
        } else {
            this.$("#finish-and-pay").prop('disabled', 'disabled');
            this.$("#finish-and-pay").css({opacity: 0.5});
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    placeOrder: function () {
        var paddingFromHeader = 16,
            scrollToOffset;

        if (this.$('#delivery_information_form_button').is(':visible')) {
            this.$('#error_msg_delivery_not_saved').removeClass('hide');
            scrollToOffset = this.$('#checkout-delivery-information-address').offset().top -
                            paddingFromHeader -
                            this.domObjects.$header.outerHeight();
            $('html, body').animate({
                scrollTop: scrollToOffset
            }, VOLO.configuration.anchorScrollSpeed);

            return;
        }
        this.$('#error_msg_delivery_not_saved').addClass('hide');
        this.spinner.spin(this.$('#finish-and-pay')[0]);
        this.model.placeOrder(this.vendorCode, this.vendorId);
        this.$('.error_msg').addClass('hide');
    },

    handlePaymentSuccess: function (data) {
        if (_.isNull(data.hosted_payment_page_redirect)) {
            this.model.cartModel.emptyCart(this.vendorId);
            Turbolinks.visit(Routing.generate('order_tracking', {orderCode: data.code}));
        } else {
            if (data.hosted_payment_page_redirect.method === 'post') { // adyen hpp
                var params = data.hosted_payment_page_redirect.parameters,
                    url = "https://" + window.location.hostname + Routing.generate('handle_payment', {'orderCode': data.code});

                params.countryCode = VOLO.configuration.countryCode.toUpperCase();
                params.resURL = url;
                this.redirectPost(data.hosted_payment_page_redirect.url, params);
            } else {
                window.location.replace(data.hosted_payment_page_redirect.url); // paypal
            }
        }
        this.spinner.stop();
    },

    handlePaymentError: function (data) {
        this.$('.error_msg').removeClass('hide');

        if (_.isObject(data) && _.isString(data.error.errors.message)) {
            this.$('.error_msg').html(data.error.errors.message);
        }
        this.spinner.stop();
    },

    redirectPost: function (location, args) {
        var compiled = _.template(this.$('#template__form__redirect').html()),
            view = compiled({location: location, args: args});

        this.$el.append(view);
        this.$('#form__redirect').submit();
    }
});
