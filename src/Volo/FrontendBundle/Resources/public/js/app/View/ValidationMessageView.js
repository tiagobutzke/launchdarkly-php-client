var ValidationMessageView = Backbone.View.extend({
    tagName: 'span',
    className: 'error_msg',

    initialize: function() {
        this.template = _.template('<%= message %>');
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    }
});
