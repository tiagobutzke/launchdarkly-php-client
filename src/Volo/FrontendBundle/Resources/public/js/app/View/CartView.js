var CartToppingView = Backbone.View.extend({
    tagName: 'span',
    className: 'summary__item__extra',

    initialize: function() {
        this.template = _.template($('#template-cart-summary-extra-item').html());
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    }
});

var CartItemView = Backbone.View.extend({
    tagName: 'tr',
    className: 'cart__item',
    events: {
        'click .summary__item__name': '_editItem',
        'click .summary__item__price-wrap': '_editItem',
        'click .summary__item__sign': '_editItem',
        'click .summary__item__quantity-wrap': '_editItem',
        'click .summary__item__remove': '_removeItem',
        'click .summary__item__minus': '_decreaseQuantity',
        'click .summary__item__plus': '_increaseQuantity'
    },

    initialize: function(options) {
        _.bindAll(this);
        this.template = _.template($('#template-cart-item').html());
        this.cartModel = options.cartModel;
        this.vendorId = options.vendorId;
        this.listenTo(this.model.toppings, 'change', this.render);
        this.listenTo(this.model, 'change:quantity', this._updateItemQuantity);
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this._renderToppingViews(this.model.get('toppings'));
        this._toggleMinusAvailabilty(this.model.get('quantity'));
        this._toggleRemoveAvailabilty(this.model.get('quantity'));

        return this;
    },

    _renderToppingViews: function(toppings) {
        _.each(toppings, this._renderToppingView, this);
    },

    _renderToppingView: function(topping) {
        var view = new CartToppingView({
            model: new ToppingModel(topping)
        });

        this.$('.summary__extra__items').append(view.render().el);
    },

    _editItem: function() {
        var menuItemData = this._getMenuItemData(),
            menuToppings = this._getMenuItemToppings(menuItemData),
            allToppingsWithSelection = this._getAllToppingsWithSelection(this.model.toppings.toJSON(), menuToppings);

        var clone = new CartItemModel(this.model.toJSON());
        clone.toppings = new ToppingCollection(allToppingsWithSelection);

        var view = new ToppingsView({
            el: '.modal-dialogs',
            model: clone,
            cartModel: this.cartModel,
            vendorId: this.vendorId,
            productToUpdate: this.model
        });

        view.render(); //render dialog
        $('#choices-toppings-modal').modal(); //show dialog
    },

    _removeItem: function() {
        this.cartModel.getCart(this.vendorId).removeItem(this.model);
    },

    _decreaseQuantity: function() {
        if (this.model.get('quantity') > 1) {
            this.cartModel.getCart(this.vendorId).increaseQuantity(this.model, -1);
        }
    },

    _updateItemQuantity: function() {
        this.$('.summary__item__quantity-wrap').text(VOLO.formatNumber(this.model.get('quantity')));
    },

    _increaseQuantity: function() {
        this.cartModel.getCart(this.vendorId).increaseQuantity(this.model, 1);
    },

    _toggleMinusAvailabilty: function(quantity) {
        this.$('.summary__item__minus').toggleClass('summary__item__icon__disabled', quantity < 2);
    },

    _toggleRemoveAvailabilty: function(quantity) {
        this.$('.icon-cancel-circled').toggleClass('hide', quantity > 1);
    },

    _getAllToppingsWithSelection: function(cartToppings, menuToppings) {
        var menuToppingsClone = _.cloneDeep(menuToppings);
        return _.each(menuToppingsClone, function(menuTopping) {
            _.each(menuTopping.options, function(option) {
                if (_.findWhere(cartToppings, {id: option.id})) {
                    option.selected = true;
                }
            });

            return menuTopping;
        });
    },

    _getMenuItemToppings: function(menuItemData) {
        return menuItemData.product_variations[0].toppings;
    },

    _getMenuItemData: function() {
        var $menuItems = $('.menu__item'),
            variationId = this.model.get('product_variation_id'),
            menuItem;

        menuItem = _.find($menuItems, function(menuItem) {
            var productVariations = $(menuItem).data().object.product_variations,
                productVariation = productVariations ? productVariations[0] : {};

            return productVariation.id === variationId;
        }, this);

        return menuItem ? $(menuItem).data().object : null;
    }
});

