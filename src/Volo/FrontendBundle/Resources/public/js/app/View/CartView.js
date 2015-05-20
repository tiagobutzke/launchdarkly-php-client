var CartItemView = Backbone.View.extend({
    initialize: function() {
        this.template = _.template($('#template-cart-item').html());
        this.listenTo(this.model, "change", this.render);
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

        var cart = this.model.vendorCart.get(this.vendor_id);

        cart.products.on('add', this.render, this);

        this.listenTo(cart, 'cart:dirty', this.disableCart, this);
        this.listenTo(cart, 'cart:ready', this.enableCart, this);
        this.listenTo(this.model, 'change', this.render);
    },

    disableCart: function() {
        this.$el.css({ opacity: 0.5 });
    },

    enableCart: function() {
        this.$el.css({ opacity: 1 });
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        this.model.vendorCart.get(this.vendor_id).products.each(this.renderNewItem);

        this._makeCartAndMenuSticky();
        return this;
    },

    renderNewItem: function(item) {
        var view = new CartItemView({
            model: item
        });

        this.$('.desktop-cart__products').append(view.render().el);
        this._toggleContainerVisibility();
    },

    _makeCartAndMenuSticky: function() {
        var $menuCache = $('.menu'),
            $headerCache = $('.header');

        new StickOnTop(
            $('.desktop-cart-container'),
            $('.desktop-cart'),
            function(){ return $headerCache.height(); },
            function(){ return $menuCache.position().top; },
            function(){ return $menuCache.offset().top + $menuCache.height(); }
        );

        new StickOnTop(
            $('.menu__categories nav'),
            $('.menu__categories'),
            function(){ return $headerCache.height(); },
            function(){ return $menuCache.offset().top + 27; },
            function(){ return $menuCache.offset().top + $menuCache.height(); }
        );
    },

    _toggleContainerVisibility: function() {
        var $productsContainer = this.$('.desktop-cart__products'),
            $cartMsg = this.$('.desktop-cart_order__message'),
            cartEmpty = this.model.vendorCart.get(this.vendor_id).products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    }
});
