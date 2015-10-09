var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        'click .restaurants__filter': 'showOrHideFilter',
        'click .restaurants__filter-tooltip': 'filterRestaurants',
        'click .restaurants__filter-form__head-cancel-cuisines': 'clearFilterCuisines',
        'click .restaurants__filter-form__head-cancel-food-characteristics': 'clearFilterFoodCharacteristics',
        'click .restaurants__filter-form__head-show-all': 'showFilterAboveCertainAmount',
        'click .restaurants__filter-form__head-show-less': 'hideFilterAboveCertainAmount'
    },

    initialize: function() {
        this.cuisines = "";
        this.foodCharacteristics = "";
        _.bindAll(this);
        $('body').on('click', this.hideFilter);
    },

    filterRestaurants: function(e) {
        e.stopPropagation();
        this.cuisines = _.values($('.restaurants__filter-form-cuisines').serializeJSON()).join(',');
        this.foodCharacteristics = _.values($('.restaurants__filter-form-food-characteristics').serializeJSON()).join(',');
        this.model.set('cuisines', this.cuisines);
        this.model.set('food_characteristics', this.foodCharacteristics);
        this.checkCuisinesCancelButtonState();
        this.checkFoodCharacteristicsCancelButtonState();
    },

    showOrHideFilter: function() {
        if (this.$('.restaurants__filter-tooltip').hasClass('hide')) {
            this.$('.restaurants__filter-tooltip').removeClass('hide');
        } else {
            this.$('.restaurants__filter-tooltip').addClass('hide');
            this.showFilterButtonState();
        }

        return false;
    },

    hideFilter: function(e) {
        if (!this.$('.restaurants__filter-tooltip').hasClass('hide')) {
            this.$('.restaurants__filter-tooltip').addClass('hide');
            this.showFilterButtonState();
        }
    },

    showFilterButtonState: function() {
        if (this.cuisines !== "" || this.foodCharacteristics !== "") {
            this.$('.restaurants__filter').addClass('restaurants__filter--active');
        } else {
            this.$('.restaurants__filter').removeClass('restaurants__filter--active');
        }
    },

    checkCuisinesCancelButtonState: function() {
        if (this.cuisines !== "") {
            this.$('.restaurants__filter-form__head-cancel-cuisines').removeClass('hide');
        } else {
            this.$('.restaurants__filter-form__head-cancel-cuisines').addClass('hide');
        }
    },

    checkFoodCharacteristicsCancelButtonState: function() {
        if (this.foodCharacteristics !== "") {
            this.$('.restaurants__filter-form__head-cancel-food-characteristics').removeClass('hide');
        } else {
            this.$('.restaurants__filter-form__head-cancel-food-characteristics').addClass('hide');
        }
    },

    clearFilterCuisines: function(e) {
        $('.restaurants__filter-form-cuisines .form-control').attr("checked", false);
        this.filterRestaurants(e);
    },

    clearFilterFoodCharacteristics: function(e) {
        $('.restaurants__filter-form-food-characteristics .form-control').attr("checked", false);
        this.filterRestaurants(e);
    },

    showFilterAboveCertainAmount: function (e) {
        var $e = $(e.target),
            $form = $e.closest('form');
        $e.addClass('hide');
        $form.find('.restaurants__filter-form__head-show-less').removeClass('hide');
        $form.closest('form').find('.filter-form-group-hidden').removeClass('hide');
    },

    hideFilterAboveCertainAmount: function (e) {
        var $e = $(e.target),
            $form = $e.closest('form');
        $e.addClass('hide');
        $form.closest('form').find('.restaurants__filter-form__head-show-all').removeClass('hide');
        $form.closest('form').find('.filter-form-group-hidden').addClass('hide');
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        $('body').off('click', this.hideFilter);
    }

});

VOLO.RestaurantsView = Backbone.View.extend({
    events: {
        "click .restaurants__list__item": "_restaurantClick"
    },

    initialize: function(options) {
        _.bindAll(this);

        this._eventTriggerDelay = options.triggerDelay || 300;
        this._lastTimeOut = null;
        this._displayedRestaurants = [];
        this.$window = $(window);
        this._scrollEvent = this.$window.on('scroll resize', this._onScrollResize);
        this.collection = options.filterVendorCollection;
        this.template = _.template($('#template-restaurant-details').html());

        this.listenTo(VOLO.filterModel, 'change', this.render);
        this.listenTo(this.collection, 'all', this.debug);
        this.listenTo(this.collection, 'reset', this.renderVendors);
    },

    debug: function() {
        console.log(arguments);
    },

    render: function() {
        this.collection.fetch({reset: true});

        return this;
    },

    renderVendors: function() {
        this.$el.empty();
        this.collection.each(this.renderVendor);
        window.blazy.revalidate();
    },

    renderVendor: function(vendor) {
        this.$el.append(this.template(vendor.toJSON()));
    },

    onGtmServiceCreated: function() {
        var newRestaurants = this._checkNewDisplayedRestaurants();

        if (newRestaurants.length) {
            this.trigger('restaurantsView:restaurantsDisplayedOnLoad', _.map(newRestaurants, this._fetchDataFromNode));
        }
    },

    _onScrollResize: function() {
        if (this._lastTimeOut) {
            clearTimeout(this._lastTimeOut);
        }

        this._lastTimeOut = setTimeout(this._triggerImageVisible, this._eventTriggerDelay);
    },

    _triggerImageVisible: function() {
        var newRestaurants = this._checkNewDisplayedRestaurants();

        if (newRestaurants.length) {
            this.trigger('restaurantsView:restaurantsDisplayedOnScroll', _.map(newRestaurants, this._fetchDataFromNode));
        }
        this._lastTimeOut = null;
    },

    _checkNewDisplayedRestaurants: function() {
        var visibleRestaurants = this._getVisibleRestaurants(),
            newRestaurants = this._getNewRestaurants(visibleRestaurants);

        this._displayedRestaurants = this._displayedRestaurants.concat(visibleRestaurants);

        return newRestaurants;
    },

    _getNewRestaurants: function(visibleRestaurants) {
        return _.difference(visibleRestaurants, this._displayedRestaurants);
    },

    _getVisibleRestaurants: function() {
        var $restaurants = this.$('.restaurants__list__item');

        return _.filter($.makeArray($restaurants), this._isElOnScreen);
    },

    _isElOnScreen: function(element) {
        var elementTop = $(element).offset().top,
            scrolled = this.$window.height() + this.$window.scrollTop();

        return elementTop <= scrolled;
    },

    _restaurantClick: function(event) {
        this.trigger('restaurantsView:restaurantClicked', this._fetchDataFromNode(event.currentTarget));
    },

    _fetchDataFromNode: function (node) {
        var jNode = $(node);

        return {
            name: jNode.data('name'),
            id: jNode.data('code'),
            variant: jNode.data('variant'),
            position: jNode.data('position')
        };
    },

    unbind: function() {
        if (this._lastTimeOut) {
            clearTimeout(this._lastTimeOut);
        }

        this._scrollEvent.unbind('scroll resize', this._onScrollResize);

        this.stopListening();
        this.undelegateEvents();
    }
});
