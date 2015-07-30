var VOLO = VOLO || {};

VOLO.RestaurantsView = Backbone.View.extend({
    events: {
        "click .restaurants__list__item": "_restaurantClick"
    },

    initialize: function(options) {
        _.bindAll(this);

        this._eventTriggerDelay = options.triggerDelay || 300;
        this._lastTimeOut = null;
        this._displayedRestaurants = [];
        this._scrollEvent = $(window).on('scroll resize', this._onScrollResize);
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
        var $restaurants = this.$('.restaurants__list__item'),
            curried = _.curry(this._isElOnScreen),
            $window = $(window);

        return _.filter($.makeArray($restaurants), curried($window.height(), $window.scrollTop()));
    },

    _isElOnScreen: function(height, scrollTop, element) {
        var elementPos = $(element).offset(),
            elementTop = elementPos.top,
            scrolled = height + scrollTop;

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
