var VOLO = VOLO || {};

VOLO.FiltersView = Backbone.View.extend({
    events: {
        "click #button-filter": "_onClick"
    },

    initialize: function(options) {
        _.bindAll(this);
    },

    _onClick: function() {
        var cuisines = _.values($('#form-filters').serializeJSON()).join(',');
        this.model.set('cuisines', cuisines);
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
        this.collection = new VOLO.FilterVendorCollection();
        VOLO.filterVendorCollection = this.collection;
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
