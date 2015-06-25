var ToppingOptionCommentView = Backbone.View.extend({
    className: 'topping__header__comment',

    initialize: function() {
        this.template = _.template($('#template-topping-comment').html());
    },

    render: function() {
        var optionsVisible = this.model.areOptionsVisible();

        this.$el.html(this.template(this.model.toJSON()));
        this.$('.topping__comment__help-text').toggle(optionsVisible || this.model._getSelectedItems().length === 0);
        this.$('.topping__comment__selected_items_list').toggle(!optionsVisible);

        return this;
    }
});

var ToppingOptionView = Backbone.View.extend({
    className: 'topping-option',

    initialize: function() {
        this.template = _.template($('#template-topping-option').html());
        this.$el.toggleClass('selected', this.model.isSelected());
    },

    events: {
        'click': 'toggleSelection'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    select: function(force) {
        if (this.model.select(force)) {
            this.$el.addClass('selected', true);
            this.trigger('topping:validate');
        }
    },

    unselect: function(force) {
        if (this.model.unselect(force)) {
            this.$el.removeClass('selected');
            this.trigger('topping:validate');
        }
    },

    toggleSelection: function() {
        if (this.model.isSelected()) {
            this.unselect();
        } else {
            this.select();
        }
    },

    remove: function() {
        this.undelegateEvents();
        Backbone.View.prototype.remove.apply(this, arguments);
    }

});

var ToppingView = Backbone.View.extend({
    className: 'topping',

    initialize: function() {
        _.bindAll(this, '_unselectAllToppingOptions');

        this.template = _.template($('#template-topping').html());
        this.subModels = [];
        this.subViews = [];
        this.headerCommentSubView = null;
    },

    events: {
        'click .topping__header': '_toggleOptions'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this._initToppingOptions();
        this._validate();
        this._showOptions(this.model.areOptionsVisible());
        this._renderToppingHeaderCommentView();

        return this;
    },

    _initToppingOptions: function() {
        var toppingOptions = this.model.options;

        toppingOptions.map(this._renderToppingOptionView, this);
    },

    _renderToppingOptionView: function(toppingOptionModel) {
        var view = new ToppingOptionView({
            model: toppingOptionModel
        });

        if(!this.model.isCheckBoxList()) {
            this.listenTo(toppingOptionModel, 'toppingOption:beforeSelection', this._unselectAllToppingOptions);
        }
        this.listenTo(view, 'topping:validate', this._validate, this);
        this.subModels.push(toppingOptionModel);
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

    _unselectAllToppingOptions: function() {
        _.invoke(this.subViews, 'unselect', true);
    },

    _validate: function() {
        this.$('.topping__header').toggleClass('valid invalid', this.model.isValid());
        this.$el.toggleClass('topicOptionCheckbox', this.model.isCheckBoxList());
        this.$el.toggleClass('selection-required', this.model.isSelectionRequired());
        this.trigger('toppings:validate');
    },

    _removeHeaderCommentSubView: function() {
        if (this.headerCommentSubView && _.isFunction(this.headerCommentSubView.remove)) {
            this.headerCommentSubView.remove();
        }
    },

    remove: function() {
        _.invoke(this.subModels, 'remove');
        _.invoke(this.subViews, 'remove');
        this._removeHeaderCommentSubView();
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
        this.vendorId = options.vendorId;
        this.subViews = [];
        this.productToUpdate = options.productToUpdate || null;
        this.gtmService = options.gtmService;
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
        this._validate();

        if (this.subViews.length > 0) {
            this.subViews[0].setOptionsVisibility(true);
        }

        return this;
    },

    _initToppings: function() {
        this.model.toppings.map(this._initToppingView, this);
    },

    _renderQuantitySelector: function() {
        var view = new ToppingsProductQuantityView({
            model: this.model
        });

        this.listenTo(this.model, 'change:quantity', this._validate, this);
        this.quantitySelectorView = view;
        this.$('.modal-footer').append(view.render().el);
    },

    _initToppingView: function(topping) {
        var view = new ToppingView({
            model: topping
        });

        this.listenTo(view, 'toppings:validate', this._validate, this);
        this.listenTo(view, 'topping:openingOptions', this._closeAllTopicOptions, this);

        this.subViews.push(view);
        this.$('.toppings').append(view.render().el);
    },

    _validate: function() {
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
        if (!this.model.isValid()) {
            this.$('.modal-error-message').slideDown('fast');

            return;
        }

        if (this.gtmService) {
            console.log('GTM:addProduct');
            this.gtmService.fireAddProduct(this.vendorId, {
                id: this.model.get('product_variation_id'),
                name: this.model.get('name')
            });
        }

        if (this.productToUpdate) {
            this.cartModel.getCart(this.vendorId).updateItem(this.productToUpdate, this.model.toJSON()); //modify product
        } else {
            this.cartModel.getCart(this.vendorId).addItem(this.model.toJSON(), this.model.get('quantity')); //add product
        }
        this._closeModal();
    },
    
    _closeModal: function() {
        this.undelegateEvents(); //stop listening on events, very important!
        _.invoke(this.subViews, 'remove');
        this.quantitySelectorView.remove();
        this.$('#choices-toppings-modal').modal('hide');
    }
});
