var PaymentTypeView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.checkoutModel = options.checkoutModel;
    },

    events: {
        'click .paypal': '_displayPayPal',
        'click. .adyen': '_displayCreditCard',
        'click .checkout__item__card__help-toggle': '_toggleCreditCardHelp'
    },

    unbind: function() {
        this.undelegateEvents();
    },

    _toggleCreditCardHelp: function() {
        this.$('.checkout__item__card__help').toggle();
    },

    _displayCreditCard: function() {
        var $creditCardNode = this.$('.adyen');
        $creditCardNode.addClass('checkout__payment__wrapper--active');

        this.checkoutModel.set('payment_type_code', $creditCardNode.data('payment_type_code'));
        this.checkoutModel.set('payment_type_id', $creditCardNode.data('payment_type_id'));

        this.$('.checkout__payment__wrapper--active').removeClass('checkout__payment__wrapper--active');
        this.$('.checkout__payment_paypal_description').addClass('hide');
        this.$('#payment_form').toggleClass('hide', $("input:radio[name='credit_card']").length > 0);
        this.$('#saved_payment_options').removeClass('hide');
        this.$('.checkout__list').removeClass('hide');
    },

    _displayPayPal: function() {
        var $payPalNode = this.$('.paypal');
        $payPalNode.addClass('checkout__payment__wrapper--active');

        this.checkoutModel.set('payment_type_code', $payPalNode.data('payment_type_code'));
        this.checkoutModel.set('payment_type_id', $payPalNode.data('payment_type_id'));
        this.checkoutModel.set('credit_card_id', null);

        this.$('.checkout__payment__wrapper--active').removeClass('checkout__payment__wrapper--active');
        this.$('.checkout__payment_paypal_description').removeClass('hide');
        this.$('#payment_form').addClass('hide');
        this.$('#saved_payment_options').addClass('hide');
        this.$('.checkout__list').addClass('hide');
        this.$('input[name="credit_card"]').attr("checked", false);
    }
});
