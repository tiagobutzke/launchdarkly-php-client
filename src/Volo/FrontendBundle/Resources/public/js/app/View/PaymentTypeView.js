var PaymentTypeView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.checkoutModel = options.checkoutModel;

        if (this.$('#checkout-payment-form').length > 0) {
            this.paymentFormView = new PaymentFormView({
                el: this.$('#checkout-payment-form'),
                model: this.checkoutModel,
                customerModel: options.customerModel
            });
        }

        if (options.cartModel.getCart(options.vendorId).get('total_value') > 0 || VOLO.configuration.countryCode !== 'fi') {
            this.$('.checkout__payment__option-wrapper').first().click();
        } else {
            this.$('.invoice').click();
        }

    },

    events: {
        'click .paypal': '_displayPayPal',
        'click .adyen': '_displayCreditCard',
        'click .adyen_hpp': '_displayAdyenHpp',
        'click .cod': '_displayCashOnDelivery',
        'click .invoice': '_displayInvoice',
        'click .checkout__payment__credit-card-fields-help-toggle': '_toggleCreditCardHelp',
        'click #checkout-add-credit-card-link': '_toggleNewCreditCard',
        'click .checkout__saved-payment-options__credit-card-radio': '_selectSavedCreditCard'
    },

    unbind: function() {
        this.undelegateEvents();
        this.paymentFormView.unbind();
    },

    _toggleCreditCardHelp: function() {
        this.$('.checkout__payment__credit-card-fields-help').toggle();
    },

    _displayInvoice: function() {
        var $invoice = this.$('.invoice');

        this.checkoutModel.save('payment_type_code', $invoice.data('payment_type_code'));
        this.checkoutModel.save('payment_type_id', $invoice.data('payment_type_id'));

        this._activatePaymentMethod($invoice);
    },

    _displayCashOnDelivery: function() {
        var $cod = this.$('.cod');
        this.checkoutModel.save('payment_type_code', $cod.data('payment_type_code'));
        this.checkoutModel.save('payment_type_id', $cod.data('payment_type_id'));
        this.$('.checkout__payment__option-description--paypal').addClass('hide');
        this.$('.checkout__payment__option-description--adyen').addClass('hide');
        this.$('.checkout__payment__option-description--cod').removeClass('hide');
        this.$('.checkout__saved-payment-options__list').addClass('hide');
        this._hideCreditCardManager();

        this._activatePaymentMethod($cod);
    },

    _displayCreditCard: function() {
        var $creditCardNode = this.$('.adyen');

        this.checkoutModel.save('payment_type_code', $creditCardNode.data('payment_type_code'));
        this.checkoutModel.save('payment_type_id', $creditCardNode.data('payment_type_id'));

        this.$('.checkout__payment__option-description--paypal').addClass('hide');
        this.$('.checkout__payment__option-description--adyen').addClass('hide');
        this.$('.checkout__payment__option-description--cod').addClass('hide');
        this.$('#checkout-payment-form').toggleClass('hide', this.$(".checkout__saved-payment-options__credit-card-radio").length > 0);
        this.$('#checkout-saved-payment-options').removeClass('hide');
        this.$('.checkout__saved-payment-options__list').removeClass('hide');

        this._activatePaymentMethod($creditCardNode);
    },

    _displayAdyenHpp: function() {
        var $node = this.$('.adyen_hpp');

        this.checkoutModel.save('payment_type_code', $node.data('payment_type_code'));
        this.checkoutModel.save('payment_type_id', $node.data('payment_type_id'));

        this.$('.checkout__payment__option-description--adyen').removeClass('hide');
        this.$('.checkout__payment__option-description--paypal').addClass('hide');
        this.$('.checkout__payment__option-description--cod').addClass('hide');
        this.$('#checkout-saved-payment-options').addClass('hide');
        this.$('.checkout__saved-payment-options__list').addClass('hide');
        this._hideCreditCardManager();

        this._activatePaymentMethod($node);
    },

    _displayPayPal: function() {
        var $payPalNode = this.$('.paypal');

        this.checkoutModel.save('payment_type_code', $payPalNode.data('payment_type_code'));
        this.checkoutModel.save('payment_type_id', $payPalNode.data('payment_type_id'));
        this.checkoutModel.save('credit_card_id', null);

        this.$('.checkout__payment__option-description--paypal').removeClass('hide');
        this.$('.checkout__payment__option-description--adyen').addClass('hide');
        this.$('.checkout__payment__option-description--cod').addClass('hide');
        this._hideCreditCardManager();

        this._activatePaymentMethod($payPalNode);
    },

    _hideCreditCardManager: function() {
        this.$('#checkout-payment-form').addClass('hide');
        this.$('#checkout-saved-payment-options').addClass('hide');
        this.$('.checkout__saved-payment-options__list').addClass('hide');
        this.$('.checkout__saved-payment-options__credit-card-radio').attr("checked", false);
        this.$('#checkout-add-credit-card-link').removeClass('checkout__saved-payment-options-open');
    },

    _activatePaymentMethod: function($paymentMethodNode) {
        this.$('.checkout__payment__option-wrapper--active').removeClass('checkout__payment__option-wrapper--active');
        $paymentMethodNode.addClass('checkout__payment__option-wrapper--active');
    },

    _toggleNewCreditCard: function() {
        var $checkoutPaymentForm = this.$('#checkout-payment-form'),
            paymentFormVisible;

        $checkoutPaymentForm.toggleClass('hide');
        paymentFormVisible = !$checkoutPaymentForm.hasClass('hide');

        this.$('#checkout-add-credit-card-link').toggleClass('checkout__saved-payment-options-open', paymentFormVisible);
        if (paymentFormVisible) {
            this.$('.checkout__saved-payment-options__credit-card-radio').attr("checked", false);
            VOLO.checkoutModel.set('credit_card_id', null);
        }
    },

    _selectSavedCreditCard: function() {
        this.$('#checkout-payment-form').addClass('hide');
        this.$('#checkout-add-credit-card-link').removeClass('checkout__saved-payment-options-open');

        VOLO.checkoutModel.set('credit_card_id', this.$(".checkout__saved-payment-options__credit-card-radio:checked").val());
        VOLO.checkoutModel.set('adyen_encrypted_data', null);
    }
});
