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
        'click .forgot-password-link': '_loadForgotPasswordFormIntoLoginModal',
        'submit .forgot-password-form': '_handingSubmitOfLostPasswordForm',
        'submit .reset-password-form': '_handingSubmitOfResetPasswordForm',
        'submit .login-form': '_handingSubmitOfLoginForm',
        'submit .registration-form': '_handingSubmitOfRegistrationForm',
        'click .modal-close-button': '_closeLoginRegistrationOverlay'
    },

    unbind: function() {
        this._unbindLoginView();
        this._unbindRegisterValidationView();
        this.stopListening();
        this.undelegateEvents();
    },

    _loadForgotPasswordFormIntoLoginModal: function() {
        var email = this.$('#username').val();

        this.$('.modal-content').html(this.templateForgottPassword);
        this._registerValidationView = new ValidationView({
            el: this.$('.forgot-password-form'),
            constraints: this._forgotPasswordConstraints
        });

        this.$('#email').val(email);
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

    _handingSubmitOfLoginForm: function(event) {
        event.preventDefault();
        var $form = this.$('.login-form');
        this._handleFormSubmit($form, this.$('.modal-content'));
    },

    _handingSubmitOfRegistrationForm: function(event) {
        event.preventDefault();
        var $form = this.$('.registration-form');
        this._handleFormSubmit($form, this.$('.modal-content'));

        return false;
    },

    _closeLoginRegistrationOverlay: function() {
        this.$el.modal('hide');
        this.$('.modal-error-message').text('').addClass('hide');
        this.address = null;
    },

    _handleFormSubmit: function($form, $modalContent) {
        var target = document.getElementById('spinner-wrapper'),
            spinner = new Spinner(),
            data = $form.serializeJSON();

        if (this.address) {
            data.guest_address = this.address.toJSON();
        }

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $.param(data),
            dataType: 'json',
            beforeSend: function(xhr) {
                $(target).addClass('modal-content--loading');
                spinner.spin(target);
                $form.find('button').prop("disabled", true);
            }.bind(this),
            success: function(response) {
                this._unbindLoginView();
                this._unbindRegisterValidationView();

                this._fireFormSubmit($form, true);

                window.location.replace(response.url);
            }.bind(this),
            error: function (data) {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                $modalContent.html(data.responseText);
                this._rebindValidations($form);
                $form.find('button').prop("disabled", false);

                this._fireFormSubmit($form, false);
            }.bind(this)
        });
    },


    _rebindValidations: function($form) {
        if ($form.attr('id') === 'login-form') {
            this._bindLoginView();
        } else {
            this._bindRegisterValidationView();
        }
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

    _fireFormSubmit: function ($form, isSuccess) {
        if ($form.attr('id') === 'login-form') {
            this.trigger('loginRegistrationView:login', {
                'method': 'email',
                'result': isSuccess ? 'success' : 'fail'
            });
        } else {
            this.trigger('loginRegistrationView:registration', {
                'method': 'email',
                'result': isSuccess ? 'success' : 'fail'
            });
        }
    }
});
