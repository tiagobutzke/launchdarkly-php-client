var ToppingOptionCommentView = Backbone.View.extend({
    className: 'topping__header__comment',

    initialize: function() {
        this.template = _.template($('#template-topping-comment').html());
    },

    render: function() {
        var optionsVisible = this.model.areOptionsVisible();

        this.$el.html(this.template(this.model.toJSON()));
        this.$('#topping-comment-help-text').toggle(optionsVisible || this.model._getSelectedItems().length === 0);
        this.$('#topping-comment-select-more-text').toggle(!optionsVisible);
        this.$('#topping-comment-selected-items-list').toggle(!optionsVisible);

        return this;
    }
});

var ToppingOptionView = Backbone.View.extend({
    className: 'topping-option',

    initialize: function() {
        this.template = _.template($('#template-topping-option').html());
        this.$el.toggleClass('selected', this.model.isSelected());

        this.listenTo(this.model, 'change:selected', this._updateVisualization);
    },

    events: {
        'click': '_updateSelection'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    _updateVisualization: function() {
        this.$el.toggleClass('selected', this.model.isSelected());
    },

    _updateSelection: function() {
        this.trigger('toppingOption:updateSelection', this.model);
    }
});

var ToppingView = Backbone.View.extend({
    className: 'topping',

    initialize: function() {
        _.bindAll(this);

        this.template = _.template($('#template-topping').html());
        this.subViews = [];
        this.headerCommentSubView = null;

        this.listenTo(this.model, 'topping:validateOptions', this._validateTopping);
    },

    events: {
        'click .topping__header': '_toggleOptions'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        this.$el.toggleClass('topicOptionCheckbox', this.model.isCheckBoxList());
        this.$el.toggleClass('selection-required', this.model.isSelectionRequired());

        this._initToppingOptions();
        this._showOptions(this.model.areOptionsVisible());
        this._renderToppingHeaderCommentView();
        this._validateTopping();

        return this;
    },

    _initToppingOptions: function() {
        this.model.options.each(this._renderToppingOptionView, this);
    },

    _renderToppingOptionView: function(toppingOptionModel) {
        var view = new ToppingOptionView({
            model: toppingOptionModel
        });

        this.listenTo(view, 'toppingOption:updateSelection', this.model.toggleToppingOptionSelection);

        this.subViews.push(view);
        this.$('.topping__options').append(view.render().el);
    },

    _renderToppingHeaderCommentView: function() {
        this._removeHeaderCommentSubView();
        this.headerCommentSubView = new ToppingOptionCommentView({
            model: this.model
        });
        this.$('.topping__header__info').append(this.headerCommentSubView.render().el);
    },

    _toggleOptions: function() {
        this.setOptionsVisibility(!this.model.areOptionsVisible());
    },

    setOptionsVisibility: function(areVisible) {
        if(areVisible !== this.model.areOptionsVisible()) {
            if (areVisible) {
                this.trigger('topping:openingOptions');
            }
            this.model.setOptionsVisibility(areVisible, true);
            this._showOptions(areVisible);
            this._renderToppingHeaderCommentView();
            this.$('.topping__header__arrow').toggleClass('icon-down-open-big icon-up-open-big');
        }
    },

    _showOptions: function(boolean) {
        this.$el.toggleClass('optionsVisible', boolean);
        this.$('.topping__options').toggle(boolean);
        $(".portlet-header").toggleClass("ui-icon-plus ui-icon-minus");
    },

    _validateTopping: function() {
        this.$('.topping__header').toggleClass('valid invalid', this.model.isValid());
        this.trigger('toppings:validateToppings');
    },

    _removeHeaderCommentSubView: function() {
        if (this.headerCommentSubView && _.isFunction(this.headerCommentSubView.remove)) {
            this.headerCommentSubView.remove();
        }
    },

    remove: function() {
        this.model.options.reset(null);
        _.invoke(this.subViews, 'remove');
        this._removeHeaderCommentSubView();
        this.undelegateEvents();
        Backbone.View.prototype.remove.apply(this, arguments);
    }
});

var ToppingSpecialInstructionsView = Backbone.View.extend({
    initialize: function() {
        this.template = _.template($('#topping-special-instructions').html());
    },

    events: {
        'click .topping__header': '_toggleInstructions',
        'change .topping__special-instructions__textarea': '_memorizeInstructions'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    _memorizeInstructions: function() {
        this.model.set('special_instructions', this.$('.topping__special-instructions__textarea').val());
        this.trigger('special_instructions:validate');
    },

    _toggleInstructions: function() {
        var areInstructionsVisible, hasText;

        this.$('.topping__options').toggleClass('hide');
        areInstructionsVisible = this.$('.topping__options').hasClass('hide');
        this.$('.topping__header__arrow').toggleClass('icon-down-open-big', areInstructionsVisible);
        this.$('.topping__header__arrow').toggleClass('icon-up-open-big', !areInstructionsVisible);

        hasText = this.model.get('special_instructions') !== '';
        this.$('.topping__comment__help-text').toggleClass('hide', hasText);
        this.$('.topping__comment__special-instructions').toggleClass('hide', !hasText);
        this.$('.topping__comment__special-instructions').html(_.escape(this.model.get('special_instructions')));
    },

    remove: function() {
        this.undelegateEvents();
        Backbone.View.prototype.remove.apply(this, arguments);
    }
});

var ToppingsProductQuantityView = Backbone.View.extend({
    className: 'toppings-product-quantity',

    events: {
        'click .product__quantity__decrease': '_decreaseQuantity',
        'click .product__quantity__increase': '_increaseQuantity'
    },

    initialize: function() {
        this.template = _.template($('#toppings-product-quantity').html());
        this.listenTo(this.model, 'change:quantity', this.render);
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    _increaseQuantity: function() {
        this.model.set('quantity', this.model.get('quantity') + 1);
    },

    _decreaseQuantity: function() {
        var quantity = this.model.get('quantity');

        if (quantity > 1) {
            this.model.set('quantity', quantity - 1);
        }
    }
});

var ToppingsView = Backbone.View.extend({
    initialize: function(options) {
        this.template = _.template($('#template-choices-toppings').html());
        this.cartModel = options.cartModel;
        this.vendor = options.vendor;
        this.subViews = [];
        this.specialInstructionsView = null;
        this.productToUpdate = options.productToUpdate || null;
        this.gtmService = options.gtmService;

        this.listenTo(this.model, 'change:quantity', this._validateToppings);
    },

    events: {
        'click .toppings-add__to__cart': '_addToCart',
        'hide.bs.modal': '_closeModal'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this.$('.modal-error-message').hide();
        this._initToppings();
        this._renderQuantitySelector();
        this._validateToppings();
        this._initSpecialInstructions();

        if (this.subViews.length > 0) {
            this.subViews[0].setOptionsVisibility(true);
        }

        return this;
    },

    _initToppings: function() {
        this.model.toppings.map(this._initToppingView, this);
    },

    _initSpecialInstructions: function () {
        this.specialInstructionsView = new ToppingSpecialInstructionsView({
            model: this.model,
            className: 'topping__container'
        });

        this.$('.toppings__special-instructions').html(this.specialInstructionsView.render().el);
    },

    _renderQuantitySelector: function() {
        var view = new ToppingsProductQuantityView({
            model: this.model
        });

        this.quantitySelectorView = view;
        this.$('.modal-footer').append(view.render().el);
    },

    _initToppingView: function(topping) {
        var view = new ToppingView({
            model: topping
        });

        this.listenTo(view, 'toppings:validateToppings', this._validateToppings, this);
        this.listenTo(view, 'topping:openingOptions', this._closeAllTopicOptions, this);

        this.subViews.push(view);
        this.$('.toppings').append(view.render().el);
    },

    _validateToppings: function() {
        var valid = this.model.isValid();

        this.$('.toppings-add__to__cart').toggleClass('disabled', !valid);
        if(valid) {
            this.$('.modal-error-message').slideUp('fast');
        }
    },

    _closeAllTopicOptions: function() {
        _.invoke(this.subViews, 'setOptionsVisibility', false);
    },

    _addToCart: function() {
        var cart;

        if (!this.model.isValid()) {
            this.$('.modal-error-message').slideDown('fast');

            return;
        }

        cart = this.cartModel.getCart(this.vendor.id);

        this.listenToOnce(cart, 'cart:ready', function () {
            this._fireAddToCartEvent(this.model.get('product_variation_id'));
        }.bind(this));

        if (this.productToUpdate) {
            cart.updateItem(this.productToUpdate, this.model); //modify product
        } else {
            cart.addItem(this.model); //add product
        }
        this._closeModal();
    },

    _fireAddToCartEvent: function (productVariationId) {
        if (this.gtmService) {
            cart = this.cartModel.getCart(this.vendor.id);
            var model = _.findWhere(cart.products.models, {attributes: {product_variation_id: productVariationId}});

            console.debug('GTM:addProduct');
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
                actionLocation: 'PDP'
            });
        }
    },
    
    _closeModal: function() {
        this.undelegateEvents(); //stop listening on events, very important!
        _.invoke(this.subViews, 'remove');
        this.specialInstructionsView.remove();
        this.quantitySelectorView.remove();
        this.$('#choices-toppings-modal').modal('hide');
    },

    unbind: function() {
        console.log('ToppingsView.unbind', this.cid);
        this.stopListening();
        this.undelegateEvents();
    }
});
