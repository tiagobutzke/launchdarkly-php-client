var VOLO = VOLO || {};

VOLO.CheckoutContactInformationView = Backbone.View.extend({
    events: {
        'click .checkout__contact-information__title-link': '_switchFormVisibility',
        'submit form': '_submit'
    },

    initialize: function(options) {
        _.bindAll(this);

        this.vendorId = options.vendorId;
        this.customerModel = options.customerModel;
        this.userAddressCollection = options.userAddressCollection;
        this.loginView = options.loginView;
        this.checkoutModel = options.checkoutModel;
        this.locationModel = options.locationModel;

        this.contactInformationForm = new VOLO.ContactInformatioForm({
            el: this.$('#contact-information-form'),
            model: this.customerModel
        });

        this.listenTo(this.customerModel, 'change', this.renderContactInformation);
        this.listenTo(this.checkoutModel, 'change', this.render);
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
                //this._showEditLink();
                //this._hideCancelLink();
            } else {
                this._openForm();
                //this._showCancelLink();
                //this._showEditLink();
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

    openLoginModal: function () {
        this.loginView.showLoginModal();
        this.loginView.setUsername(this.$('#contact-information-email').val());
        this.loginView.setErrorMessage(this.$('#checkout-edit-contact-information').data('error-message-key'));
        this.loginView.setAddress(this.userAddressCollection.get(this.checkoutModel.get('address_id')));
    },

    unbind: function () {
        this.contactInformationForm.unbind();
        this.stopListening();
        this.undelegateEvents();
    },

    _switchFormVisibility: function () {
        if (!this.customerModel.isValid()) {
            this._openForm();

            return;
        }

        if (this.$('#checkout-edit-contact-information').hasClass('hide')) {
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

    _submit: function() {
        var form = this.$('#contact-information-form').serializeJSON({
                checkboxUncheckedValue: 'false',
                parseBooleans: true
            }),
            customer = {};

        _.each(form.customer, function (val, key) {
            if (_.trim(val).length > 0) {
                customer[key] = _.escape(val);
            }
        });

        this.$('.form__error-message').remove();

        this._isExistingUser(customer.email)
            .then(function (response) {
                if (response.exists) {
                    return;
                }
                $.ajax({
                    url: Routing.generate('checkout_validate_phone_number', {phoneNumber: this.$('#contact-information-mobile-number').val()}),
                    success: function (response) {
                        this._onSuccessMobileNumberValidation(customer, response);
                    }.bind(this),
                    error: function (response) {
                        var errorMessage = _.get(response, 'responseJSON.error.mobile_number');
                        if (errorMessage) {
                            this.contactInformationForm.createErrorMessage(
                                _.get(response, 'responseJSON.error.mobile_number'),
                                this.$('#contact-information-mobile-number')[0]
                            );
                        }
                    }.bind(this)
                });
            }.bind(this));

        return false;
    },

    _isExistingUser: function (email) {
        if (!this.customerModel.isGuest) {
            var deferred = $.Deferred();

            return deferred.resolve({
                exists: false
            });
        }

        return $.ajax({
            url: Routing.generate('checkout_validate_email', {email: email}),
            dataType: 'json',
            success: function (response) {
                if (response.exists) {
                    this.openLoginModal();
                }
            }.bind(this)
        });
    },

    _onSuccessMobileNumberValidation: function (customer, response) {
        customer.mobile_number = response.mobile_number;
        customer.mobile_country_code = response.mobile_country_code;

        this.customerModel.save(customer, {
            success: this._onCustomerSaveSuccess,
            error: this._onCustomerSaveError
        });
    },

    _onCustomerSaveSuccess: function (customer) {
        this.renderContactInformation();
        this._switchFormVisibility();
        this.checkoutModel.save('is_contact_information_valid', true);
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: customer.get('is_newsletter_subscribed')
        });
    },

    _onCustomerSaveError: function(model, response) {
        _.each(_.get(response,  'responseJSON.error.errors', []), function (error) {
            var selector = 'input[name=\'customer['+ error.field_name +']\']',
                element = this.$(selector);
            _.each(_.get(error, 'violation_messages', []), function (message) {
                this.contactInformationForm.createErrorMessage(message, element[0]);
            }, this);
        }, this);
    }
});
