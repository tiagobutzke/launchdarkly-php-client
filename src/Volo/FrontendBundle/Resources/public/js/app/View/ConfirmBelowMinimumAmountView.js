var ConfirmBelowMinimumAmountView = Backbone.View.extend({
    initialize: function() {
        console.log('ConfirmBelowMinimumAmountView.initialize ', this.cid);
        _.bindAll(this);
        this.templateConfirmBelowMinimumAmount = _.template($('#template-confirm-below-minimum-amount').html());
    },

    events: {
        'click .allow-below-minimum-amount-checkout': '_goToCheckoutWithBelowMinimumAmount'
    },

    render: function() {
        var cacheVendorCartData = this.model.toJSON(),
        cacheModalContent = this.templateConfirmBelowMinimumAmount(cacheVendorCartData);
        cacheModalContent = cacheModalContent
            .replace("%subtotal%", VOLO.formatCurrency(cacheVendorCartData.subtotal))
            .replace("%minimum_order_amount%", VOLO.formatCurrency(cacheVendorCartData.minimum_order_amount))
            .replace("%vendor_name%", $('.hero-menu__info__headline').text());

        $('.modal-confirm-below-minimum-amount').html(cacheModalContent);
        $('.confirm-below-minimum-amount-modal').modal();

        return this;
    },

    _goToCheckoutWithBelowMinimumAmount: function() {
        $('.confirm-below-minimum-amount-modal').modal('hide');
        this.trigger('confirm_below_minimum_amount:allow_below_minimum_amount_checkout');
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});
