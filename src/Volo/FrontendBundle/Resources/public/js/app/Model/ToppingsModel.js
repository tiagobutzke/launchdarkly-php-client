var ToppingOptionModel = Backbone.Model.extend({
    defaults: {
        selected: false,
        buttonType: 'radioButton'
    },

    toggleSelection: function() {
        if(this.get('selected')) {
            return this.select();
        }

        return this.unselect();
    },

    select: function(force) {
        if(force || this.toppingModel.canSelectSubModel(this.get('buttonType'))) {
            this.trigger('toppingOption:beforeSelection');
            this.set({ selected: true });

            return true;
        }

        return false;
    },

    unselect: function(force) {
        if(force || this.toppingModel.canUnselectSubModel(this.get('buttonType'))) {
            this.set({ selected: false });

            return true;
        }

        return false;
    },

    isSelected: function () {
        return this.get('selected');
    },

    setToppingModel: function (toppingModel) {
        this.toppingModel = toppingModel;
    },

    setToRadioButton: function () {
        this.set('buttonType', 'radioButton');
    },

    setToCheckBox: function () {
        this.set('buttonType', 'checkBox');
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
        this.listenTo(this.options, 'add', this._setInitialSelection);
        _.each(this.options.models, this._setInitialSelection, this);
        _.invoke(this.options.models, 'setToppingModel', this);
        _.invoke(this.options.models, (this.isCheckBoxList() ? 'setToCheckBox' : 'setToRadioButton'), this);
        delete this.attributes.options;
    },

    _setInitialSelection: function(toppingModel) {
        var quantity_minimum;

        if (!toppingModel) {
            return;
        }

        quantity_minimum = this.get('quantity_minimum');
        if (this.options.length <= quantity_minimum && this._getSelectedItems.length < quantity_minimum) {
            toppingModel.select(true);
        }
    },

    isCheckBoxList: function() {
        return this.get('quantity_maximum') > 1 || this.options.length === 1;
    },

    areOptionsVisible: function() {
        return this.get('optionsVisible');
    },

    setOptionsVisibility: function(areVisible, isSilent) {
        var options = isSilent ? { silent: true } : {};

        this.set({ optionsVisible: areVisible }, options);
    },

    isSelectionRequired: function() {
        return this.get('quantity_minimum') > 0;
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
            min = this.get('quantity_minimum'),
            options = this.options.length;

        min = options > min ? min : options;
        return (selectedCount >= min) && (selectedCount <= max);
    },

    canSelectSubModel: function(buttonType) {
        if ((buttonType === 'checkBox') && (this._getSelectedItems().length >= this.get('quantity_maximum'))) {

            return false;
        }

        return true;
    },

    canUnselectSubModel: function(buttonType) {
        var max = this.get('quantity_maximum'),
            min = this.get('quantity_minimum');

        if (max !== min &&
            ((buttonType === 'radioButton' && this.isSelectionRequired()) || this._getSelectedItems().length <= min)) {

            return false;
        }

        return true;
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
