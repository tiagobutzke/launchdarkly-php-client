var LoginRegistrationView = Backbone.View.extend({
    _loginValidationView: null,
    _registerValidationView: null,

    _loginConstraints: {
        _username: {
            presence: true
        },
        _password: {
            presence: true
        }
    },

    _forgotPasswordConstraints: {
        _email: {
            presence: true,
            email: true
        }
    },

    _resetPasswordConstraints: {
        '_password': {
            presence: true
        }
    },

    _registrationConstraints: {
        'customer[first_name]': {
            presence: true
        },
        'customer[last_name]': {
            presence: true
        },
        'customer[email]': {
            presence: true,
            email: true
        },
        'customer[mobile_number]': {
            presence: true
        },
        'customer[password]': {
            presence: true
        },
        'customer[confirm_password]': {
            presence: true,
            equality: "customer[password]"
        }
    },

    initialize: function (options) {
        options = options || {};
        console.log('LoginRegistrationView.initialize ', this.cid);
        _.bindAll(this);

        this.address = null;
        this.spinner = null;

        this.customerModel = options.customerModel;
        this.templateLogin = _.template($('#template-login').html());
        this.templateRegistration = _.template($('#template-registration').html());
        this.templateResetPassword = _.template($('#template-reset-password').html());
        this.templateForgottPassword = _.template($('#template-forgot-password').html());
    },

    render: function() {
        this.$('.modal-content').html(this.templateLogin());
        this.$el.modal();
        this._bindLoginView();

        return this;
    },

    setUsername: function (username) {
        this.$('#username').val(username);
    },

    setErrorMessage: function (errorMessage) {
        this.$('.modal-error-message').html(errorMessage);
        this.$('.modal-error-message').removeClass('hide');
    },

    setAddress: function (address) {
        this.address = address;
    },

    renderRegistration: function() {
        this.$('.modal-content').html(this.templateRegistration());
        this.$el.modal();
        this._bindRegisterValidationView();

        if (this.customerModel) {
            this.$('#contact-information-first-name').val(this.customerModel.get('first_name'));
            this.$('#contact-information-last-name').val(this.customerModel.get('last_name'));
            this.$('#contact-information-email').val(this.customerModel.get('email'));
            this.$('#contact-information-mobile-number').val(this.customerModel.getFullMobileNumber());
        }

        return this;
    },

    renderResetPassword: function () {
        this.$('.modal-content').html(this.templateResetPassword());

        this.$el.modal();
        this._registerValidationView = new ValidationView({
            el: this.$('.reset-password-form'),
            constraints: this._resetPasswordConstraints
        });

        return this;
    },

    events: {
        'click .register-link': 'renderRegistration',
        'click .login-link': 'render',
        'click .forgot-password-link': 'renderForgotPassword',
        'submit .forgot-password-form': '_handingSubmitOfLostPasswordForm',
        'submit .reset-password-form': '_handingSubmitOfResetPasswordForm',
        'submit .login-form': '_submitLoginForm',
        'submit .registration-form': '_submitRegistrationForm',
        'click .modal-close-button': '_closeLoginRegistrationOverlay'
    },

    unbind: function() {
        this._unbindLoginView();
        this._unbindRegisterValidationView();
        this.stopListening();
        this.undelegateEvents();
    },

    renderForgotPassword: function() {
        var email = this.$('#username').val();
        this.$el.modal();

        this.$('.modal-content').html(this.templateForgottPassword);
        this._registerValidationView = new ValidationView({
            el: this.$('.forgot-password-form'),
            constraints: this._forgotPasswordConstraints
        });

        this.$('#email').val(email);

        return this;
    },

    _handingSubmitOfLostPasswordForm: function(event) {
        event.preventDefault();
        var $form = this.$('.forgot-password-form'),
            $modalContent = this.$('.modal-content'),
            target = document.getElementById('spinner-wrapper'),
            spinner = new Spinner();

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            beforeSend: function() {
                $(target).addClass('modal-content--loading');
                spinner.spin(target);
                $form.find('button').prop("disabled", true);
            },
            success: function(data) {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                $modalContent.html(data);
            },
            error: function (data) {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                $modalContent.html(data.responseText);
                $form.find('button').prop("disabled", false);
            }
        });
    },

    _handingSubmitOfResetPasswordForm: function(event) {
        event.preventDefault();
        var $form = this.$('.reset-password-form'),
            $modalContent = this.$('.modal-content'),
            target = document.getElementById('spinner-wrapper'),
            spinner = new Spinner();

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            beforeSend: function() {
                $(target).addClass('modal-content--loading');
                spinner.spin(target);
                $form.find('button').prop("disabled", true);
            },
            success: function() {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                this.render();
            }.bind(this),
            error: function (data) {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                $modalContent.html(data.responseText);
                $form.find('button').prop("disabled", false);
            }
        });
    },

    _submitLoginForm: function() {
        var userService = new VOLO.UserService(),
            userData = this.$('form').serializeJSON(),
            addressData = this.address ? this.address.toJSON() : {},
            redirectTo = $('body').hasClass('order-status-page') ? Routing.generate('home') : null,
            login;

        this._disableForm();
        this._enableSpinner();

        login = userService.login(userData, addressData, redirectTo);
        login.then(this._loginSuccess, this._loginFail);

        return false;
    },

    _loginSuccess: function(result) {
        this._fireSubmitEvent('loginRegistrationView:login', 'success');
        window.location.replace(result.url);
    },

    _loginFail: function(response) {
        this._replaceFormWithServerResponse(response);

        this._bindLoginView();
        this._fireSubmitEvent('loginRegistrationView:login', 'fail');
    },

    _disableForm: function() {
        this.$('#spinner-wrapper').addClass('modal-content--loading');
        this.$('button').prop("disabled", true);
    },

    _enableForm: function() {
        this.$('#spinner-wrapper').removeClass('modal-content--loading');
        this.$('button').prop("disabled", false);
    },

    _enableSpinner: function() {
        if (!this.spinner) {
            this.spinner = new Spinner();
        }

        this.spinner.spin(document.getElementById('spinner-wrapper'));
    },

    _disableSpinner: function() {
        this.spinner && this.spinner.stop();
        this.spinner = null;
    },

    _submitRegistrationForm: function() {
        var userService = new VOLO.UserService(),
            userData = this.$('form').serializeJSON(),
            addressData = this.address ? this.address.toJSON() : {},
            register;

        this._disableForm();
        this._enableSpinner();

        register = userService.register(userData, addressData);
        register.then(this._registerSuccess, this._registerFail);

        return false;
    },

    _registerSuccess: function() {
        this._fireSubmitEvent('loginRegistrationView:registration', 'success');

        window.location.reload(true);
    },

    _registerFail: function(response) {
        this._replaceFormWithServerResponse(response);

        this._bindRegisterValidationView();
        this._fireSubmitEvent('loginRegistrationView:registration', 'fail');
    },

    _replaceFormWithServerResponse: function(response) {
        this._disableSpinner();
        this._enableForm();

        this.$('.modal-content').html(response.responseText);
    },

    _closeLoginRegistrationOverlay: function() {
        this.$el.modal('hide');
        this.$('.modal-error-message').text('').addClass('hide');
        this.address = null;
    },

    _bindLoginView: function() {
        this._unbindLoginView();
        this._loginValidationView = new ValidationView({
            el: this.$('.login-form'),
            constraints: this._loginConstraints
        });
    },

    _bindRegisterValidationView: function() {
        this._unbindRegisterValidationView();
        this._registerValidationView = new ValidationView({
            el: this.$('.registration-form'),
            constraints: this._registrationConstraints
        });
    },

    _unbindLoginView: function() {
        if (this._loginValidationView) {
            console.log('unbind login validation view');
            this._loginValidationView.unbind();
        }
    },

    _unbindRegisterValidationView: function() {
        if (this._registerValidationView) {
            console.log('unbind register validation view');
            this._registerValidationView.unbind();
        }
    },

    _fireSubmitEvent: function(name, result) {
        this.trigger(name, {
            'method': 'email',
            'result': result
        });
    }
});
