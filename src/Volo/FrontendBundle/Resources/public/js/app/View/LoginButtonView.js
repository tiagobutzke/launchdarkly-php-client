var LoginButtonView = Backbone.View.extend({
    initialize: function () {
        console.log('LoginButtonView.initialize ', this.cid);
        _.bindAll(this);
    },

    events: {
        'click .show-login-modal': '_showLoginModal'
    },

    _showLoginModal: function () {
        if (this.loginRegistrationView) {
            this.loginRegistrationView.unbind();
        }

        this.loginRegistrationView = new LoginRegistrationView({
            el:'#login-registration-modal'
        });

        this.loginRegistrationView.render();
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        if (this.loginRegistrationView) {
            this.loginRegistrationView.unbind();
        }
    }
});
