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
        this.queryParams = options.queryParams || {};
    },

    render: function() {
        this.$('.modal-content').load(Routing.generate('login', this.queryParams), function(){
            this.$el.modal();
            this._bindLoginView();
        }.bind(this));

        return this;
    },

    renderRegistration: function(data) {
        this.$('.modal-content').load(Routing.generate('customer.create'), function(){
            this.$el.modal();
            this._bindRegisterValidationView();

            if (data) {
                this.$('#first_name').val(data.first_name);
                this.$('#last_name').val(data.last_name);
                this.$('#email').val(data.email);
                this.$('#mobile_number').val(data.mobile_number);
            }
        }.bind(this));

        return this;
    },

    events: {
        'click .register-link': '_loadRegistrationFormIntoLoginModal',
        'click .login-link': 'render',
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

    _loadRegistrationFormIntoLoginModal: function() {
        this.$('.modal-content').load(Routing.generate('customer.create'), this._bindRegisterValidationView);
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
    },

    _closeLoginRegistrationOverlay: function() {
        this.$el.modal('hide');
    },

    _handleFormSubmit: function($form, $modalContent) {
        var target = document.getElementById('spinner-wrapper'),
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
                this._unbindLoginView();
                this._unbindRegisterValidationView();

                Turbolinks.visit(window.location.href);

                this._fireFormSubmit($form, true);
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
