var VOLO = VOLO || {};

VOLO.CheckoutContactInformationForm = VOLO.ContactInformationForm.extend({
    initialize: function(options) {
        VOLO.ContactInformationForm.prototype.initialize.apply(this, arguments);

        this.userAddressCollection = options.userAddressCollection;
    },

    _submit: function() {
        var usedAddress = this.userAddressCollection.at(VOLO.userAddressCollection.length -1).toJSON(),
            addressData = this._prepareGuestAddressData(usedAddress),
            xhr;

        this.$('.form__error-message').remove();

        if (this.model.isExistingEmail) {
            xhr = this._loginUser(addressData);
        } else if (this.model.isRegistering) {
            xhr = this._registerUser(addressData);
        } else {
            this._doGuestCheckout();
        }

        xhr && xhr.done(this._xhrSuccess);
        xhr && xhr.fail(this._xhrError);

        return false;
    },

    _xhrSuccess: function() {
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: this.$('#contact-information-newsletter-checkbox').prop('checked')
        });
        window.location.reload();
    },

    _xhrError: function(response) {
        var errorMsg = $(response.responseText).find('.modal-error-message').text().trim();
        this.$('.checkout__contact-information__server-error-message').text(errorMsg).removeClass('hide');
        this.$('.checkout__contact-information__login-hint-message').hide();
    },

    _prepareGuestAddressData: function(address) {
        var result = {};

        _.each(_.keys(address), function(key) {
            result['guest_address['+key+']'] = address[key];
        });

        return result;
    },

    _loginUser: function(addressData) {
        var data = _.extend(addressData, {
            _username: this.$('#contact-information-email').val(),
            _password: this.$('#contact-information-password').val()
        });

        return $.ajax({
            type: "POST",
            url: Routing.generate('login_check'),
            data: data
        });
    },

    _registerUser: function(addressData) {
        var data = _.extend(addressData, {
            'customer[first_name]': this.$('#contact-information-first-name').val(),
            'customer[last_name]': this.$('#contact-information-last-name').val(),
            'customer[email]': this.$('#contact-information-email').val(),
            'customer[mobile_number]': this.$('#contact-information-mobile-number').val(),
            'customer[password]': this.$('#contact-information-password').val(),
            'customer[confirm_password]': this.$('#contact-information-password').val()
        });

        return $.ajax({
            type: "POST",
            url: Routing.generate('customer.create'),
            data: data
        });
    }
});
