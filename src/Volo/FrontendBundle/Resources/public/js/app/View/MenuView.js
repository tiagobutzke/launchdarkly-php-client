var MenuView = Backbone.View.extend({
    initialize : function (options) {
        console.log('MenuView.initialize ', this.cid);
        this.cartModel = options.cartModel;

        this.domObjects = {};
        this.navigateToAnchorBuffer = 30;
        this.domObjects.$header = options.$header;
        this.domObjects.$postalCodeBar = options.$postalCodeBar;
        this.domObjects.$el = options.$el;

        this.subViews = [];
        this.vendor = this.$el.data().vendor;
        this.vendor.variant = this.$el.data().vendorIsOpen;

        this.gtmService = options.gtmService || null;

        _.each(this.$('.menu__item'), this.initSubViews, this);

        // initializing menu height resize behaviour
        this.vendorGeocodingView = options.vendorGeocodingView;
        this.listenTo(this.vendorGeocodingView, 'vendor_geocoding_view:postcode_toggle', this._updateStickingOnTopCoordinates);

        this.stickOnTopMenu = new StickOnTop({
            $container: this.$('.menu__categories'),
            stickOnTopValueGetter: function() {
                var $postalCodeBar = this.domObjects.$postalCodeBar;

                return this.domObjects.$header.outerHeight() +
                    ($postalCodeBar.hasClass('hidden') ? 0 : $postalCodeBar.outerHeight()) +
                    $('.top-banner:visible').outerHeight();
            }.bind(this),
            startingPointGetter: function() {
                var vendorLogo = this.$('.menu__categories__vendor-logo'),
                    vendorLogoHeight = vendorLogo.length ? vendorLogo.outerHeight() : 0;

                return this.$el.offset().top + vendorLogoHeight;
            }.bind(this),
            endPointGetter: function() {
                return this.$el.offset().top + this.$el.outerHeight();
            }.bind(this)
        });
        this.stickOnTopMenu.init(this.$('.menu__categories nav'));
        this._boundStickyUpdate = this.stickOnTopMenu.updateCoordinates.bind(this.stickOnTopMenu);
        this.$('.menu__categories__vendor-logo-img').off('load', this._boundStickyUpdate).on('load', this._boundStickyUpdate);
    },

    _updateStickingOnTopCoordinates: function () {
        this.stickOnTopMenu.updateCoordinates();
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
        'click .menu__icon-legend__link': 'showAllergyModal'
    },

    unbind: function() {
        console.log('MenuView.unbind', this.cid);
        // unbinding cart sticking behaviour

        this.$('.menu__categories__vendor-logo-img').off('load', this._boundStickyUpdate);
        this.stickOnTopMenu.remove();

        _.invoke(this.subViews, 'unbind');
        this.subViews.length = 0;

        this.stopListening();
        this.undelegateEvents();
        this.domObjects = {};
        this.vendorGeocodingView && this.vendorGeocodingView.unbind();

        if (_.isObject(this.gtmService)) {
            this.gtmService.unbind();
        }
    },

    initSubViews: function(item) {
        var object = this.$(item).data().object;
        this.subViews.push(new MenuItemView({
            el: $(item),
            model: new MenuItemModel(object),
            cartModel: this.cartModel,
            vendor: this.vendor,
            gtmService: this.gtmService
        }));
    },

    _navigateToAnchor: function(event) {
        var offset,
            targetTop;

        if (event.target) {
            offset = this.$($.attr(event.target, 'href')).offset();
            targetTop = _.get(offset, 'top');
        }

        if (!targetTop) {
            return false;
        }

        $('html, body').animate({
            scrollTop:
                targetTop +
                this.navigateToAnchorBuffer -
                this.domObjects.$header.outerHeight()
        }, VOLO.configuration.anchorScrollSpeed);

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
        this.vendor = options.vendor;
        this.gtmService = options.gtmService;
    },

    setGtmService: function (gtmService) {
        this.gtmService = gtmService;
    },

    addProduct: function() {
        var model, cart;

        console.log('MenuItemView.addProduct ', this.cid);

        if (this.cartModel.getCart(this.vendor.id).isValid()) {
            if (this.model.showOrderDialog()) {
                this.createViewDialog();
            } else {
                model = CartItemModel.createFromMenuItem(this.model.toJSON());
                cart = this.cartModel.getCart(this.vendor.id);

                this.listenToOnce(cart, 'cart:ready', function () {
                    this._fireAddToCartEvent(model.get('product_variation_id'));
                }.bind(this));

                cart.addItem(model);
            }
        }
    },

    createViewDialog: function() {
        var cartItemModel = CartItemModel.createFromMenuItem(this.model.toJSON());

        var view = new ToppingsView({
            el: '.modal-dialogs',
            model: cartItemModel,
            cartModel: this.cartModel,
            vendor: this.vendor,
            gtmService: this.gtmService
        });

        view.render(); //render dialog
        $('#choices-toppings-modal').modal(); //show dialog
    },

    unbind: function() {
        console.log('MenuItemView.unbind', this.cid);
        this.stopListening();
        this.undelegateEvents();
    },

    _fireAddToCartEvent: function(productVariationId) {
        var cart = this.cartModel.getCart(this.vendor.id),
            model = _.findWhere(cart.products.models, {attributes: {product_variation_id: productVariationId}});

        if (this.gtmService) {
            console.log('GTM:addProduct');
            this.gtmService.fireAddProduct(this.vendor.id, {
                id: model.get('product_variation_id'),
                name: model.get('name'),
                productPrice: model.get('total_price'),
                vendor: {
                    'name': this.vendor.name,
                    'id': this.vendor.code,
                    'category': this.vendor.category,
                    'variant': this.vendor.variant
                },
                cart: {
                    value: cart.get('total_value'),
                    contents: cart.getProductsIds().join(','),
                    quantity: cart.getProductsCount()
                },
                actionLocation: 'Restaurant Detail Page'
            });
        }
    }
});
