var VOLO = VOLO || {};

VOLO.SpecialInstructionsTutorialView = Backbone.View.extend({
    initialize: function () {
        _.bindAll(this);
    },

    events: {
        'click .desktop-cart__special-instructions-tutorial__dismiss': '_hide'
    },

    _hide: function() {
        this.$el.hide();

        return $.ajax({
            url: Routing.generate('special_instructions_tutorial_dismiss'),
            type: 'PUT'
        });
    },

    unbind: function() {
        this.undelegateEvents();
    }
});
