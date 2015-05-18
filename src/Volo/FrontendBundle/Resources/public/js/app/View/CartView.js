var CartItemView = Backbone.View.extend({
    initialize: function() {
        this.template = _.template($('#template-cart-item').html());
    },
    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    }
});

var CartView = Backbone.View.extend({
    initialize: function (options) {
        this.template = _.template($('#template-cart').html());

        _.bindAll(this, 'renderNewItem');

        this.vendor_id = options.vendor_id;
        this.model.vendorCart.get(this.vendor_id).products.on('add', this.renderNewItem, this);

        this.listenTo(this.model, "change", this.render);
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    renderNewItem: function(item) {
        var view = new CartItemView({
            model: item
        });

        this.$('.desktop-cart__products').append(view.render().el);
        this._toggleContainerVisibility();
    },

    _toggleContainerVisibility: function() {
        var $productsContainer = this.$('.desktop-cart__products'),
            $cartMsg = this.$('.desktop-cart_order__message'),
            cartEmpty = this.model.vendorCart.get(this.vendor_id).products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    }
});
