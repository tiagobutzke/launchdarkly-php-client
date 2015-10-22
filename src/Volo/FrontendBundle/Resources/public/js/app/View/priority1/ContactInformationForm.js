var VOLO = VOLO || {};

VOLO.ContactInformationForm = ValidationView.extend({
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
        this.listenTo(this, 'form-field:error', this._disableContinueButton);
        this.listenTo(this, 'form:valid', this._enableContinueButton);
    },

    events: function() {
        return _.extend({
                'submit': '_submit'
            }, ValidationView.prototype.events.apply(this)
        );
    },

    _enableContinueButton: function() {
        this.$(".button").removeClass('button--disabled');
    },

    _disableContinueButton: function() {
        this.$(".button").addClass('button--disabled');
    },

    _canBeSubmitted: function () {
        return !this.$(".button").hasClass('button--disabled');
    },

    fillUpForm: function () {
        if (this.model.isValid()) {
            this.$('#contact-information-first-name').val(_.unescape(this.model.get('first_name')));
            this.$('#contact-information-last-name').val(_.unescape(this.model.get('last_name')));
            this.$('#contact-information-email').val(_.unescape(this.model.get('email')));
            this.$('#contact-information-mobile-number').val(_.unescape(this.model.getFullMobileNumber()));
            this.$('#contact-information-newsletter-checkbox').prop('checked', this.model.get('is_newsletter_subscribed'));
        } else {
            this._disableContinueButton();
        }
    },

    _submit: function() {
        return false;
    },

    saveCustomerInformation: function () {
        var form = this.$el.serializeJSON({
                checkboxUncheckedValue: 'false',
                parseBooleans: true
            }),
            customer = {},
            phoneNum = encodeURIComponent(this.$('#contact-information-mobile-number').val()),
            routingParam = {phoneNumber: phoneNum};

        _.each(form.customer, function (val, key) {
            customer[key] = _.isString(val) ? _.escape(val) : val;
        });

        this.$('.form__error-message').remove();

        this._checkIfUserExists(customer.email)
            .then(function (response) {
                if (!response.exists) {
                    return $.ajax({
                        url: Routing.generate('customer_validate_phone_number', routingParam),
                        success: _.curry(this._onSuccessMobileNumberValidation, 2)(customer),
                        error: this._onErrorMobileNumberValidation
                    });
                }
            }.bind(this));
    },

    _checkIfUserExists: function (email) {
        if (!this.model.isGuest) {
            return $.Deferred().resolve({exists: false});
        }
        this.$('.form__error-message').remove();

        return $.ajax({
            url: Routing.generate('customer_validate_email', {email: encodeURIComponent(email)}),
            dataType: 'json',
            success: function (response) {
                if (response.exists) {
                    this.model.trigger('customer:already_exist');
                } else {
                    this.model.trigger('customer:new');
                }
            }.bind(this),
            error: function(response) {
                this._onCustomerSaveError(null, response);
            }.bind(this)
        });
    },

    _onErrorMobileNumberValidation: function (response) {
        var errorMessage = _.get(response, 'responseJSON.error.mobile_number');
        var field;
        if (errorMessage) {
            field = this.$('#contact-information-mobile-number')[0];
            this.removeCurrentErrorMessage(field);
            this.createErrorMessage(_.get(response, 'responseJSON.error.mobile_number'), field);
            this.trigger('form:error');
        }
    },

    _onSuccessMobileNumberValidation: function (customer, response) {
        customer.mobile_number = response.mobile_number;
        customer.mobile_country_code = '+' + response.mobile_country_code;

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
                this.removeCurrentErrorMessage(element[0]);
                this.createErrorMessage(message, element[0]);
            }, this);
        }, this);
    },
    _validateForm: function () {
        if (!this._canBeSubmitted()) {
            return false;
        }

        var formValues = validate.collectFormValues(this.el),
            promise = validate.async(formValues, this.constraints);

        promise.then(function () {
            this._hideErrorMessages();
            this.saveCustomerInformation();
        }.bind(this), function (errors) {
            this._showErrorMessages(errors);
        }.bind(this));

        return false;
    },

    /**
     * @override
     */
    onFieldSuccessValidation: function (target) {
        if (!this.hasErrors($(target).closest('form'))) {
            this.trigger('form:valid');
        }
    },

    hasErrors:function($form){
        return $form.find('span.form__error-message:visible').size();
    }
});

