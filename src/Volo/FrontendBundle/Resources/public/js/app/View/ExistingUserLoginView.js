var ExistingUserLoginView = Backbone.View.extend({
    initialize: function () {
        _.bindAll(this);
        var queryParams = {};
        if (this.$el.data('username')) {
            queryParams.username = this.$el.data('username');
        }
        if (this.$el.data('error-message-key')) {
            queryParams.error = this.$el.data('error-message-key');
        }
        this.loginRegistrationView = new LoginRegistrationView({
            el: '#login-registration-modal',
            queryParams: queryParams
        });
    },

    render: function () {
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
