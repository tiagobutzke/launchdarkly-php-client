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
    tagName: 'tbody',
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
        this.listenTo(this.model.toppings, 'update', this.render);
        this.listenTo(this.model, 'change:quantity', this._updateItemQuantity);
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this._renderToppingViews(this.model.toppings.toJSON());
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
        var variationId = this.model.get('product_variation_id'),
            menuItemData = $('[data-variation-id="' + variationId +'"]').data('object'),
            menuToppings = menuItemData.product_variations[0].toppings;

        var clone = this.model.clone();
        clone.transformToppingsToMenuFormat(menuToppings);

        var view = new ToppingsView({
            el: '.modal-dialogs',
            model: clone,
            cartModel: this.cartModel,
            vendorId: this.vendorId,
            productToUpdate: this.model,
            gtmService: this.gtmService
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

    unbind: function() {
        if (_.isObject(this.gtmService)) {
            this.gtmService.unbind();
        }
    }
});

var CartView = Backbone.View.extend({
    initialize: function (options) {
        console.log('CartView.initialize ', this.cid);
        _.bindAll(this);

        this.subViews = [];
        this.locationModel = options.locationModel;

        this.template = _.template($('#template-cart').html());
        this.templateSubTotal = _.template($('#template-cart-subtotal').html());
        this.templateCheckoutButton = _.template($('#template-cart-checkout-button').html());

        this.vendor_id = this.$('.desktop-cart').data().vendor_id;
        this.model.getCart(this.vendor_id).set('minimum_order_amount', this.$('.desktop-cart').data().minimum_order_amount);
        this.model.getCart(this.vendor_id).set('location', {
            location_type: this.locationModel.defaults.location_type,
            latitude:  this.locationModel.get('latitude'),
            longitude: this.locationModel.get('longitude')
        });

        this.timePickerView = new TimePickerView({
            model: this.model,
            vendor_id: this.vendor_id,
            values: options.timePickerValues
        });
        this.vendorGeocodingView = options.vendorGeocodingView;

        this.domObjects = {};
        this.domObjects.$header = options.$header;
        this.domObjects.$menuMain = options.$menuMain;
        this.domObjects.$iosBanner = options.$iosBanner;
        this.domObjects.$body = options.$body,
        this.$window = options.$window;
        this.smallScreenMaxSize = options.smallScreenMaxSize;

        // margin of the menu height from the bottom edge of the window
        this.cartBottomMargin = VOLO.configuration.cartBottomMargin;
        this.itemsOverflowingClassName = VOLO.configuration.itemsOverflowingClassName;
        this._spinner = new Spinner();

        // initializing cart sticking and resizing behaviour
        this.boundUpdateCartHeight = this._updateCartHeight.bind(this);
        this.stickOnTopCartContainerSelector = '.desktop-cart';
        this.stickOnTopCartTargetSelector = '.desktop-cart-container';
        this.stickOnTopCart = new StickOnTop({
            $container: this.$(this.stickOnTopCartContainerSelector),
            noStickyBreakPoint: this.smallScreenMaxSize,
            stickOnTopValueGetter: function() {
                var stickOnTopValue;
                if (this.domObjects.$body.hasClass('show-ios-smart-banner')) {
                    stickOnTopValue = this.domObjects.$header.outerHeight() + this.domObjects.$iosBanner.outerHeight();
                } else {
                    stickOnTopValue = this.domObjects.$header.outerHeight();
                }

                var $postalCodeBar = this.domObjects.$body.find('.menu__postal-code-bar');
                if (!$postalCodeBar.hasClass('hidden')) {
                    stickOnTopValue += $postalCodeBar.outerHeight();
                }

                return stickOnTopValue;
            }.bind(this),
            startingPointGetter: function() {
                return this.$(this.stickOnTopCartTargetSelector).offset().top;
            }.bind(this),
            isActiveGetter: function() {
                return !$('body').hasClass('checkout');
            }.bind(this),
            endPointGetter: function() {
                return this.domObjects.$menuMain.offset().top + this.domObjects.$menuMain.outerHeight();
            }.bind(this)
        });

        this.CartItemViewClass = CartItemView;

        this.initListener();
    },

    setGtmService: function(gtmService) {
        this.gtmService = gtmService;

        _.each(this.subViews, function(cartItemView) {
            cartItemView.gtmService = gtmService;
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
        this.$('.desktop-cart').addClass('mobile-cart__hidden');
        $('body').removeClass('body-cart-fix');
    },

    _showMobileCart: function() {
        console.log(arguments);
        this.$('.desktop-cart').removeClass('mobile-cart__hidden');
        $('body').addClass('body-cart-fix');
        window.blazy.revalidate();
    },

    unbind: function() {
        // unbinding cart sticking behaviour
        this.stickOnTopCart.remove();
        // unbinding cart height resize behaviour
        this.$window.off('resize', this.boundUpdateCartHeight).off('scroll', this.boundUpdateCartHeight);

        if (_.isObject(this.timePickerView)) {
            this.timePickerView.unbind();
            this.timePickerView.remove();
        }

        _.invoke(this.subViews, 'unbind');
        _.invoke(this.subViews, 'remove');
        this.stopListening();
        this.undelegateEvents();
        this.domObjects = {};

        this.vendorGeocodingView && this.vendorGeocodingView.unbind();

        if (_.isObject(this.gtmService)) {
            this.gtmService.unbind();
        }
    },

    initListener: function () {
        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'cart:dirty', this.disableCart, this);

        this.listenTo(vendorCart, 'cart:ready', this.enableCart, this);
        this.listenTo(vendorCart, 'cart:ready', this.renderCheckoutButton, this);
        this.listenTo(vendorCart, 'cart:ready', this.stopSpinner, this);

        this.listenTo(vendorCart, 'cart:error', this.handleVouchersErrors, this);
        this.listenTo(vendorCart, 'cart:error', this.stopSpinner, this);
        this.listenTo(vendorCart, 'cart:error', this.renderProducts, this);

        this.listenTo(vendorCart, 'change', this.renderSubTotal);
        this.listenTo(vendorCart.products, 'update', this.renderProducts, this);
        this.listenTo(vendorCart.products, 'update', this._toggleContainerVisibility, this);
        this.listenTo(vendorCart.products, 'update', this._updateCartIcon, this);
        this.listenTo(vendorCart.products, 'change', this._updateCartIcon, this);
        this.listenTo(vendorCart, 'update', this.render, this);

        // initializing cart height resize behaviour
        this.$window.off('resize', this.boundUpdateCartHeight).on('resize', this.boundUpdateCartHeight);
        this.$window.off('scroll', this.boundUpdateCartHeight).on('scroll', this.boundUpdateCartHeight);

        this.listenTo(this.vendorGeocodingView, 'vendor_geocoding_view:postcode_toggle', this._updateStickingOnTopCoordinates);
        this._initializeMobileCartIcon();
    },

    _initializeMobileCartIcon: function() {
        //listening on cart icon in header
        var $header = this.domObjects.$header;
        $header && $header.find('.header__cart').click(this._showMobileCart);
    },

    _updateCartIcon: function() {
        var productsCount = this.model.getCart(this.vendor_id).getProductsCount(),
            $header = this.domObjects.$header,
            $productCounter = $header ? $header.find('.header__cart__products__count') : null;

        if ($productCounter) {
            $productCounter.toggleClass('hidden', productsCount < 1);
            $productCounter.text(Math.min(productsCount, 99));
            this._animateAddToCart($productCounter);
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
    },

    stopSpinner: function() {
        this._spinner.stop();
    },

    _handleCartSubmit: function() {
        var vendorCode = this.$('.btn-checkout').data('vendor-code');

        if (!this.$('.btn-checkout').hasClass('disabled')) {
            Turbolinks.visit(Routing.generate('checkout_payment', {
                vendorCode: vendorCode
            }));
        }

        return false;
    },

    render: function() {
        console.log('CartView:render');
        this.$('.desktop-cart').html(this.template(this.model.getCart(this.vendor_id).attributes));
        this.renderSubTotal();
        this.renderCheckoutButton();
        this.renderProducts();
        this.renderTimePicker();

        this._toggleContainerVisibility();
        this._updateCartIcon();

        // recalculating cart scrolling position, should be done as last thing
        this.stickOnTopCart.init(this.$(this.stickOnTopCartTargetSelector));
        this._updateCartHeight();

        return this;
    },

    setZipCode: function (zipCode) {
        this.vendorGeocodingView.setZipCode(zipCode);
    },

    _updateStickingOnTopCoordinates: function() {
        this.stickOnTopCart.updateCoordinates();
    },

    renderCheckoutButton: function() {
        this.$('.desktop-cart__order__checkout_button_container').html(
            this.templateCheckoutButton(this.model.getCart(this.vendor_id).attributes)
        );
    },

    renderSubTotal: function () {
        this.$('.desktop-cart__order__subtotal-container').html(
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
        this.setGtmService(this.gtmService);

        // recalculating cart scrolling position
        this.stickOnTopCart.updateCoordinates();
        this._updateCartHeight();
    },

    renderNewItem: function(item) {
        var view = new this.CartItemViewClass({
            model: item,
            cartModel: this.model,
            vendorId: this.vendor_id
        });
        this.subViews.push(view);

        this.$('.desktop-cart__products').append(view.render().el);
    },

    _updateCartHeight: function () {
        var $checkoutSummary = this.$('.checkout__summary'),
            $stickOnTopCartContainer,
            isCartSticky,
            additionalElementsHeight,
            fixedCartElementsHeight;

        if (this.isBelowMediumScreen()) {
            // disabling cart resizing on small screens
            $checkoutSummary.css({
                'max-height': ''
            });
            $checkoutSummary.removeClass(this.itemsOverflowingClassName);

            return;
        }

        $stickOnTopCartContainer = this.$(this.stickOnTopCartContainerSelector);
        isCartSticky = $stickOnTopCartContainer.hasClass(this.stickOnTopCart.stickingOnTopClass);
        additionalElementsHeight = isCartSticky ? 0 :
            this.$(this.stickOnTopCartTargetSelector).offset().top - $stickOnTopCartContainer.offset().top;
        fixedCartElementsHeight = additionalElementsHeight + this.$('.desktop-cart__header').outerHeight() +
            this.$('.desktop-cart__footer').outerHeight();

        if (isCartSticky) {
            // reduced window size cart resizing when not sticking
            $checkoutSummary.css({
                'max-height': (this.$window.outerHeight() - this.domObjects.$header.outerHeight() -
                fixedCartElementsHeight - this.cartBottomMargin) + 'px'
            });

        } else {
            // full window size cart resizing when sticking
            $checkoutSummary.css({
                'max-height': (this.$window.outerHeight() - ($stickOnTopCartContainer.offset().top -
                this.$window.scrollTop()) - fixedCartElementsHeight - this.cartBottomMargin) + 'px'
            });

        }

        // adding css styling in case of scrolling of summary list
        $checkoutSummary.toggleClass(this.itemsOverflowingClassName,
            this.$('.summary__items').outerHeight() > $checkoutSummary.outerHeight()
        );
    },

    _toggleContainerVisibility: function() {
        var $productsContainer = this.$('.desktop-cart__products'),
            $cartMsg = this.$('.desktop-cart_order__message'),
                cartEmpty = this.model.getCart(this.vendor_id).products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    },

    renderTimePicker: function() {
        this.timePickerView.setElement(this.$('.cart__time-picker'));
        this.timePickerView.render();
    },

    _showBelowMinimumAmountMsg: function () {
        this.$('.desktop-cart__error__below-minimum-amount').removeClass('hide');
    },

    handleVouchersErrors: function (data) {
        var supportedErrors = [
            'ApiVoucherInactiveException',
            'ApiVoucherDoesNotExistException',
            'ApiVoucherInvalidVendorException',
            'ApiVoucherUsageExceededException',
            'ApiVoucherTemporaryClosedException',
            'ApiVoucherCustomerRequiredException',
            'ApiVoucherInvalidPaymentTypeException',
            'ApiVoucherNotValidForCustomerException',
            'ApiVoucherOrderAmountExceededException',
            'ApiVoucherProductCategoryUsageException',
            'ApiVoucherIsNotValidForPlatformException',
            'ApiVoucherLimitedToNewCustomersException',
            'ApiVoucherOrderAmountNotReachedException',
            'ApiVoucherUsagePerCustomerExceededException',
            'ApiVoucherTemporaryClosedWithScheduleException',
            'ApiVoucherPromotionOrderAmountNotReachedException',
            'ApiVoucherInvalidPaymentTypeButAnotherOneIsAvailableException'
        ];

        if (_.isObject(data) && _.indexOf(supportedErrors, _.get(data, 'error.errors.exception_type')) !== -1) {
            this.model.getCart(this.vendor_id).set('voucher', null);
            _.defer(this.model.getCart(this.vendor_id).updateCart);
        }
    }
});

_.extend(CartView.prototype, VOLO.DetectScreenSizeMixin);

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

    _reloadCartIconValues: function () {
        var that = this;
        $.get(Routing.generate('default_cart_values'))
            .done(function (response) {
                that.$el.data('vendor_id', response.vendor_id);
                that.$('.header__cart__products__count').html(Math.min(response.products_count, 99));
            });
    },

    unbind: function() {
        this.undelegateEvents();
    },

    render: function() {
        this._reloadCartIconValues();
        return this;
    }
});
