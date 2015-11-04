VOLO.NewsLetterView = ValidationView.extend({

    _defaultConstraints: {
        "newsletter[email]": {
            presence: true,
            email: true
        },
        "newsletter[city_id]": {
            presence: true
        }
    },

    initialize: function (options) {
        _.bindAll(this);
        ValidationView.prototype.initialize.apply(this, arguments);
    },

    _submitNewsLetterForm: function (e) {
        $.ajax({
            url: Routing.generate('newsletter.subscribe'),
            dataType: 'json',
            data: JSON.stringify(this.$('.footer__subscribe__form').serializeJSON().newsletter),
            method: 'POST',
            success: function () {
                this._showSignupSuccessMessage();
            }.bind(this),
            error: function (response) {
                _.each(_.get(response, 'responseJSON.error.errors', []), function (error) {
                    var element = this.$('input[name="newsletter[' + error.field_name + ']"]');
                    _.each(_.get(error, 'violation_messages', []), function (message) {
                        this.removeCurrentErrorMessage(element[0]);
                        this.createErrorMessage(message, element[0]);
                    }, this);
                }, this);
            }.bind(this)
        });

        return false;
    },

    _validateForm: function () {
        var formValues = validate.collectFormValues(this.el),
            promise = validate.async(formValues, this.constraints);

        promise.then(this._submitNewsLetterForm, function (errors) {
            this._showErrorMessages(errors);
        }.bind(this));

        return false;
    },

    _showSignupSuccessMessage: function () {
        this.$el.addClass('show-subscribe-message').animate({
            height: $('.footer__subscribe__success-message').outerHeight()
        }, 500);
    },
});
