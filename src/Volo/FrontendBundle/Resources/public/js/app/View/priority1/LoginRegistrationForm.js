var VOLO = VOLO || {};

VOLO.LoginRegistrationForm = VOLO.ContactInformationForm.extend({
    initialize: function (options) {
        VOLO.ContactInformationForm.prototype.initialize.apply(this, arguments);

        this.loginRegistrationView = options.loginRegistrationView;
        this.constraints = _.extend({}, VOLO.ContactInformationForm.prototype._defaultConstraints, {
            "customer[mobile_number]": {
                presence: true,
                mobileNumber: true
            },
            'customer[email]': {
                presence: true,
                emailApi: true
            },
            'customer[password]': {
                presence: true,
                length: {
                    minimum: 6
                }
            },
            'customer[confirm_password]': {
                presence: true,
                equality: "customer[password]"
            }
        });
    },

    /**
     * @override
     */
    saveCustomerInformation: function () {
        this.loginRegistrationView._submitRegistrationForm();
    },

    /**
     * @override
     */
    onFieldSuccessValidation: function (target) {
        VOLO.ContactInformationForm.prototype.onFieldSuccessValidation.apply(this, arguments);
        this.hideCurrentErrorMessage(target);
    }
});
