var VOLO = VOLO || {};

VOLO.CheckoutContactInformationView = Backbone.View.extend({
    events: {
        'click .checkout__contact-information__title-link': '_switchFormVisibility',
        'submit form': '_submit'
    },

    initialize: function(options) {
        _.bindAll(this);

        var View = ValidationView.extend({
            events: function(){
                return _.extend({}, ValidationView.prototype.events, {
                    'keydown #contact-information-mobile-number': '_hideErrorMsg'
                });
            },

            _hideErrorMsg: function() {
                this.$('.invalid_number').hide();
            }
        });

        this.customerModel = options.customerModel;
        this.userAddressCollection = options.userAddressCollection;
        this.existingUserLoginView = null;

        this._jsValidationView = new View({
            el: this.$('#contact-information-form'),
            constraints: {
                "customer[first_name]": {
                    presence: true
                },
                "customer[last_name]": {
                    presence: true
                },
                "customer[email]": {
                    presence: true,
                    email: true
                },
                "customer[mobile_number]": {
                    presence: true
                }
            }
        });

        this.listenTo(this.customerModel, 'change', this.renderContactInformation);
    },

    render: function () {
        if (this.userAddressCollection.length === 0) {
            this.hideContactInformation();
            this._hideForm();
            this.$el.addClass('checkout__step--reduced');
            this.$('.checkout__contact-information__title-link').addClass('hide');

            return this;
        } else {
            this.$el.removeClass('checkout__step--reduced');
            this.$('.checkout__contact-information__title-link').removeClass('hide');
        }

        if (this.customerModel.isValid()) {
            this.renderContactInformation();
            this._closeForm();
        } else {
            this._fillUpForm();
            this._openForm();
        }

        return this;
    },

    renderContactInformation: function () {
        this.$('.customer_full_name').html(this.customerModel.escape('first_name') + ' ' + this.customerModel.escape('last_name'));
        this.$('.customer_email').html(this.customerModel.escape('email'));
        this.$('.customer_phone_number').html(_.escape(this.customerModel.getFullMobileNumber()));
    },

    renderExistingUser: function () {
        if (this.existingUserLoginView) {
            this.existingUserLoginView.unbind();
        }
        this.existingUserLoginView = new ExistingUserLoginView({
            el: this.$('#show-login-overlay'),
            username: this.$('#contact-information-email').val(),
            address: this.userAddressCollection.first()
        });
        this.existingUserLoginView.render();
    },

    unbind: function () {
        this._jsValidationView.unbind();
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
        this._fillUpForm();
        this.$('.form__error-message').addClass('hide');
        this._showForm();
        this.$('.checkout__title-link__text--edit-contact').removeClass('contact_information_form-open');
        this.hideContactInformation();
        this.trigger('form:open');
    },

    _closeForm: function () {
        this._hideForm();
        this.$('.checkout__title-link__text--edit-contact').addClass('contact_information_form-open');
        this.$('#contact_information').removeClass('hide');
        this.showContactInformation();
        this.trigger('form:close');
    },

    _fillUpForm: function () {
        if (this.customerModel.isValid()) {
            this.$('#contact-information-first-name').val(this.customerModel.get('first_name'));
            this.$('#contact-information-last-name').val(this.customerModel.get('last_name'));
            this.$('#contact-information-email').val(this.customerModel.get('email'));
            this.$('#contact-information-mobile-number').val(this.customerModel.getFullMobileNumber());
            this.$('#newsletter_checkbox').val(this.customerModel.get('newsletter_checkbox'));
        }
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

    _submit: function() {
        var form = this.$('#contact-information-form').serializeJSON(),
            customer = form.customer;

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
                        this.$('.error_msg.invalid_number').data({'validation-msg': _.get(response, 'responseJSON.error.mobile_number')});
                        this._jsValidationView._displayMessage(this.$('#contact-information-mobile-number')[0]);
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
                    this.renderExistingUser();
                }
            }.bind(this)
        });
    },

    _onSuccessMobileNumberValidation: function (customer, response) {
        customer.mobile_number = response.mobile_number;
        customer.mobile_country_code = response.mobile_country_code;

        this.customerModel.save(customer, {
            success: this._onCustomerSaveSuccess
        });
    },

    _onCustomerSaveSuccess: function () {
        this.renderContactInformation();
        this._switchFormVisibility();
    },

    isNewsletterSubscriptionChecked: function () {
        return this.$('#newsletter_checkbox').is(':checked');
    }
});
