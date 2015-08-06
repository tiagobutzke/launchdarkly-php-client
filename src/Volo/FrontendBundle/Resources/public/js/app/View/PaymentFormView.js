var PaymentFormView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.customerModel = options.customerModel;

        this._fillName();
        this._initPaymentFormating();
        this._adyenPublicKey = this.$el.data('adyen_public_key');
        this.listenTo(this.customerModel, 'change', this._fillName);
    },

    events: {
        'keyup #checkout-credit-card-form-expiry': '_fillExpiryForms',
        'keyup input': '_fillEncryptedData',
        'change #checkout-store-credit-card': '_changeIsCreditCardStored'
    },

    _initPaymentFormating: function() {
        this.$('#checkout-credit-card-form-expiry').payment('formatCardExpiry');
        this.$('#checkout-adyen-encrypted-form-number').payment('formatCardNumber');
        this.$('#checkout-adyen-encrypted-form-cvc').payment('formatCardCVC');
    },

    _fillEncryptedData: function() {
        var encryptedForm = adyen.encrypt.createEncryptedForm(
            this.el,
            this._adyenPublicKey,
            {cardTypeElement: this.$('#checkout-credit-card-form-card-type')[0]}
        );

        var isComplete = !this.$("input").filter(function() {
            return $(this).val() === "";
        }).length;

        this.model.save('adyen_encrypted_data', isComplete ? encryptedForm.encrypt() : null);
    },

    _changeIsCreditCardStored: function () {
        this.model.save('is_credit_card_store_active', this.$("#checkout-store-credit-card").is(':checked'));
    },

    _fillName: function() {
        this.$('#checkout-adyen-encrypted-form-holder-name').val(this.customerModel.getFullName());
    },

    _fillExpiryForms: function(e) {
        var inputValue = e.target.value,
            expiryValues = $.payment.cardExpiryVal(inputValue),
            month = expiryValues.month,
            year = expiryValues.year;

        if (month && year) {
            if (month < 10) {
                month = '0' + month.toString();
            }

            this.$('#checkout-adyen-encrypted-form-expiry-year').val(year);
            this.$('#checkout-adyen-encrypted-form-expiry-month').val(month);
        }
    },

    unbind: function() {
        this.undelegateEvents();
    }
});
