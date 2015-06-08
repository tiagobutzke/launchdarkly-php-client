var LoginButtonView = Backbone.View.extend({
    initialize: function () {
        console.log('LoginButtonView.initialize ', this.cid);
        _.bindAll(this);

        this.loginRegistrationView = new LoginRegistrationView({
            el:'#login-registration-modal'
        });
    },

    events: {
        'click .show-login-modal': '_showLoginModal'
    },

    _showLoginModal: function () {
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
