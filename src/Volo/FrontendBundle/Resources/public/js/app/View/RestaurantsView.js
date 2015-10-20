var VOLO = VOLO || {};

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

        this.listenTo(options.filterModel, 'change', this._refreshVendors);
        this.listenTo(this.collection, 'reset', this.render);

        _.each(this.$('.restaurants__list a > div'), this._initVendorModel);
    },

    _initVendorModel: function(item) {
        var vendor = $(item).data().vendor,
            model = new VOLO.VendorModel(vendor),
            view = new VOLO.RestaurantView({
                model: model,
                el: item.parentNode
            });

        this.subViews.push(view);
        this.listenTo(view, 'restaurantsView:restaurantClicked', this._fetchDataFromNode);
        this.collection.add(model);
    },

    render: function() {
        this.$('.restaurants__list--open').empty();
        this.$('.restaurants__list--closed').empty();

        _.invoke(this.subViews, 'remove');
        this.subViews.length = 0;

        if (this.collection.length === 0) {
            this.$('.restaurants__search__not-found-message').removeClass('hide');
        } else {
            this.$('.restaurants__search__not-found-message').addClass('hide');
        }

        this.collection.each(this._renderVendor);
        window.blazy.revalidate();
        this._onScrollResize();

        return this;
    },

    _refreshVendors: function() {
        this.collection.fetch({reset: true});
    },

    _renderVendor: function(vendor) {
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
        var elementTop = view.$('.restaurants__list__item').offset() || {top: 0},
            scrolled = this.$window.height() + this.$window.scrollTop();

        return elementTop.top <= scrolled;
    },

    _fetchDataFromNode: function (view) {
        var position = 1;

        if (view.model.isOpen()) {
            position += this.$('.restaurants__list--open a').index(view.$el);
        } else {
            position += this.$('.restaurants__list--open a').length + this.$el.find('.restaurants__list--closed a').index(view.$el);
        }

        return {
            name: view.model.get('name'),
            id: view.model.get('code'),
            variant: view.model.isOpen() ? 'open' : 'closed',
            position: position
        };
    },

    unbind: function() {
        if (this._lastTimeOut) {
            clearTimeout(this._lastTimeOut);
        }

        this._scrollEvent.unbind('scroll resize', this._onScrollResize);

        this.stopListening();
        this.undelegateEvents();
        _.invoke(this.subViews, 'unbind');
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
        this.listenTo(this.model, 'view:show', this._show);
        this.listenTo(this.model, 'view:hide', this._hide);
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this.$el.attr('href', Routing.generate('vendor', {
            code: this.model.get('code'),
            urlKey: this.model.get('url_key')
        }));

        return this;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    },

    _restaurantClick: function() {
        this.trigger('restaurantsView:restaurantClicked', this);
    },

    _show: function() {
        this.$el.removeClass('hide');
    },

    _hide: function() {
        this.$el.addClass('hide');
    }
});
