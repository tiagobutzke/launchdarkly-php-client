VOLO = VOLO || {};
VOLO.NewsLetterView = Backbone.View.extend({
    initialize: function() {
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

        this.listenTo(this.validationView, 'form:valid', this._onFormValid);
        this.listenTo(this.validationView, 'form:error', this._onFormError);
    },

    events: {
        'submit .footer__subscribe__form': '_onFormSubmit',
        'click #newsletter-form-submit': '_onFormSubmit'
    },

    unbind: function() {
        this.validationView.unbind();
        this.stopListening();
    },

    _onFormValid: function() {
        debugger;

        return false;
    },

    _onFormError: function() {
        debugger;

        return false;
    },

    _onFormSubmit: function() {
        return false;
    }

});
// VOLO.NewsLetterView = ValidationView.extend({
//     _defaultConstraints: {
//         "newsletter[email]": {
//             presence: true,
//             email: true
//         },
//         "newsletter[city_id]": {
//             presence: true
//         }
//     },
//
//     initialize: function () {
//         _.bindAll(this);
//         ValidationView.prototype.initialize.apply(this, arguments);
//
//         this.mobileDetect = new MobileDetect(window.navigator.userAgent);
//     },
//
//     render: function() {
//         // this.mobileDetect.mobile() ? this.$('select').selectpicker('mobile') : this.$('select').selectpicker();
//         // this.$('select').selectpicker('refresh');
//
//         return this;
//     },
//
//     _submitNewsLetterForm: function () {
//         $.ajax({
//             url: Routing.generate('newsletter.subscribe'),
//             dataType: 'json',
//             data: JSON.stringify(this.$('.footer__subscribe__form').serializeJSON().newsletter),
//             method: 'POST',
//             success: function () {
//                 this._showSignupSuccessMessage();
//             }.bind(this),
//             error: function (response) {
//                 _.each(_.get(response, 'responseJSON.error.errors', []), function (error) {
//                     var element = this.$('input[name="newsletter[' + error.field_name + ']"]');
//                     _.each(_.get(error, 'violation_messages', []), function (message) {
//                         this.removeCurrentErrorMessage(element[0]);
//                         this.createErrorMessage(message, element[0]);
//                     }, this);
//                 }, this);
//             }.bind(this)
//         });
//
//         return false;
//     },
//
//     _validateForm: function () {
//         var formValues = validate.collectFormValues(this.el),
//             promise = validate.async(formValues, this.constraints);
//
//         promise.then(this._submitNewsLetterForm, function (errors) {
//             this._showErrorMessages(errors);
//         }.bind(this));
//
//         return false;
//     },
//
//     _showSignupSuccessMessage: function () {
//         this.$el.addClass('show-subscribe-message').animate({
//             height: $('.footer__subscribe__success-message').outerHeight()
//         }, 500);
//     },
// });
