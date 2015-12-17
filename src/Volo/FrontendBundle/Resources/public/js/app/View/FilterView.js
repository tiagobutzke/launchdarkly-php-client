var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        'click .restaurants__filter': '_showOrHideFilter',
        'click .restaurants__filter-tooltip': '_invokeFilterRestaurants',
        'click .restaurants__filter-form__head-cancel-cuisines': '_clearFilterCuisines',
        'click .restaurants__filter-form__head-cancel-food-characteristics': '_clearFilterFoodCharacteristics',
        'click .restaurants__filter-form__head-cancel-budgets': '_clearFilterBudgets',
        'click .filter__mobile-button': '_closeFilter',
        'click .restaurants__filter-budgets-button': '_toggleBudgetsButtonsState'
    },

    initialize: function(options) {
        _.bindAll(this);
        $('body').on('click', this._closeFilter);

        this._updateFilterModelWithFormValues();
        this.vendorCollection = options.vendorCollection;
        this.listenTo(this.vendorCollection, 'reset', this._updateFilterButton);
        this._updateFilterButton();
        this.throttleFilterRestaurants = _.throttle(this._filterRestaurants, 200, {leading: false});
    },

    _toggleBudgetsButtonsState: function(e) {
        var $target = $(e.target);
        if ($target.hasClass('restaurants__filter-budgets-button--active')) {
            $target.removeClass('restaurants__filter-budgets-button--active');
        } else {
            $target.addClass('restaurants__filter-budgets-button--active');
        }
    },

    _updateBudgetCancelButtonState: function() {
        if (_.isEmpty(this.model.get('budgets'))) {
            this._hideElement(this.$('.restaurants__filter-form__head-cancel-budgets'));
        } else {
            this._showElement(this.$('.restaurants__filter-form__head-cancel-budgets'));
        }
    },

    _getCuisinesFormValues: function() {
        return _.values(this.$('.restaurants__filter-form-cuisines').serializeJSON()).join(',');
    },

    _getFoodCharacteristicsFormValues: function() {
        return _.values(this.$('.restaurants__filter-form-food-characteristics').serializeJSON()).join(',');
    },

    _getBudgetsFormValues: function() {
        var budgetItems = this.$('.restaurants__filter-budgets-button--active'),
            budgetItemsValue = [];

        if (budgetItems.length) {
            _.each(this.$('.restaurants__filter-budgets-button--active'), function(budgetItem) {
                budgetItemsValue.push($(budgetItem).data('budget'));
            });
        }
        return budgetItemsValue.toString();
    },

    _updateFilterModelWithFormValues: function () {
        this.model.set('cuisines', this._getCuisinesFormValues());
        this.model.set('food_characteristics', this._getFoodCharacteristicsFormValues());
        this.model.set('budgets', this._getBudgetsFormValues());
    },

    _filterRestaurants: function(e) {
        this._updateFilterModelWithFormValues();
        this._updateCuisinesCancelButtonState();
        this._updateFoodCharacteristicsCancelButtonState();
        this._updateBudgetCancelButtonState();
        this._updateFilterButtonState();
    },

    _invokeFilterRestaurants: function(e) {
        e.stopPropagation();
        this.throttleFilterRestaurants();
    },

    _updateFilterButton: function() {
        this.$('.filter__mobile-button span').text(this.vendorCollection.length);
    },

    _showOrHideFilter: function() {
        if (this.$('.restaurants__filter-tooltip').hasClass('hide')) {
            this._showFilter();
        } else {
            this._closeFilter();
        }

        return false;
    },

    _showFilter: function() {
        this._showElement(this.$('.restaurants__filter-tooltip'));
    },

    _closeFilter: function() {
        this._hideElement(this.$('.restaurants__filter-tooltip'));
    },

    _updateFilterButtonState: function() {
        if (!_.isEmpty(this.model.get('cuisines')) || !_.isEmpty(this.model.get('food_characteristics')) || !_.isEmpty(this.model.get('budgets'))) {
            this.$('.restaurants__filter').addClass('restaurants__filter--active');
        } else {
            this.$('.restaurants__filter').removeClass('restaurants__filter--active');
        }
    },

    _updateCuisinesCancelButtonState: function() {
        if (_.isEmpty(this.model.get('cuisines'))) {
            this._hideElement(this.$('.restaurants__filter-form__head-cancel-cuisines'));
        } else {
            this._showElement(this.$('.restaurants__filter-form__head-cancel-cuisines'));
        }
    },

    _updateFoodCharacteristicsCancelButtonState: function() {
        if (_.isEmpty(this.model.get('food_characteristics'))) {
            this._hideElement(this.$('.restaurants__filter-form__head-cancel-food-characteristics'));
        } else {
            this._showElement(this.$('.restaurants__filter-form__head-cancel-food-characteristics'));
        }
    },

    _clearFilterCuisines: function(e) {
        this.$('.restaurants__filter-form-cuisines .form-control').attr("checked", false);
        this._invokeFilterRestaurants(e);
    },

    _clearFilterFoodCharacteristics: function(e) {
        this.$('.restaurants__filter-form-food-characteristics .form-control').attr("checked", false);
        this._invokeFilterRestaurants(e);
    },

    _clearFilterBudgets: function(e) {
        this.$('.restaurants__filter-budgets-button--active').removeClass('restaurants__filter-budgets-button--active');
        this._invokeFilterRestaurants(e);
    },

    _hideElement: function($el) {
        $el.addClass('hide');
    },

    _showElement: function($el) {
        $el.removeClass('hide');
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        $('body').off('click', this._closeFilter);
    }
});
