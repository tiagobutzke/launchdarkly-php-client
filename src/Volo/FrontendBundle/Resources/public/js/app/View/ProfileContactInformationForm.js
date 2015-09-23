var VOLO = VOLO || {};

VOLO.ProfileContactInformationForm = Backbone.View.extend({
    events: {
        'submit': '_hideSuccessMessage'
    },

    initialize: function() {
        _.bindAll(this);

        this.contactInformationForm = new VOLO.ContactInformationForm({
            el: this.el,
            model: this.model
        });
        this.listenTo(this.model, 'change', this.render);
        this.listenTo(this.model, 'customer:saved', this._showSuccessMessage);
    },

    unbind: function() {
        this.contactInformationForm.unbind();

        this.stopListening();
        this.undelegateEvents();
    },

    render: function() {
        this.contactInformationForm.fillUpForm();

        return this;
    },

    _hideSuccessMessage: function() {
        this.$('.form__success-message').addClass('hide');
    },

    _showSuccessMessage: function() {
        this.$('.form__success-message').removeClass('hide');
    }
});
