var LoginButtonView = Backbone.View.extend({
    initialize: function () {
        console.log('LoginButtonView.initialize ', this.cid);
        _.bindAll(this);

        this.loginRegistrationView = new LoginRegistrationView({
            el:'#login-registration-modal'
        });

        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:login', function(data) {
            this.trigger('loginRegistrationView:login', data);
        }.bind(this));
        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:registration', function(data) {
            this.trigger('loginRegistrationView:registration', data);
        }.bind(this));
    },

    events: {
        'click .show-login-modal': 'showLoginModal'
    },

    showLoginModal: function () {
        this.loginRegistrationView.render();
    },

    showRegistrationModal: function(data) {
        this.loginRegistrationView.renderRegistration(data);
    },

    showModalResetPassword: function(code) {
        this.loginRegistrationView.renderResetPassword(code);
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        if (this.loginRegistrationView) {
            this.loginRegistrationView.unbind();
        }
    }
});
