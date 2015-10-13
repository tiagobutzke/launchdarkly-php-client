var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        'click .restaurants__filter': '_showOrHideFilter',
        'click .restaurants__filter-tooltip': '_filterRestaurants',
        'click .restaurants__filter-form__head-cancel-cuisines': '_clearFilterCuisines',
        'click .restaurants__filter-form__head-cancel-food-characteristics': '_clearFilterFoodCharacteristics'
    },

    initialize: function() {
        this.cuisines = "";
        this.foodCharacteristics = "";
        _.bindAll(this);
        $('body').on('click', this._hideFilter);
    },

    _filterRestaurants: function(e) {
        e.stopPropagation();
        this.cuisines = _.values($('.restaurants__filter-form-cuisines').serializeJSON()).join(',');
        this.foodCharacteristics = _.values($('.restaurants__filter-form-food-characteristics').serializeJSON()).join(',');
        this.model.set('cuisines', this.cuisines);
        this.model.set('food_characteristics', this.foodCharacteristics);
        this._updateCuisinesCancelButtonState();
        this._updateFoodCharacteristicsCancelButtonState();
    },

    _showOrHideFilter: function() {
        if (this.$('.restaurants__filter-tooltip').hasClass('hide')) {
            this._showFilter();
        } else {
            this._hideFilter();
        }

        return false;
    },

    _showFilter: function() {
        this._showElement(this.$('.restaurants__filter-tooltip'));
    },

    _hideFilter: function() {
        this._hideElement(this.$('.restaurants__filter-tooltip'));
        this._updateFilterButtonState();
    },

    _updateFilterButtonState: function() {
        if (this.cuisines !== "" || this.foodCharacteristics !== "") {
            this.$('.restaurants__filter').addClass('restaurants__filter--active');
        } else {
            this.$('.restaurants__filter').removeClass('restaurants__filter--active');
        }
    },

    _updateCuisinesCancelButtonState: function() {
        if (this.cuisines === "") {
            this._hideElement(this.$('.restaurants__filter-form__head-cancel-cuisines'));
        } else {
            this._showElement(this.$('.restaurants__filter-form__head-cancel-cuisines'));
        }
    },

    _updateFoodCharacteristicsCancelButtonState: function() {
        if (this.foodCharacteristics === "") {
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
        $('body').off('click', this._hideFilter);
    }
});
