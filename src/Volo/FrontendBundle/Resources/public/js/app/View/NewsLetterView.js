VOLO.NewsLetterView = Backbone.View.extend({
    events: {
        'submit .footer__subscribe__form': '_submitNewsLetterForm'
    },

    initialize: function() {
        _.bindAll(this);
    },

    _submitNewsLetterForm: function() {
        $.ajax({
            url: Routing.generate('newsletter.subscribe'),
            dataType: 'json',
            data: JSON.stringify(this.$el.serializeJSON()),
            method: 'POST',
            success: function () {
                this._showSignupSuccessMessage();
            }.bind(this),
            error: function() {
                console.log('error');
            }.bind(this)
        });

        return false;
    },

    _showSignupSuccessMessage: function() {
        this.$el.addClass('show-subscribe-message');
        this.$el.animate({
            height: $('.footer__subscribe__success-message').outerHeight()
        }, 500);
    }
});
