var VOLO = VOLO || {};

VOLO.ProfilePasswordFormView = Backbone.View.extend({
    initialize: function() {
        this._jsValidationView = new ValidationView({
            el: this.el,
            constraints: {
                "password_form[new_password]": {
                    length: {
                        minimum: 6
                    }
                }
            }
        });
    }
});
