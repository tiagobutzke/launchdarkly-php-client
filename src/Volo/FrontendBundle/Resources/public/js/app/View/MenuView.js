var MenuView = Backbone.View.extend({
    initialize : function (options) {
        this.cartModel = options.cartModel;
        this.$header = options.$header;
        this.subViews = [];
        this.vendor_id = this.$el.data().vendor_id;

        _.each(this.$('.menu__item'), this.initSubViews, this);
    },

    remove: function() {
        _.invoke(this.subViews, 'remove');
        Backbone.View.prototype.remove.apply(this, arguments);
    },

    events: {
        'click .anchorNavigation': '_navigateToAnchor'
    },

    initSubViews: function(item) {
        var object = this.$(item).data().object;
        this.subViews.push(new MenuItemView({
            el: $(item),
            // @TODO: create ProductModel
            model: new Backbone.Model(object),
            cartModel: this.cartModel,
            vendor_id: this.vendor_id
        }));
    },

    _navigateToAnchor: function(event) {
        $('html, body').animate({
            scrollTop: this.$($.attr(event.target, 'href')).offset().top - this.$header.outerHeight()
        }, 500);

        return false;
    },
});

var MenuItemView = Backbone.View.extend({
    events: {
        'click .menu__item__add': 'addProduct'
    },
    initialize : function (options) {
        this.cartModel = options.cartModel;
        this.vendor_id = options.vendor_id;
    },
    addProduct: function() {
        this.cartModel.getCart(this.vendor_id).addItem(this.model.toJSON(), 1);
    }
});
