var CheckoutModel = Backbone.Model.extend({
    defaults: {
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
    }
});
