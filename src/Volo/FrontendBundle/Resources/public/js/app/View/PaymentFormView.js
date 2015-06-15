var PaymentFormView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this);

        this._fillName();
        this._initPaymentFormating();
        this._adyenPublicKey = this.$el.data('adyen_public_key');
    },

    events: {
        'keyup #form-expiry': '_fillExpiryForms',
        'keyup input': '_fillEncryptedData'
    },

    _initPaymentFormating: function() {
        this.$('#form-expiry').payment('formatCardExpiry');
        this.$('#adyen-encrypted-form-number').payment('formatCardNumber');
        this.$('#adyen-encrypted-form-cvc').payment('formatCardCVC');
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

    _fillName: function() {
        var name = $('#contact_information .checkout__item span')[0];

        if (name) {
            this.$('#adyen-encrypted-form-holder-name').val(name.textContent);
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

            this.$('#adyen-encrypted-form-expiry-year').val(year);
            this.$('#adyen-encrypted-form-expiry-month').val(month);
        }
    },

    unbind: function() {
        this.undelegateEvents();
    }
});
