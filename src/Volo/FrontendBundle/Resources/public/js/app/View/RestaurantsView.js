var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        'click .restaurants__filter': 'showOrHideFilter',
        'click .restaurants__filter-tooltip': 'filterRestaurants',
        'click .restaurants__filter-form__head-cancel-cuisines': 'clearFilterCuisines',
        'click .restaurants__filter-form__head-cancel-food-characteristics': 'clearFilterFoodCharacteristics'
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

        this.subViews = [];
        this._eventTriggerDelay = options.triggerDelay || 300;
        this._lastTimeOut = null;
        this._displayedRestaurants = [];
        this.$window = $(window);
        this._scrollEvent = this.$window.on('scroll resize', this._onScrollResize);
        this.collection = options.filterVendorCollection;

        this.listenTo(VOLO.filterModel, 'change', this.render);
        this.listenTo(this.collection, 'all', this.debug);
        this.listenTo(this.collection, 'reset', this.renderVendors);

        _.each(this.$('.restaurants__list a > div'), this._initVendorModels);
    },

    debug: function() {
        console.log(arguments);
    },

    _initVendorModels: function(item) {
        var vendor = $(item).data().vendor;
        var model = new VOLO.VendorModel(vendor);
        var view = new VOLO.RestaurantView({
            model: model,
            el: item
        });
        this.subViews.push(view);
        this.listenTo(view, 'restaurantsView:restaurantClicked', this._fetchDataFromNode);
    },

    render: function() {
        this.collection.fetch({reset: true});

        return this;
    },

    renderVendors: function() {
        this.$('.restaurants__list--open').empty();
        this.$('.restaurants__list--closed').empty();

        _.invoke(this.subViews, 'remove');
        this.subViews.length = 0;

        if (this.collection.length === 0) {
            this.$('.restaurants__filter__not-found-message').removeClass('hide');
        } else {
            this.$('.restaurants__filter__not-found-message').addClass('hide');
        }

        this.collection.each(this.renderVendor);
        window.blazy.revalidate();
        this._onScrollResize();
    },

    renderVendor: function(vendor) {
        var view = new VOLO.RestaurantView({
            model: vendor
        });

        if (vendor.isOpen()) {
            this.$('.restaurants__list--open').append(view.render().$el);
        } else {
            this.$('.restaurants__list--closed').append(view.render().$el);
        }

        this.subViews.push(view);
        this.listenTo(view, 'restaurantsView:restaurantClicked', this._fetchDataFromNode);
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
        console.log('_triggerImageVisible');
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
        return _.filter(this.subViews, this._isElOnScreen);
    },

    _isElOnScreen: function(view) {
        var elementTop = view.$el.offset().top,
            scrolled = this.$window.height() + this.$window.scrollTop();

        return elementTop <= scrolled;
    },

    _fetchDataFromNode: function (view) {
        var position = -1;

        if (view.model.isOpen()) {
            position = this.$('.restaurants__list--open a').index(view.$el.parent());
        } else {
            position = this.$('.restaurants__list--open a').length + this.$el.find('.restaurants__list--closed a').index(view.$el.parent());
        }

        var foo = {
            name: view.model.get('name'),
            id: view.model.get('code'),
            variant: view.model.isOpen() ? 'open' : 'closed',
            position: position
        };
        console.log('_fetchDataFromNode ', foo);
        return foo;
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

VOLO.RestaurantView = Backbone.View.extend({
    tagName: 'a',

    events: {
        "click": "_restaurantClick"
    },

    initialize: function() {
        _.bindAll(this);
        this.template = _.template($('#template-restaurant-details').html());
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this.$el.attr('href', Routing.generate('vendor', {code: this.model.get('code'), urlKey: this.model.get('url_key')}));

        return this;
    },

    _restaurantClick: function() {
        this.trigger('restaurantsView:restaurantClicked', this);
    }
});
