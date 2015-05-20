var MenuItemModel = Backbone.Model.extend({
    hasToppings: function() {
        return this.toJSON().product_variations[0].toppings.length > 0;
    },

    hasChoices: function() {
        return this.toJSON().product_variations[0].choices.length > 0;
    },

    showOrderDialog: function() {
        return this.hasChoices() || this.hasToppings();
    }
});
