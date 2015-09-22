var LoginButtonView = Backbone.View.extend({
    initialize: function (options) {
        console.log('LoginButtonView.initialize ', this.cid);
        _.bindAll(this);

        this.customerModel = options.customerModel;

        this.loginRegistrationView = new LoginRegistrationView({
            el:'#login-registration-modal',
            customerModel:  options.customerModel
        });

        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:login', function(data) {
            this.trigger('loginRegistrationView:login', data);
        }.bind(this));
        this.listenTo(this.loginRegistrationView, 'loginRegistrationView:registration', function(data) {
            this.trigger('loginRegistrationView:registration', data);
        }.bind(this));
        this.listenTo(this.customerModel, 'change:first_name', this.updateCustomerName);
    },

    events: {
        'click .show-login-modal': 'showLoginModal'
    },

    showLoginModal: function () {
        this.loginRegistrationView.render();
    },

    setUsername: function (username) {
        this.loginRegistrationView.setUsername(username);
    },

    setErrorMessage: function (errorMessage) {
        this.loginRegistrationView.setErrorMessage(errorMessage);
    },

    setAddress: function (address) {
        this.loginRegistrationView.setAddress(address);
    },

    showRegistrationModal: function() {
        this.loginRegistrationView.renderRegistration();
    },

    showModalResetPassword: function() {
        this.loginRegistrationView.renderResetPassword();
    },

    updateCustomerName: function() {
        this.$('.header__account__open').text(this.customerModel.get('first_name'));
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        if (this.loginRegistrationView) {
            this.loginRegistrationView.unbind();
        }
    }
});
