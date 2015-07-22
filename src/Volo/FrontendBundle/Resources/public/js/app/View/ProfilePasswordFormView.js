var VOLO = VOLO || {};

VOLO.ProfilePasswordFormView = Backbone.View.extend({
    initialize: function() {
        this._jsValidationView = new ValidationView({
            el: this.el,
            constraints: {
                "password_form[old_password]": {
                    presence: true
                },
                "password_form[new_password]": {
                    presence: true,
                    length: {
                        minimum: 6
                    }
                }
            }
        });
    }
});