var CartView = Backbone.View.extend({
    initialize: function (options) {
        console.log('CartView.initialize ', this.cid);
        _.bindAll(this);

        this.subViews = [];
        this.timePickerView = null;
        this.locationModel = options.locationModel;

        this.template = _.template($('#template-cart').html());
        this.templateSubTotal = _.template($('#template-cart-subtotal').html());

        this.vendor_id = this.$el.data().vendor_id;
        this.model.getCart(this.vendor_id).set('minimum_order_amount', this.$el.data().minimum_order_amount);

        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.domObjects.$menuMain = options.$menuMain;
        this.$window = options.$window;

        // margin of the menu height from the bottom edge of the window
        this.cartBottomMargin = VOLO.configuration.cartBottomMargin;
        this.itemsOverflowingClassName = VOLO.configuration.itemsOverflowingClassName;
        this._spinner = new Spinner();

        this.initListener();

        // initializing cart sticking behaviour
        this.stickOnTopCart = new StickOnTop({
            $container: this.$el,
            noStickyBreakPoint: 800,
            stickOnTopValueGetter: function() {
                return this.domObjects.$header.outerHeight();
            }.bind(this),
            startingPointGetter: function() {
                return this.$el.offset().top;
            }.bind(this),
            isActiveGetter: function() {
                return !$('body').hasClass('checkout-page');
            }.bind(this),
            endPointGetter: function() {
                return this.domObjects.$menuMain.offset().top + this.domObjects.$menuMain.outerHeight();
            }.bind(this)
        });
    },

    // attaching cart resize also to items scroll to avoid a bug not triggering resize
    // when scrolling page from summary list

    events: {
        'scroll .checkout__summary' : '_updateCartHeight',
        'click .btn-below-minimum-amount': '_showBelowMinimumAmountMsg',
        'click .btn-checkout': '_handleCartSubmit',
        'click .mobile-close__cart' : '_hideMobileCart'
    },


    _hideMobileCart: function() {
        this.$el.addClass('mobile-cart__hidden');
    },

    unbind: function() {
        // unbinding cart sticking behaviour
        this.stickOnTopCart.remove();
        // unbinding cart height resize behaviour
        this.$window.off('resize', this._updateCartHeight).off('scroll', this._updateCartHeight);

        if (_.isObject(this.timePickerView)) {
            this.timePickerView.unbind();
            this.timePickerView.remove();
        }

        _.invoke(this.subViews, 'unbind');
        _.invoke(this.subViews, 'remove');
        this.stopListening();
        this.undelegateEvents();
        this.domObjects = {};

        if (_.isObject(this.vendorGeocodingSubView)) {
            this.vendorGeocodingSubView.unbind();
        }
    },

    initListener: function () {
        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'cart:dirty', this.disableCart, this);
        this.listenTo(vendorCart, 'cart:ready', this.enableCart, this);
        this.listenTo(vendorCart, 'change', this.renderSubTotal);
        this.listenTo(vendorCart, 'change:order_time', this.renderTimePicker, this);
        this.listenTo(vendorCart.products, 'update', this.renderProducts, this);
        this.listenTo(vendorCart.products, 'update', this._toggleContainerVisibility, this);
        this.listenTo(vendorCart.products, 'update', this._updateCartIcon, this);
        this.listenTo(vendorCart.products, 'change', this._updateCartIcon, this);
        this.listenTo(vendorCart, 'cart:ready', this.render, this);
        this.listenTo(vendorCart, 'update', this.render, this);

        // initializing cart height resize behaviour
        this.$window.off('resize', this._updateCartHeight).off('scroll', this._updateCartHeight);
        this.$window.on('resize', this._updateCartHeight).on('scroll', this._updateCartHeight);

        this._initializeMobileCartIcon();
    },

    _initializeMobileCartIcon: function() {
        //listening on cart icon in header
        var $header = this.domObjects.$header,
            $el = this.$el;
        if ($header) {
            $header.find('.header__cart').click(function() {
                $el.removeClass('mobile-cart__hidden');
                window.blazy.revalidate();
            }.bind(this));
        }
    },

    _updateCartIcon: function() {
        var productsCount = this.model.getCart(this.vendor_id).getProductsCount(),
            $header = this.domObjects.$header,
            productCounter = $header ? $header.find('.header__cart__products__count') : null;

        if (productCounter) {
            productCounter.text(productsCount);
            this._animateAddToCart(productCounter);
        }
    },

    _animateAddToCart: function(counter) {
        var $counter = $(counter);
        if (!$counter) {
            return;
        }

        var animationLength = 100,
            originalWidth = 23,
            originalHeight = 23,
            sizeIncrease = 3;

        counter.animate({
            width: originalWidth + sizeIncrease + 'px',
            height: originalHeight + sizeIncrease + 'px',
            'line-height': originalHeight + sizeIncrease + 'px'
        }, animationLength, function () {
            counter.animate({
                width: originalWidth + 'px',
                height: originalHeight + 'px',
                'line-height': originalHeight + 'px'
            }, animationLength);
        });
    },

    disableCart: function() {
        this.$('.desktop-cart__footer').toggleClass('disabled', true);
        this.$('.btn-checkout').toggleClass('disabled', true);
        this._spinner.spin(this.$('.desktop-cart__footer')[0]);
    },

    enableCart: function() {
        this.$('.desktop-cart__footer').toggleClass('disabled', false);
        this.$('.btn-checkout').toggleClass('disabled', false);
        this._spinner.stop();
    },

    _handleCartSubmit: function() {
        return !this.$('.btn-checkout').hasClass('disabled');
    },

    render: function() {
        console.log('CartView:render');
        this.$el.html(this.template(this.model.getCart(this.vendor_id).attributes));
        this.renderSubTotal();
        this.renderProducts();
        this.renderTimePicker();

        this._toggleContainerVisibility();
        this._updateCartIcon();
        // recalculating cart scrolling position, should be done as last thing
        this.stickOnTopCart.init(this.$('.desktop-cart-container'));
        this._updateCartHeight();

        if (_.isObject(this.vendorGeocodingSubView)) {
            this.vendorGeocodingSubView.unbind();
        }
        this.vendorGeocodingSubView = new VendorGeocodingView({
            el: this.$('.vendor__geocoding__tool-box'),
            geocodingService: new GeocodingService(VOLO.configuration.locale.split('_')[1]),
            model: this.model.getCart(this.vendor_id),
            locationModel: this.locationModel
        });

        return this;
    },

    performDeliverableCheck: function () {
        this.vendorGeocodingSubView.performDeliverableCheck();
    },

    renderSubTotal: function () {
        this.$('.desktop-cart__order__subtotal__container').html(
            this.templateSubTotal(this.model.getCart(this.vendor_id).attributes)
        );

        return this;
    },

    renderProducts: function () {
        console.log('CartView renderProducts ', this.cid);
        _.invoke(this.subViews, 'unbind');
        _.invoke(this.subViews, 'remove');
        this.subViews.length = 0;
        this.model.getCart(this.vendor_id).products.each(this.renderNewItem);
    },

    renderNewItem: function(item) {
        var view = new CartItemView({
            model: item,
            cartModel: this.model,
            vendorId: this.vendor_id
        });
        this.subViews.push(view);

        this.$('.desktop-cart__products').append(view.render().el);

        // recalculating cart scrolling position
        this.stickOnTopCart.updateCoordinates();
        this._updateCartHeight();
    },

    _updateCartHeight: function () {
        var $checkoutSummary = this.$('.checkout__summary'),
            fixedCartElementsHeight = this.$('.desktop-cart__header').outerHeight() + this.$('.desktop-cart__footer').outerHeight();

        // if cart is sticking then adjust the product list max height
        if (this.$el.hasClass(this.stickOnTopCart.stickingOnTopClass)) {
            $checkoutSummary.css({
                'max-height': (this.$window.outerHeight() - this.domObjects.$header.outerHeight() - fixedCartElementsHeight - this.cartBottomMargin) + 'px'
            });
        // if not remove all adjusting
        } else {
            $checkoutSummary.css({
                'max-height': (this.$window.outerHeight() - (this.$el.offset().top - this.$window.scrollTop()) - fixedCartElementsHeight - this.cartBottomMargin) + 'px'
            });
        }

        // adding css styling in case of scrolling of summary list
        if ($checkoutSummary.find('.summary__items').outerHeight() > $checkoutSummary.outerHeight()) {
            $checkoutSummary.addClass(this.itemsOverflowingClassName);
        } else {
            $checkoutSummary.removeClass(this.itemsOverflowingClassName);
        }
    },

    _toggleContainerVisibility: function() {
        var $productsContainer = this.$('.desktop-cart__products'),
            $cartMsg = this.$('.desktop-cart_order__message'),
            cartEmpty = this.model.getCart(this.vendor_id).products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    },

    renderTimePicker: function() {
        if (_.isObject(this.timePickerView)) {
            this.timePickerView.unbind();
        }

        this.timePickerView = new TimePickerView({
            el: '.desktop-cart__time',
            model: this.model
        });

        this.timePickerView.render();
    },

    _showBelowMinimumAmountMsg: function () {
        this.$('.error-below-minimum-amount').removeClass('hide');
    }
});

var VendorCartIconView = Backbone.View.extend({
    events: {
        'click' : '_gotoMenuPage'
    },

    initialize: function() {
        _.bindAll(this);
    },

    _gotoMenuPage: function() {
        var vendorId = this.$el.data().vendor_id + '';
        if (vendorId.length > 0) {
            Turbolinks.visit(Routing.generate('vendor_by_id', {id: vendorId}));
        }
    },

    unbind: function() {
        this.undelegateEvents();
    },

    render: function() {
        return this;
    }
});
