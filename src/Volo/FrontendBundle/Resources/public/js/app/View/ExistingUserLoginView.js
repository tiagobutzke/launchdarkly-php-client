var ExistingUserLoginView = Backbone.View.extend({
    initialize: function () {
        _.bindAll(this);
        this.loginRegistrationView = new LoginRegistrationView({
            el:'#login-registration-modal'
        });
    },

    render: function () {
        var queryParams = [];
        if (this.$el.data('username')) {
            queryParams.push('username=' + encodeURIComponent(this.$el.data('username')));
        }
        if (this.$el.data('error-message-key')) {
            queryParams.push('error=' + encodeURIComponent(this.$el.data('error-message-key')));
        }

        this.loginRegistrationView.render(queryParams.join('&'));
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        if (this.loginRegistrationView) {
            this.loginRegistrationView.unbind();
        }
    }
});
