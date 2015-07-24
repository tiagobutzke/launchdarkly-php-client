var VOLO = VOLO || {};

VOLO.CheckoutContactInformationView = Backbone.View.extend({
    events: {
        'submit': '_submit'
    },

    initialize: function() {
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

        this._jsValidationView = new View({
            el: this.$el,
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
    },

    unbind: function () {
        this._jsValidationView.unbind();
        this.stopListening();
        this.undelegateEvents();
    },

    _submit: function() {
        this.trigger('validationView:validateSuccessful', {
            newsletterSignup: this.$('#newsletter_checkbox').is(':checked')
        });
    }
});
