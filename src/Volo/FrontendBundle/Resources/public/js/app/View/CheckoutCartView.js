var CheckoutCartItemView = CartItemView.extend({
    _editItem: $.noop
});

var CheckoutCartView = CartView.extend({
    initialize: function() {
        CartView.prototype.initialize.apply(this, arguments);

        this.CartItemViewClass = CheckoutCartItemView
    },

    render: function () {
        CartView.prototype.render.apply(this, arguments);

        this.$('.desktop-cart__time').hide();
        this.$('.btn-checkout').hide();
        this.$('.desktop-cart_order__message').hide();
        this.$('.vendor__geocoding__tool-box').hide();
        return this;
    },
 
    /**
     * @override
     * @private
     */
    renderTimePicker: $.noop,

    /**
     * @override
     * @private
     */
    _makeCartAndMenuSticky: $.noop,

    /**
     * @override
     * @private
     */
    handleVouchersErrors: $.noop,

    /**
     * @override
     * @private
     */
    renderCheckoutButton: $.noop
});

