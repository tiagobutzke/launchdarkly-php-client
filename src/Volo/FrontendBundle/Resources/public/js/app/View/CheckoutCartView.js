var CheckoutCartView = CartView.extend({
    render: function () {
        CartView.prototype.render.apply(this, arguments);

        this.$('.desktop-cart__time').hide();
        this.$('.btn-checkout').hide();
        this.$('.desktop-cart_order__message').hide();
        return this;
    },

    renderTimePicker: function () {

    },

    _makeCartAndMenuSticky: function () {

    }
});
