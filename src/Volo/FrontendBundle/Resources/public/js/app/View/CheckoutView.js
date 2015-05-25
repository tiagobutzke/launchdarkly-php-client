var CheckoutView = Backbone.View.extend({
    initialize: function (options) {
        _.bindAll(this, 'renderNewItem');

        this.subViews = [];

        this.template = _.template($('#template-cart').html());

        this.vendor_id = options.vendor_id;
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        this.model.getCart(this.vendor_id).products.each(this.renderNewItem);

        this.$('.desktop-cart__time').hide();
        this.$('.btn-checkout').hide();
        this.$('.desktop-cart_order__message').hide();
        return this;
    },

    remove: function() {
        _.invoke(this.subViews, 'remove');
        Backbone.View.prototype.remove.apply(this, arguments);
    },

    renderNewItem: function(item) {
        var view = new CartItemView({
            model: item
        });
        this.subViews.push(view);

        this.$('.desktop-cart__products').append(view.render().el);
    }
});
