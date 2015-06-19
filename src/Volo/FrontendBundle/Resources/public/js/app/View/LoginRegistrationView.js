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
            this._loginValidationView = new ValidationView({
                el: this.$('.login-form'),
                constraints: this._loginConstraints
            });
        }.bind(this));

        return this;
    },

    renderRegistration: function(data) {
        this.$('.modal-content').load(Routing.generate('customer.create'), function(){
            this.$el.modal();
            this._registerValidationView = new ValidationView({
                el: this.$('.registration-form'),
                constraints: this._registrationConstraints
            });

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
        if (this._loginValidationView) {
            this._loginValidationView.unbind();
        }
        if (this._registerValidationView) {
            this._registerValidationView.unbind();
        }

        this.stopListening();
        this.undelegateEvents();
    },

    _loadRegistrationFormIntoLoginModal: function() {
        this.$('.modal-content').load(Routing.generate('customer.create'), function() {
            this._registerValidationView = new ValidationView({
                el: this.$('.registration-form'),
                constraints: this._registrationConstraints
            });
        }.bind(this));
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
                Turbolinks.visit(window.location.href);
            },
            error: function (data) {
                spinner.stop();
                $(target).removeClass('modal-content--loading');
                $modalContent.html(data.responseText);
                $form.find('button').prop("disabled", false);
            }
        });
    }
});
