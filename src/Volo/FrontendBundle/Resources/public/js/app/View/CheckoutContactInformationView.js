var VOLO = VOLO || {};

VOLO.CheckoutContactInformationView = Backbone.View.extend({
    events: {
        'click .checkout__contact-information__title-link': '_switchFormVisibility',
        'click .checkout__contact-information__register-link': '_showRegistrationFields',
        'click .checkout__contact-information__hide-register-link': '_hideRegisterFields',
        'click .checkout__contact-information__forgot-password': '_openForgotPasswordModal',
        'blur #contact-information-email': '_checkIfUserExists'
    },

    initialize: function(options) {
        _.bindAll(this);

        this.vendorId = options.vendorId;
        this.customerModel = options.customerModel;
        this.userAddressCollection = options.userAddressCollection;
        this.checkoutModel = options.checkoutModel;
        this.locationModel = options.locationModel;
        this.loginView = options.loginView;

        this.contactInformationForm = new VOLO.CheckoutContactInformationForm({
            el: this.$('#contact-information-form'),
            model: this.customerModel,
            userAddressCollection: this.userAddressCollection
        });

        this.listenTo(this.customerModel, 'change', this.renderContactInformation);
        this.listenTo(this.customerModel, 'customer:saved', this._onCustomerSaveSuccess);
        this.listenTo(this.customerModel, 'customer:already_exist', this._showLoginField);
        this.listenTo(this.customerModel, 'customer:new', this._showRegisterLink);
        this.listenTo(this.checkoutModel, 'change', this.render);
    },

    _checkIfUserExists: function() {
        var email = this.$('#contact-information-email').val();
        if (email) {
            this.contactInformationForm._checkIfUserExists(email);
        }
    },

    render: function () {
        if (this.customerModel.isGuest) {
            this.renderGuest();
        } else {
            this.renderAuthenticatedCustomer();
        }

        return this;
    },

    renderAuthenticatedCustomer: function () {
        if (_.isNull(this.checkoutModel.get('address_id'))) {
            this.hideContactInformation();
            this._hideForm();
            this.$el.addClass('checkout__step--reduced');
            this._hideEditLink();
            this._hideCancelLink();
        } else {
            this.$el.removeClass('checkout__step--reduced');

            this.contactInformationForm.fillUpForm();
            if (this.customerModel.isValid()) {
                this.checkoutModel.save('is_contact_information_valid', true);
                this.renderContactInformation();
                this._closeForm();
            } else {
                this._openForm();
            }
        }
    },

    renderGuest: function () {
        if (_.isNull(this.checkoutModel.get('address_id'))) {
            this.checkoutModel.save('is_contact_information_valid', false);
            this.hideContactInformation();
            this._hideForm();
            this.$el.addClass('checkout__step--reduced');
            this._hideEditLink();
            this._hideCancelLink();
        } else {
            this.$el.removeClass('checkout__step--reduced');

            this.contactInformationForm.fillUpForm();
            if (this.checkoutModel.get('is_contact_information_valid') && this.customerModel.isValid()) {
                this.renderContactInformation();
                this._closeForm();
            } else {
                this._openForm();
                if (!this.customerModel.isValid()) {
                    this._hideEditLink();
                    this._hideCancelLink();
                }
            }
        }
    },

    renderContactInformation: function () {
        this.$('.checkout__contact-information__full-name').text(_.unescape(this.customerModel.getFullName()));
        this.$('.checkout__contact-information__email').text(_.unescape(this.customerModel.get('email')));
        this.$('.checkout__contact-information__phone-number').text(_.unescape(this.customerModel.getFullMobileNumber()));
    },

    _showLoginField: function() {
        //additional validation
        this.contactInformationForm.constraints["customer[password]"] = {
            presence: true
        };

        this.contactInformationForm.cleanErrorMessages();
        delete this.contactInformationForm.constraints["customer[mobile_number]"];
        delete this.contactInformationForm.constraints["customer[first_name]"];
        delete this.contactInformationForm.constraints["customer[last_name]"];


        this.$('.checkout__contact-information__register-link-wrapper').addClass('hide');
        this.$('.checkout__contact-information__hide-register-link-wrapper').addClass('hide');
        this.$('.checkout__contact-information__form-first-name').addClass('hide');
        this.$('.checkout__contact-information__form-last-name').addClass('hide');
        this.$('#contact-information-mobile-number-wrap').addClass('hide');

        this.$('.checkout__contact-information__password-wrapper').removeClass('hide');
        this.$('.checkout__contact-information__login-hint-message').removeClass('hide');
        this.$('.checkout__contact-information__forgot-password').removeClass('hide');

        this.$('#contact-information-password').focus();
    },

    _showRegisterLink: function() {
        if (this.contactInformationForm.isUserRegistering()) return;

        this.contactInformationForm.cleanErrorMessages();
        delete this.contactInformationForm.constraints["customer[password]"];

        this.contactInformationForm.constraints["customer[first_name]"] = { presence: true };
        this.contactInformationForm.constraints["customer[last_name]"] = { presence: true };
        this.contactInformationForm.constraints["customer[mobile_number]"] = { presence: true };

        this.$('.checkout__contact-information__password-wrapper').addClass('hide');
        this.$('.checkout__contact-information__login-hint-message').addClass('hide');
        this.$('.checkout__contact-information__server-error-message').addClass('hide');
        this.$('.checkout__contact-information__forgot-password').addClass('hide');

        this.$('.checkout__contact-information__form-first-name').removeClass('hide');
        this.$('.checkout__contact-information__form-last-name').removeClass('hide');
        this.$('#contact-information-mobile-number-wrap').removeClass('hide');
        this.$('.checkout__contact-information__register-link-wrapper').removeClass('hide');
    },

    _hideRegisterFields: function() {
        this.$('.checkout__contact-information__password-wrapper').addClass('hide');
        this.$('.checkout__contact-information__hide-register-link-wrapper').addClass('hide');
        this.$('.checkout__contact-information__server-error-message').addClass('hide');

        this.$('.checkout__contact-information__register-link-wrapper').removeClass('hide');
    },

    _showRegistrationFields: function() {
        //additional validations
        this.contactInformationForm.constraints["customer[password]"] = {
            presence: true
        };

        this.$('.checkout__contact-information__register-link-wrapper').addClass('hide');
        this.$('.checkout__contact-information__password-wrapper').removeClass('hide');
        this.$('.checkout__contact-information__hide-register-link-wrapper').removeClass('hide');
    },

    unbind: function () {
        this.contactInformationForm.unbind();
        this.stopListening();
        this.undelegateEvents();
    },

    _switchFormVisibility: function () {
        if (!this.customerModel.isValid() || this.$('#checkout-edit-contact-information').hasClass('hide')) {
            this._openForm();
        } else {
            this._closeForm();
        }

        return false;
    },

    _openForm: function () {
        this.contactInformationForm.fillUpForm();
        this.$('.form__error-message').addClass('hide');
        this._showForm();
        this._showCancelLink();
        this._hideEditLink();
        this.hideContactInformation();
        this.trigger('form:open', this);

        console.debug('Checkout step 2');
    },

    _closeForm: function () {
        this._hideForm();
        this._showEditLink();
        this._hideCancelLink();
        this.$('#contact_information').removeClass('hide');
        this.showContactInformation();
        this.trigger('form:close', this);
    },

    _showForm: function () {
        this.$('#checkout-edit-contact-information').removeClass('hide');
    },

    _hideForm: function () {
        this.$('#checkout-edit-contact-information').addClass('hide');
    },

    hideContactInformation: function () {
        this.$('#checkout-contact-information').addClass('hide');
    },

    showContactInformation: function () {
        this.$('#checkout-contact-information').removeClass('hide');
    },

    _showEditLink: function () {
        this.$('.checkout__title-link__text--edit-contact').removeClass('hide');
        this.$('.checkout__title-link__icon.icon-pencil').removeClass('hide');
    },

    _hideEditLink: function () {
        this.$('.checkout__title-link__text--edit-contact').addClass('hide');
        this.$('.checkout__title-link__icon.icon-pencil').addClass('hide');
    },

    _showCancelLink: function () {
        this.$('.checkout__title-link__text--cancel-contact').removeClass('hide');
    },

    _hideCancelLink: function () {
        this.$('.checkout__title-link__text--cancel-contact').addClass('hide');
    },

    _onCustomerSaveSuccess: function () {
        this.renderContactInformation();
        this._switchFormVisibility();
        this.checkoutModel.save('is_contact_information_valid', true);
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: this.customerModel.get('is_newsletter_subscribed')
        });
    },

    _openForgotPasswordModal: function() {
        this.loginView.loginRegistrationView.render().renderForgotPassword();
        $('#email').val(this.$('#contact-information-email').val());
    }
});
