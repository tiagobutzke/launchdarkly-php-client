var ConfirmBelowMinimumAmountView = Backbone.View.extend({
    initialize: function (options) {
        this.gtmService = options.gtmService;
        console.log('ConfirmBelowMinimumAmountView.initialize ', this.cid);
        _.bindAll(this);
        this.templateConfirmBelowMinimumAmount = _.template($('#template-confirm-below-minimum-amount').html());
    },

    events: {
        'click .allow-below-minimum-amount-checkout': '_goToCheckoutWithBelowMinimumAmount',
        'click .button-secondary': '_trackCancellation',
        'click .modal-close-button': '_trackCancellation' ,
        'click .confirm-below-minimum-amount-modal': '_trackModalClick'
    },

    render: function () {
        var cacheVendorCartData = this.model.toJSON(),
            cacheModalContent = this.templateConfirmBelowMinimumAmount(cacheVendorCartData);
        cacheModalContent = cacheModalContent
            .replace("%subtotal%", VOLO.formatCurrency(cacheVendorCartData.subtotal))
            .replace("%minimum_order_amount%", VOLO.formatCurrency(cacheVendorCartData.minimum_order_amount))
            .replace("%vendor_name%", $('.hero-menu__info__headline').text());

        this.$el.html(cacheModalContent);
        this.$('.confirm-below-minimum-amount-modal').modal();
        $('body').keydown(this._trackEscapeKey);

        return this;
    },

    _trackEscapeKey: function (event) {
        if (event.keyCode === 27) {
            this._trackCancellation();
        }
    },

    _unbindKeyUp: function () {
        $('body').unbind('keydown', this._trackEscapeKey);
        this.$el.unbind('hide.bs.modal', this.unbindKeyUp);
    },

    _trackModalClick: function(event) {
        if (_.get(event, 'target.className', '').indexOf('confirm-below-minimum-amount-modal') > -1) {
            this._trackClickTarget('Cancel');
        }
    },

    _trackCancellation: function() {
        this._trackClickTarget('Cancel');
    },

    _goToCheckoutWithBelowMinimumAmount: function () {
        this._trackClickTarget('Checkout');
        this.$el.modal('hide');
        this.trigger('confirm_below_minimum_amount:allow_below_minimum_amount_checkout');
    },

    _trackClickTarget: function (clickTarget) {
        var cacheVendorCartData = this.model.toJSON();
        this.gtmService.fireSurchargeAction({
            "clickTarget": clickTarget,
            "minimumOrderValue": cacheVendorCartData.minimum_order_amount,
            "differenceToMinimumAmount": (cacheVendorCartData.minimum_order_amount - cacheVendorCartData.subtotal),
        });
        this._unbindKeyUp();
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
        this._unbindKeyUp();
    }
});
