var CartItemView = Backbone.View.extend({
    tagName: 'tr',
    initialize: function() {
        this.template = _.template($('#template-cart-item').html());
        this.listenTo(this.model, "change", this.render);
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));

        return this;
    }
});

var CartView = Backbone.View.extend({
    events: {
        'change #order-delivery-time': 'updateDeliveryTime',
        'change #order-delivery-date': 'updateDeliveryTime'
    },
    initialize: function () {
        _.bindAll(this, 'renderNewItem', 'renderSubTotal', 'disableCart', 'enableCart', 'initListener');

        this.subViews = [];

        this.template = _.template($('#template-cart').html());
        this.templateSubTotal = _.template($('#template-cart-subtotal').html());

        this.vendor_id = this.$el.data().vendor_id;

        this.initListener();
    },

    remove: function() {
        _.invoke(this.subViews, 'remove');
        Backbone.View.prototype.remove.apply(this, arguments);
    },

    initListener: function () {
        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'cart:dirty', this.disableCart, this);
        this.listenTo(vendorCart, 'cart:ready', this.enableCart, this);
        this.listenTo(vendorCart, 'change', this.renderSubTotal);
        this.listenTo(vendorCart, 'change', this.renderProducts, this);
        this.listenTo(vendorCart, 'change:orderTime', this.renderTimePicker, this);
        this.listenTo(vendorCart.products, 'change', this.renderProducts, this);
        this.listenTo(vendorCart.products, 'add', this.renderNewItem, this);

    },

    disableCart: function() {
        this.$el.css({ opacity: 0.5 });
    },

    enableCart: function() {
        this.$el.css({ opacity: 1 });
    },

    render: function() {
        this.$el.html(this.template(this.model.getCart(this.vendor_id).attributes));
        this.renderSubTotal();

        this.renderProducts();
        this.renderTimePicker();
        this._makeCartAndMenuSticky();
        return this;
    },

    renderSubTotal: function () {
        this.$('.desktop-cart__order__subtotal__container').html(
            this.templateSubTotal(this.model.getCart(this.vendor_id).attributes)
        );

        return this;
    },

    renderProducts: function () {
        _.invoke(this.subViews, 'remove');
        this.subViews.length = 0;
        this.model.getCart(this.vendor_id).products.each(this.renderNewItem);
    },

    renderNewItem: function(item) {
        var view = new CartItemView({
            model: item
        });
        this.subViews.push(view);

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
            function(){ return $menuCache.offset().top + 140; },
            function(){ return $menuCache.offset().top + $menuCache.height(); }
        );
    },

    _toggleContainerVisibility: function() {
        var $productsContainer = this.$('.desktop-cart__products'),
            $cartMsg = this.$('.desktop-cart_order__message'),
            cartEmpty = this.model.vendorCart.get(this.vendor_id).products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    },

    updateDeliveryTime: function() {
        var time = this.$('#order-delivery-time').val().split(':'),
            date = this.$('#order-delivery-date').val().split('-'),
            datetime = new Date(date[0], date[1] - 1, date[2], time[0], time[1]);

        this.model.getCart(this.vendor_id).set('orderTime', datetime);
    },

    renderTimePicker: function() {
        var date = this.model.getCart(this.vendor_id).get('orderTime');

        if (_.isDate(date)) {
            this.$('#order-delivery-date').val(date.toISOString().split('T')[0]);
            this.$('#order-delivery-time').val(date.toTimeString().substring(0, 5));
        }
    }
});
