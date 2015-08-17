var VOLO = VOLO || {};

VOLO.ContactInformatioForm = ValidationView.extend({
    _defaultConstraints: {
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
    },

    initialize: function () {
        _.bindAll(this);
        ValidationView.prototype.initialize.apply(this, arguments);
    },

    events: _.extend({
            'submit': '_submit',
            'keydown #contact-information-mobile-number': '_hideErrorMsg'
        }, ValidationView.prototype.events
    ),

    _hideErrorMsg: function() {
        this.$('.invalid_number').hide();
    },

    fillUpForm: function () {
        if (this.model.isValid()) {
            this.$('#contact-information-first-name').val(_.unescape(this.model.get('first_name')));
            this.$('#contact-information-last-name').val(_.unescape(this.model.get('last_name')));
            this.$('#contact-information-email').val(_.unescape(this.model.get('email')));
            this.$('#contact-information-mobile-number').val(_.unescape(this.model.getFullMobileNumber()));
            this.$('#contact-information-newsletter-checkbox').prop('checked', this.model.get('is_newsletter_subscribed'));
        }
    },

    _submit: function() {
        var form = this.$el.serializeJSON({
                checkboxUncheckedValue: 'false',
                parseBooleans: true
            }),
            customer = {},
            routingParam = {phoneNumber: this.$('#contact-information-mobile-number').val()};

        _.each(form.customer, function (val, key) {
            customer[key] = _.isString(val) ? _.escape(val) : val; 
        });
        
        this.$('.form__error-message').remove();

        this._checkIfUserExists(customer.email)
            .then(function (response) {
                if (!response.exists) {
                    return $.ajax({
                        url: Routing.generate('checkout_validate_phone_number', routingParam),
                            success: _.curry(this._onSuccessMobileNumberValidation, 2)(customer),
                        error: this._onErrorMobileNumberValidation
                    });
                }
            }.bind(this));

        return false;
    },

    _checkIfUserExists: function (email) {
        if (!this.model.isGuest) {
            return $.Deferred().resolve({exists: false});
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

    _onErrorMobileNumberValidation: function (response) {
        var errorMessage = _.get(response, 'responseJSON.error.mobile_number');
        if (errorMessage) {
            this.createErrorMessage(
                _.get(response, 'responseJSON.error.mobile_number'),
                this.$('#contact-information-mobile-number')[0]
            );
        }
    },

    _onSuccessMobileNumberValidation: function (customer, response) {
        customer.mobile_number = response.mobile_number;
        customer.mobile_country_code = response.mobile_country_code;

        this.model.save(customer, {
            success: function () {
                this.model.trigger('customer:saved');
            }.bind(this),
            error: this._onCustomerSaveError,
            wait: true
        });
    },

    _onCustomerSaveError: function(model, response) {
        _.each(_.get(response,  'responseJSON.error.errors', []), function (error) {
            var selector = 'input[name=\'customer['+ error.field_name +']\']',
                element = this.$(selector);
            _.each(_.get(error, 'violation_messages', []), function (message) {
                this.createErrorMessage(message, element[0]);
            }, this);
        }, this);
    }
});
