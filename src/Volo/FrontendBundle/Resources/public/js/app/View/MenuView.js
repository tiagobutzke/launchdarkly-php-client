var MenuView = Backbone.View.extend({
    initialize : function (options) {
        this.cartModel = options.cartModel;

        this.domObjects = {};
        this.domObjects.$header = options.$header;

        this.subViews = [];
        this.vendor_id = this.$el.data().vendor_id;

        _.each(this.$('.menu__item'), this.initSubViews, this);

        // initializing menu height resize behaviour

        this.stickOnTopMenu = new StickOnTop({
            $container: this.$('.menu__categories'),
            stickOnTopValueGetter: function() {
                return this.domObjects.$header.outerHeight();
            }.bind(this),
            startingPointGetter: function() {
                var vendorLogo = this.$('.menu__vendor-logo'),
                    vendorLogoHeight = vendorLogo.length ? vendorLogo.outerHeight() : 0;

                return this.$el.offset().top + vendorLogoHeight;
            }.bind(this),
            endPointGetter: function() {
                return this.$el.offset().top + this.$el.outerHeight();
            }.bind(this)
        });
        this.stickOnTopMenu.init(this.$('.menu__categories nav'));
    },

    // attaching navigation behaviour to menu links
    events: {
        'click .anchorNavigation': '_navigateToAnchor'
    },

    remove: function() {
        // unbinding cart sticking behaviour
        this.stickOnTopMenu.remove();

        _.invoke(this.subViews, 'remove');
        Backbone.View.prototype.remove.apply(this, arguments);
        this.domObjects = {};
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
            scrollTop: this.$($.attr(event.target, 'href')).offset().top - this.domObjects.$header.outerHeight()
        }, 500);

        return false;
    }
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
