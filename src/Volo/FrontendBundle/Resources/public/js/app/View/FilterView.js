var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        'click .restaurants__filter': '_showOrHideFilter',
        'click .restaurants__filter-tooltip': '_filterRestaurants',
        'click .restaurants__filter-form__head-cancel-cuisines': '_clearFilterCuisines',
        'click .restaurants__filter-form__head-cancel-food-characteristics': '_clearFilterFoodCharacteristics',
        'click .filter__mobile-button ': '_closeFilter'
    },

    initialize: function(options) {
        _.bindAll(this);
        $('body').on('click', this._closeFilter);

        this._updateFilterModelWithFormValues();
        this.vendorCollection = options.vendorCollection;
        this.listenTo(this.vendorCollection, 'reset', this._updateFilterButton);
        this._updateFilterButton();
    },

    _getFormValueFrom: function(selector) {
        var elements = $(selector);
        if (elements.length) {
            return _.values(elements.serializeJSON()).join(',')
        }

        return '';
    },

    _getCuisinesFormValues: function () {
        return this._getFormValueFrom('.restaurants__filter-form-cuisines');
    },

    _getFoodCharacteristicsFormValues: function () {
        return this._getFormValueFrom('.restaurants__filter-form-food-characteristics');
    },

    _updateFilterModelWithFormValues: function () {
        this.model.set('cuisines', this._getCuisinesFormValues());
        this.model.set('food_characteristics', this._getFoodCharacteristicsFormValues());
    },

    _filterRestaurants: function(e) {
        e.stopPropagation();
        this._updateFilterModelWithFormValues();
        this._updateCuisinesCancelButtonState();
        this._updateFoodCharacteristicsCancelButtonState();
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
        this._updateFilterButtonState();
    },

    _updateFilterButtonState: function() {
        if (!_.isEmpty(this.model.get('cuisines')) || !_.isEmpty(this.model.get('food_characteristics'))) {
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
        $('.restaurants__filter-form-cuisines .form-control').attr("checked", false);
        this._filterRestaurants(e);
    },

    _clearFilterFoodCharacteristics: function(e) {
        $('.restaurants__filter-form-food-characteristics .form-control').attr("checked", false);
        this._filterRestaurants(e);
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
