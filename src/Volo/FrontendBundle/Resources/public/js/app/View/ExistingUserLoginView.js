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

        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:login', function(data) {
            this.trigger('loginRegistrationView:login', data);
        }.bind(this));
        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:registration', function(data) {
            this.trigger('loginRegistrationView:registration', data);
        }.bind(this));
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
