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

    events: function(){
        return _.extend({}, ValidationView.prototype.events, {
            'keydown #contact-information-mobile-number': '_hideErrorMsg'
        });
    },

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
    }
});
