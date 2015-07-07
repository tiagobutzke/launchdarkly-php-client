var PaymentTypeView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.checkoutModel = options.checkoutModel;
    },

    events: {
        'click .paypal': '_displayPayPal',
        'click .adyen': '_displayCreditCard',
        'click .adyen_hpp': '_displayAdyenHpp',
        'click .checkout__item__card__help-toggle': '_toggleCreditCardHelp',
        'click #add_new_credit_card_link': '_toggleNewCreditCard',
        'click .credit-card--radio': '_selectSavedCreditCard'
    },

    unbind: function() {
        this.undelegateEvents();
    },

    _toggleCreditCardHelp: function() {
        this.$('.checkout__item__card__help').toggle();
    },

    _displayCreditCard: function() {
        var $creditCardNode = this.$('.adyen');

        this.checkoutModel.set('payment_type_code', $creditCardNode.data('payment_type_code'));
        this.checkoutModel.set('payment_type_id', $creditCardNode.data('payment_type_id'));

        this.$('.checkout__payment_paypal_description').addClass('hide');
        this.$('#payment_form').toggleClass('hide', this.$(".credit-card--radio").length > 0);
        this.$('#saved_payment_options').removeClass('hide');
        this.$('.checkout__list').removeClass('hide');

        this._activatePaymentMethod($creditCardNode);
    },

    _displayAdyenHpp: function() {
        var $node = this.$('.adyen_hpp');

        this.checkoutModel.set('payment_type_code', $node.data('payment_type_code'));
        this.checkoutModel.set('payment_type_id', $node.data('payment_type_id'));

        this.$('.checkout__payment_adyen_hpp_description').removeClass('hide');
        this.$('#saved_payment_options').addClass('hide');
        this.$('.checkout__list').addClass('hide');
        this.$('#payment_form').addClass('hide');

        this._activatePaymentMethod($node);
    },

    _displayPayPal: function() {
        var $payPalNode = this.$('.paypal');

        this.checkoutModel.set('payment_type_code', $payPalNode.data('payment_type_code'));
        this.checkoutModel.set('payment_type_id', $payPalNode.data('payment_type_id'));
        this.checkoutModel.set('credit_card_id', null);

        this.$('.checkout__payment_paypal_description').removeClass('hide');
        this.$('#payment_form').addClass('hide');
        this.$('#saved_payment_options').addClass('hide');
        this.$('.checkout__list').addClass('hide');
        this.$('.credit-card--radio').attr("checked", false);
        this.$('#add_new_credit_card_link').removeClass('paymentFormOpen');

        this._activatePaymentMethod($payPalNode);
    },

    _activatePaymentMethod: function($paymentMethodNode) {
        this.$('.checkout__payment__wrapper--active').removeClass('checkout__payment__wrapper--active');
        $paymentMethodNode.addClass('checkout__payment__wrapper--active');
    },

    _toggleNewCreditCard: function() {
        var $payment_form = this.$('#payment_form'),
            paymentFormVisible;

        $payment_form.toggleClass('hide');
        paymentFormVisible = !$payment_form.hasClass('hide');

        this.$('#add_new_credit_card_link').toggleClass('paymentFormOpen', paymentFormVisible);
        if (paymentFormVisible) {
            this.$('.credit-card--radio').attr("checked", false);
            VOLO.checkoutModel.set('credit_card_id', null);
        }
    },

    _selectSavedCreditCard: function() {
        this.$('#payment_form').addClass('hide');
        this.$('#add_new_credit_card_link').removeClass('paymentFormOpen');

        VOLO.checkoutModel.set('credit_card_id', this.$(".credit-card--radio:checked").val());
        VOLO.checkoutModel.set('adyen_encrypted_data', null);
    }
});
