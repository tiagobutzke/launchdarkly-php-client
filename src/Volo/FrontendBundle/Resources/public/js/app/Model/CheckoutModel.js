var CheckoutModel = Backbone.Model.extend({
    defaults: {
        "placing_order": false,
        "is_guest_user": false,
        "cart_dirty": false,
        "address_id": null,
        "credit_card_id": null,
        "adyen_encrypted_data": null,
        "subtotal_before_discount": 0
    },

    initialize: function (data, options) {
        this.carts = options.cartModel.vendorCarts.models;
        _.bindAll(this);

        _.each(this.carts, function (model) {
            this.listenTo(model, 'cart:dirty', function() {this.set('cart_dirty', true);}.bind(this));
            this.listenTo(model, 'cart:ready', function() {this.set('cart_dirty', false);}.bind(this));
            this.listenTo(model, 'cart:error', function() {this.set('cart_dirty', false);}.bind(this));
        }.bind(this));
    },

    isValid: function () {
        if (this.get('cart_dirty')) {
            return false;
        }

        if (this.get('is_guest_user')) {
            return !_.isNull(this.get('adyen_encrypted_data'));

        }

        if (_.isNull(this.get('address_id'))) {
            return false;
        }
        if (_.isNull(this.get('credit_card_id')) && _.isNull(this.get('adyen_encrypted_data'))) {
            return false;
        }

        return true;
    },

    placeOrder: function (vendorCode) {
        var data = {};
        if (_.isNull(this.get('adyen_encrypted_data'))) {
            data.credit_card_id = this.get('credit_card_id');
        } else {
            data.encrypted_payment_data = this.get('adyen_encrypted_data');
        }

        if (!this.get('is_guest_user')) {
            data.customer_address_id = this.get('address_id');
        }

        var requestSettings = {
            url: Routing.generate('checkout_place_order', {vendorCode: vendorCode}),
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data)
        };

        this.set('placing_order', true);

        var xhr = $.ajax(requestSettings);
        xhr.always(function () {
            this.set('placing_order', false);
        }.bind(this));

        xhr.success(function (data) {
            console.log("payment success", data);
            this.trigger("payment:success", data);
        }.bind(this));

        xhr.fail(function (jqXHR) {
            console.log("fail", jqXHR.responseJSON);
            this.trigger("payment:error", jqXHR.responseJSON);
        }.bind(this));
    }
});
