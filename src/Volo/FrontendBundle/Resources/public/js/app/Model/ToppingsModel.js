var ToppingOptionModel = Backbone.Model.extend({
    defaults: {
        selected: false
    },

    toggleSelection: function() {
        this.set({
            selected: !this.get('selected')
        });
    }
});

var ToppingOptionCollection = Backbone.Collection.extend({
    model: ToppingOptionModel
});

var ToppingModel = Backbone.Model.extend({
    defaults: {
        optionsVisible: false,
        options: [],
        quantity_minimum: null,
        quantity_maximum: null
    },

    initialize: function() {
        this.options = new ToppingOptionCollection(_.cloneDeep(this.get('options')));
        delete this.attributes.options;
    },

    _getSelectedItems: function() {
        return this.options.where({
            selected: true
        });
    },

    isValid: function() {
        var selectedItems = this._getSelectedItems(),
            selectedCount = selectedItems.length,
            max = this.get('quantity_maximum'),
            min = this.get('quantity_minimum');

        return (selectedCount >= min) && (selectedCount <= max);
    },

    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.apply(this, arguments);

        json.options = this.options.toJSON();

        return json;
    }
});

var ToppingCollection = Backbone.Collection.extend({
    model: ToppingModel
});

var ChoicesToppingsModel = Backbone.Model.extend({
    defaults: {
        product_variations: [{}]
    },

    initialize: function() {
        this.toppings = new ToppingCollection(_.cloneDeep(this.get('product_variations')[0].toppings));
    },

    isValid: function() {
        var notValidTopping = this.toppings.find(function(topping) {
            return !topping.isValid();
        });

        return !notValidTopping;
    },

    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.apply(this, arguments);

        json.product_variations[0].toppings = this.toppings.toJSON();

        return json;
    }
});
