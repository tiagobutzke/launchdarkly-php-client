var LoginRegistrationView = Backbone.View.extend({
    initialize: function () {
        console.log('LoginRegistrationView.initialize ', this.cid);
        _.bindAll(this);
    },

    render: function() {
        this.$('.modal-content').load(Routing.generate('login'), function(){
            this.$el.modal();
        }.bind(this));

        return this;
    },

    events: {
        'click .register-link': '_loadRegistrationFormIntoLoginModal',
        'click .login-link': '_loadLoginFormIntoModal',
        'submit .login-form': '_handingSubmitOfLoginForm',
        'submit .registration-form': '_handingSubmitOfRegistrationForm',
        'click .modal-close-button': '_closeLoginRegistrationOverlay'
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    },

    _loadRegistrationFormIntoLoginModal: function() {
        this.$('.modal-content').load(Routing.generate('customer.create'));
    },

    _loadLoginFormIntoModal: function() {
        this.$('.modal-content').load(Routing.generate('login'));
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
