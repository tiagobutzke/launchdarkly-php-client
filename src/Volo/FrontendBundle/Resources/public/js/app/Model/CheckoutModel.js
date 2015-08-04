var CheckoutModel = Backbone.Model.extend({
    defaults: {
        placing_order: false,
        is_guest_user: false,
        cart_dirty: false,
        address_id: null,
        is_contact_information_valid: false,
        credit_card_id: null,
        is_credit_card_store_active: true,
        payment_type_id: null,
        payment_type_code: null,
        adyen_encrypted_data: null,
        subtotal_before_discount: 0
    },

    initialize: function (data, options) {
        _.bindAll(this);
        this.cartModel = options.cartModel;

        _.each(this.cartModel.vendorCarts.models, function (model) {
            this.listenTo(model, 'cart:dirty', function() {this.set('cart_dirty', true);}.bind(this));
            this.listenTo(model, 'cart:ready', function() {this.set('cart_dirty', false);}.bind(this));
            this.listenTo(model, 'cart:error', function() {this.set('cart_dirty', false);}.bind(this));
        }.bind(this));
    },

    isValid: function () {
        if (this.get('cart_dirty')) {
            return false;
        }

        if (_.isNull(this.get('payment_type_code'))) {
            return false;
        }

        if (this.get('is_guest_user')) {
            if (_.indexOf(['paypal', 'adyen_hpp'], this.get('payment_type_code')) != -1) {
                return true;
            }

            return !_.isNull(this.get('adyen_encrypted_data'));
        }

        if (_.isNull(this.get('address_id'))) {
            return false;
        }

        if (_.isNull(this.get('payment_type_id')) || _.isNull(this.get('payment_type_code'))) {
            return false;
        }

        if (this.get('payment_type_code') === 'adyen' && _.isNull(this.get('credit_card_id')) && _.isNull(this.get('adyen_encrypted_data'))) {
            return false;
        }

        return true;
    },

    canBeSubmitted: function() {
        return this.isValid() && !this.get('placing_order');
    },

    placeOrder: function (vendorCode, vendorId, customer, address, isSubscribedNewsletter) {
        var data = {
            expected_total_amount: this.cartModel.getCart(vendorId).get('total_value'),
            payment_type_id: this.get('payment_type_id'),
            customer: customer.toJSON(),
            address: address.toJSON(),
            isSubscribedNewsletter: isSubscribedNewsletter
        };

        this.trigger('payment:attempt_to_pay', {
            paymentMethod: this.get('payment_type_code')
        });

        if (this.get('payment_type_code') === 'adyen') {
            if (_.isNull(this.get('adyen_encrypted_data'))) {
                data.credit_card_id = this.get('credit_card_id');
            } else {
                data.encrypted_payment_data = this.get('adyen_encrypted_data');
            }
        }

        if (!this.get('is_guest_user')) {
            data.customer_address_id = this.get('address_id');
            data.is_credit_card_store_active = this.get('is_credit_card_store_active');
        }

        var requestSettings = {
            url: Routing.generate('checkout_place_order', {vendorCode: vendorCode}),
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data)
        };

        this.set('placing_order', true);

        var xhr = $.ajax(requestSettings);

        xhr.success(function (data) {
            console.log("payment success", data);
            this.trigger("payment:success", data);
        }.bind(this));

        xhr.fail(function (jqXHR) {
            console.log("fail", jqXHR.responseJSON);
            var eventData = jqXHR.responseJSON;
            eventData.paymentMethod = this.get('payment_type_code');
            this.trigger("payment:error", eventData);
            this.set('placing_order', false);
        }.bind(this));
    }
});
