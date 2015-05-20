var ToppingOptionView = Backbone.View.extend({
    className: 'topping-option',

    initialize: function() {
        this.template = _.template($('#template-topping-option').html());
    },

    events: {
        click: 'toggleSelection'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        return this;
    },

    toggleSelection: function() {
        this.model.toggleSelection();
        this.$el.toggleClass('selected');
        this.trigger('topping:validate');
    }
});

var ToppingView = Backbone.View.extend({
    className: 'topping',

    initialize: function() {
        this.template = _.template($('#template-topping').html());
        this.subViews = [];
    },

    events: {
        'click .topping__header': '_toggleOptions'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this._initToppingOptions();
        this._validate();

        return this;
    },

    _initToppingOptions: function() {
        var toppingOptions = this.model.options;

        toppingOptions.map(this._renderToppingOptionView, this);
    },

    _renderToppingOptionView: function(toppingOption) {
        var view = new ToppingOptionView({
            model: toppingOption
        });
        this.listenTo(view, 'topping:validate', this._validate, this);
        this.subViews.push(toppingOption);

        this.$('.topping__options').append(view.render().el);
    },

    _toggleOptions: function() {
        this.model.set({optionsVisible: !this.model.get('optionsVisible')});
        this.$('.topping__options').toggle(this.model.get('optionsVisible'));
    },

    _validate: function() {
        var valid = this.model.isValid();

        this.$('.topping__header').toggleClass('valid', valid);
        this.$('.topping__header').toggleClass('invalid', !valid);

        this.trigger('toppings:validate');
    },

    remove: function() {
        _.invoke(this.subViews, 'remove');
        Backbone.View.prototype.remove.apply(this, arguments);
    }
});

var ToppingsView = Backbone.View.extend({
    initialize: function(options) {
        this.template = _.template($('#template-choices-toppings').html());
        this.cartModel = options.cartModel;
        this.vendorId = options.vendorId;
        this.subViews = [];
    },

    events: {
        'click .toppings-add__to__cart': '_addToCart'
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this._initToppings();
        this._validate();

        return this;
    },

    _initToppings: function() {
        this.model.toppings.map(this._initToppingView, this);
    },

    _initToppingView: function(topping) {
        var view = new ToppingView({
            model: topping
        });
        this.listenTo(view, 'toppings:validate', this._validate, this);

        this.subViews.push(view);
        this.$('.toppings').append(view.render().el);
    },

    _validate: function() {
        var valid = this.model.isValid();

        this.$('.toppings-add__to__cart').toggleClass('disabled', !valid);
    },

    _addToCart: function() {
        if (!this.model.isValid()) { return; }

        this.undelegateEvents(); //stop listening on events, very important!
        _.invoke(this.subViews, 'remove');
        this.cartModel.getCart(this.vendorId).addItem(this.model.toJSON(), 1); //add product
        $('#choices-toppings-modal').modal('hide'); //hide
    }
});
