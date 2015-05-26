var MenuView = Backbone.View.extend({
    initialize : function (options) {
        this.cartModel = options.cartModel;
        this.$header = options.$header;
        this.subViews = [];
        this.vendor_id = this.$el.data().vendor_id;
        // @TODO: move this check in the model
        if (_.isUndefined(this.cartModel.vendorCart.get(this.vendor_id))) {
            this.cartModel.vendorCart.add({vendor_id: this.vendor_id});
        }

        _.each(this.$('.menu__item'), this.initSubViews, this);
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
