var VOLO = VOLO || {};

VOLO.CheckoutContactInformationView = Backbone.View.extend({
    events: {
        'click .checkout__contact-information__title-link': '_switchFormVisibility'
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
        this.listenTo(this.customerModel, 'customer:saved', this._onCustomerSaveSuccess);
        this.listenTo(this.customerModel, 'customer:already_exist', this.openLoginModal);
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

    _onCustomerSaveSuccess: function () {
        this.renderContactInformation();
        this._switchFormVisibility();
        this.checkoutModel.save('is_contact_information_valid', true);
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: this.customerModel.get('is_newsletter_subscribed')
        });
    }
});
