VOLO = VOLO || {};
VOLO.NewsLetterView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this);

        this.validationView = new ValidationView({
            el: '.footer__subscribe__form',
            constraints: {
                "newsletter[email]": {
                    presence: true,
                    email: true
                },
                "newsletter[city_id]": {
                    presence: true
                }
            }
        });

        this.listenTo(this.validationView, 'form:valid', this._subscribe);
    },

    render: function() {
        var $select = this.$('.footer__subscribe__form__select'),
            mobileDetect = new MobileDetect(window.navigator.userAgent);

        mobileDetect.mobile() ? $select.selectpicker('mobile') : $select.selectpicker();
    },

    events: {
        'submit .footer__subscribe__form': '_onFormSubmit'
    },

    unbind: function() {
        this.validationView.unbind();
        this.stopListening();
    },

    _onFormSubmit: function() {
        return false;
    },

    _subscribe: function() {
        var data = JSON.stringify(this.$('.footer__subscribe__form').serializeJSON().newsletter),
            ajax = $.ajax({
                url: Routing.generate('newsletter.subscribe'),
                dataType: 'json',
                data: data,
                method: 'POST'
            });

        this._disableSubmitButton();
        ajax.then(this._showSubsribeSuccess, this._showSubscribeError);
    },

    _disableSubmitButton: function() {
        this.$('.newsletter-form-submit').addClass('button--disabled');
    },

    _enableSubmitButton: function() {
        this.$('.newsletter-form-submit').removeClass('button--disabled');
    },

    _showSubsribeSuccess: function () {
        this.$el.addClass('show-subscribe-message').animate({
            height: $('.footer__subscribe__success-message').outerHeight()
        }, 500);
        this._enableSubmitButton();
    },

    _showSubscribeError: function(response) {
        var errors = _.get(response, 'responseJSON.error.errors.data.items', []),
            errorMessage = _.get(response, 'responseJSON.error.errors.message', null);

        this._enableSubmitButton();
        this.$('.form__error-message').remove();

        if (null !== errorMessage) {
            this.validationView.createErrorMessage(errorMessage, this.$('[name="newsletter[email]"]'));
        } else {
            _.each(errors, function (error) {
                var $target = $('[name="newsletter[{fieldName}]"'.replace('{fieldName}', error.field_name));
                this.validationView.createErrorMessage(error.violation_messages[0], $target);
            }.bind(this));
        }
    }
});
