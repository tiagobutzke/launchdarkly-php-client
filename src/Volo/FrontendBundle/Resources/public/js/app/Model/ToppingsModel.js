var ToppingOptionModel = Backbone.Model.extend({
    defaults: {
        selected: false
    },

    setSelection: function(selection) {
        this.set('selected', selection);
    },

    isSelected: function() {
        return this.get('selected');
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
        _.bindAll(this);

        this.options = new ToppingOptionCollection(_.cloneDeep(this.get('options')));
        this.listenTo(this.options, 'add', this._setInitialSelection);
        _.each(this.options.models, this._setInitialSelection, this);
        delete this.attributes.options;
    },

    _setInitialSelection: function(optionModel) {
        var quantity_minimum;

        if (!optionModel) {
            return;
        }

        quantity_minimum = this.get('quantity_minimum');
        if (this.options.length <= quantity_minimum && this._getSelectedItems().length < quantity_minimum) {
            optionModel.setSelection(true);
        }
    },

    areOptionsVisible: function() {
        return this.get('optionsVisible');
    },

    isCheckBoxList: function() {
        return this.get('quantity_maximum') > 1 || this.options.length === 1;
    },

    setOptionsVisibility: function(areVisible, isSilent) {
        var options = isSilent ? { silent: true } : {};

        this.set({ optionsVisible: areVisible }, options);
    },

    isSelectionRequired: function() {
        return this.get('quantity_minimum') > 0;
    },

    _getSelectedItems: function() {
        return _.filter(this.options.models, function(optionModel) {
            return optionModel.isSelected();
        });
    },

    toggleToppingOptionSelection: function(optionModel) {
        var isSelected = optionModel.get('selected');

        if (this._isToppingUnselectable() && isSelected) {
            optionModel.setSelection(false);
            this.trigger('topping:validateOptions');
        } else if (this._isToppingSelectable() && !isSelected) {
            //if radio button, unselect all at first
            if (!this.isCheckBoxList()) {
                _.invoke(this.options.models, 'setSelection', false);
            }
            optionModel.setSelection(true);
            this.trigger('topping:validateOptions');
        } else {
            this.trigger('topping:toggleDenied');
        }
    },

    isValid: function() {
        var selectedItems = this._getSelectedItems(),
            selectedCount = selectedItems.length,
            max = this.get('quantity_maximum'),
            min = this.get('quantity_minimum'),
            options = this.options.length;

        min = options > min ? min : options;
        return (selectedCount >= min) && (selectedCount <= max);
    },

    _isToppingSelectable: function() {
        var isCheckbox = this.isCheckBoxList(),
            selectedItemsCount = this._getSelectedItems().length,
            quantityMaximum = this.get('quantity_maximum');

        return !isCheckbox || (selectedItemsCount < quantityMaximum);
    },

    _isToppingUnselectable: function() {
        var max = this.get('quantity_maximum'),
            min = this.get('quantity_minimum'),
            selectedItemsCount = this._getSelectedItems().length,
            optionsCount = this.options.length,
            maxEqualsMin = max === min,
            unselectableBySelected = selectedItemsCount > min && !maxEqualsMin,
            unselectableByMaxMin = maxEqualsMin && min > 1 && min < optionsCount;
        
        return unselectableByMaxMin || unselectableBySelected;
    },

    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.apply(this, arguments);

        json.options = this.options.toJSON();
        json.selectedOptions = new ToppingOptionCollection(this._getSelectedItems()).toJSON();

        return json;
    }
});

var ToppingCollection = Backbone.Collection.extend({
    model: ToppingModel
});
