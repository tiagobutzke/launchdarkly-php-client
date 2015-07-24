var PaymentFormView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this);

        this._fillName();
        this._initPaymentFormating();
        this._adyenPublicKey = this.$el.data('adyen_public_key');
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
            {cardTypeElement: this.$('#cardType')[0]}
        );

        var isComplete = !this.$("input").filter(function() {
            return $(this).val() === "";
        }).length;

        this.model.set('adyen_encrypted_data', isComplete ? encryptedForm.encrypt() : null);
    },

    _changeIsCreditCardStored: function () {
        this.model.set('is_credit_card_store_active', this.$("#checkout-store-credit-card").is(':checked'));
    },

    _fillName: function() {
        // TODO check if this code works and is still needed
        var name = $('#checkout-contact-information .checkout__step__item-content div')[0];

        if (name) {
            this.$('#checkout-adyen-encrypted-form-holder-name').val(name.textContent);
        }
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
