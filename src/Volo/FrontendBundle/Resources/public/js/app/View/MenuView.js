var MenuView = Backbone.View.extend({
    initialize : function (options) {
        console.log('MenuView.initialize ', this.cid);
        this.cartModel = options.cartModel;

        this.domObjects = {};
        this.navigateToAnchorBuffer = 30;
        this.domObjects.$header = options.$header;

        this.subViews = [];
        this.vendor_id = this.$el.data().vendor_id;

        this.gtmService = options.gtmService || false;

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

    setGtmService: function (gtmService) {
        this.gtmService = gtmService;
        for (var i in this.subViews) {
            this.subViews[i].setGtmService(gtmService);
        }
    },

    // attaching navigation behaviour to menu links
    events: {
        'click .anchorNavigation': '_navigateToAnchor',
        'click .btn-allergy': 'showAllergyModal'
    },

    unbind: function() {
        console.log('MenuView.unbind', this.cid);
        // unbinding cart sticking behaviour
        this.stickOnTopMenu.remove();

        _.invoke(this.subViews, 'unbind');
        this.subViews.length = 0;

        this.stopListening();
        this.undelegateEvents();
        this.domObjects = {};
        this.gtmService.unbind();
    },

    initSubViews: function(item) {
        var object = this.$(item).data().object;
        this.subViews.push(new MenuItemView({
            el: $(item),
            model: new MenuItemModel(object),
            cartModel: this.cartModel,
            vendor_id: this.vendor_id,
            gtmService: this.gtmService
        }));
    },

    _navigateToAnchor: function(event) {
        $('html, body').animate({
            scrollTop:
                this.$($.attr(event.target, 'href')).offset().top +
                this.navigateToAnchorBuffer -
                this.domObjects.$header.outerHeight()
        }, 500);

        return false;
    },

    showAllergyModal: function() {
        this.$('#allergyModal').modal();
    }
});

var MenuItemView = Backbone.View.extend({
    events: {
        'click': 'addProduct'
    },

    initialize : function (options) {
        this.cartModel = options.cartModel;
        this.vendor_id = options.vendor_id;
        this.gtmService = options.gtmService;
    },

    setGtmService: function (gtmService) {
        this.gtmService = gtmService;
    },

    addProduct: function() {
        console.log('MenuItemView.addProduct ', this.cid);

        if (this.cartModel.getCart(this.vendor_id).isValid()) {
            if (this.model.showOrderDialog()) {
                this.createViewDialog();
            } else {
                var model = CartItemModel.createFromMenuItem(this.model.toJSON());
                this.gtmService.fireAddProduct(this.vendor_id, {
                    id: model.get('product_variation_id'),
                    name: model.get('name')
                });
                this.cartModel.getCart(this.vendor_id).addItem(model.toJSON(), 1);
            }
        }
    },

    createViewDialog: function() {
        var cartItemModel = CartItemModel.createFromMenuItem(this.model.toJSON());

        var view = new ToppingsView({
            el: '.modal-dialogs',
            model: cartItemModel,
            cartModel: this.cartModel,
            vendorId: this.vendor_id,
            gtmService: this.gtmService
        });

        view.render(); //render dialog
        $('#choices-toppings-modal').modal(); //show dialog
    },

    unbind: function() {
        console.log('MenuItemView.unbind', this.cid);
        this.stopListening();
        this.undelegateEvents();
    }
});
