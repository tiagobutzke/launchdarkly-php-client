var VOLO = VOLO || {};

VOLO.CheckoutContactInformationForm = VOLO.ContactInformationForm.extend({
    initialize: function(options) {
        VOLO.ContactInformationForm.prototype.initialize.apply(this, arguments);

        this.userService = new VOLO.UserService();
        this.userAddressCollection = options.userAddressCollection;
    },

    isUserRegistering: function() {
        return this.$('.checkout__contact-information__hide-register-link-wrapper').is(':visible');
    },

    isUserLoggingIn: function() {
        return this.$('.checkout__contact-information__login-hint-message').is(':visible');
    },

    _submit: function() {
        var addressData = this.userAddressCollection.last().toJSON();

        this.$('.form__error-message').remove();
        if (this.isUserLoggingIn()) {
            this._loginUser(addressData);
        } else if (this.isUserRegistering()) {
            this._registerUser(addressData);
        } else {
            this.saveCustomerInformation();
        }

        return false;
    },

    _loginUser: function(addressData) {
        var loginData = {
                _username: this.$('#contact-information-email').val(),
                _password: this.$('#contact-information-password').val()
            },
            loginPromise = this.userService.login(loginData, addressData);

        loginPromise.then(this._registerSuccess, this._registerError);
    },

    _registerUser: function(addressData) {
        var userData = {
            'customer[first_name]': this.$('#contact-information-first-name').val(),
            'customer[last_name]': this.$('#contact-information-last-name').val(),
            'customer[email]': this.$('#contact-information-email').val(),
            'customer[mobile_number]': this.$('#contact-information-mobile-number').val(),
            'customer[password]': this.$('#contact-information-password').val(),
            'customer[confirm_password]': this.$('#contact-information-password').val()
        };

        var registerPromise = this.userService.register(userData, addressData);
        registerPromise.then(this._registerSuccess, this._registerError);
    },

    _registerSuccess: function() {
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: this.$('#contact-information-newsletter-checkbox').prop('checked')
        });

        window.location.reload(true);
    },

    _registerError: function(response) {
        var errorMsg = $(response.responseText).find('.modal-error-message').text().trim();
        this.$('.checkout__contact-information__server-error-message').text(errorMsg).removeClass('hide');
        this.$('.checkout__contact-information__login-hint-message').hide();
    }
});
