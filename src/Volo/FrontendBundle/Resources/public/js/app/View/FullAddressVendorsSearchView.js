VOLO = VOLO || {};
VOLO.FullAddressVendorsSearchView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.fullAddressSearchView = new VOLO.FullAddressInputSearchView({
            model: this.model,
            el: this.$('.restaurants__location__input'),
            appConfig: options.appConfig
        });

        this.vendorCollection = options.vendorCollection;

        this.listenTo(this.vendorCollection, 'reset', this._hideRestaurantsSearch);
        this.listenTo(this.fullAddressSearchView, 'full-address-search:submit', this._submitAddress);
    },

    events: function() {
        return {
            'submit': function() {
                return false;
            },
            'click .restaurants__location__change-button': '_handleClickOnLocationChangeButton',
            'keyup .restaurants__search__input': _.debounce(this._search, 150, {leading: false}),
            'click .restaurants__location__cancel-icon i': '_hidePostalCodeForm',
            'click .restaurants__search': '_handleClickOnSearchButton',
            'click .restaurants__search__cancel-icon': '_handleClickOnSearchCancelButton'
        };
    },

    render: function() {
        this.fullAddressSearchView.render();

        return this;
    },

    _submit: function() {
        this.fullAddressSearchView.submitGeocode();
        return false;
    },

    _submitAddress: function(address) {
        this.model.set(address);
        VOLO.vendorsRoute.navigateToVendorsList(address);
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
        this.fullAddressSearchView.unbind();
    },

    _handleClickOnLocationChangeButton: function() {
        this._showPostalCodeForm();
        this._hideRestaurantsSearch();
        this._displayAllRestaurants();
    },

    _showPostalCodeForm: function() {
        this._clearPostalCodeForm();
        this.$('.restaurants__tool-box').addClass('active-location-form');

        if (!this.isIE()) {
            this.$('.restaurants__location__input').focus();
        }

        return false;
    },

    _clearPostalCodeForm: function() {
        this.$('.restaurants__location__input').val('').blur();
    },

    _hideRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').removeClass('active-search');
        this.$('.restaurants__list__item').removeClass('hide');
        if (this.vendorCollection.length !== 0) {
            this._hideNotFoundMessage();
        }
        this.$('.restaurants__search__input').val('').blur();
    },

    _displayAllRestaurants: function() {
        this.vendorCollection.each(function(vendor) {
            vendor.trigger('view:show');
        });
    },

    _search: function() {
        var query = this.$('.restaurants__search__input').val(),
            results;

        if (this.vendorCollection.length > 0) {
            this._hideNotFoundMessage();
        }

        if (_.isEmpty(query)) {
            this._displayAllRestaurants();

            return;
        }

        results = this.vendorCollection.search(query);

        if (results.length === 0) {
            this._showNotFoundMessage();
        }

        this.vendorCollection.each(function(vendor) {
            _.indexOf(results, vendor.id) >= 0 ? vendor.trigger('view:show') : vendor.trigger('view:hide');
        });

        window.blazy.revalidate();
    },

    _hideNotFoundMessage: function() {
        this.$('.restaurants__search__not-found-message').addClass('hide');
    },

    _showNotFoundMessage: function() {
        this.$('.restaurants__search__not-found-message').removeClass('hide');
    },

    _hidePostalCodeForm: function() {
        this.$('.restaurants__tool-box').removeClass('active-location-form');
        this._clearPostalCodeForm();

        return false;
    },

    _handleClickOnSearchButton: function() {
        this._showRestaurantsSearch();
        this._hidePostalCodeForm();
    },

    _handleClickOnSearchCancelButton: function() {
        this._hideRestaurantsSearch();
        this._displayAllRestaurants();

        return false;
    },

    _showRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').addClass('active-search');
        this.$('.restaurants__search__input').focus();
        return false;
    }
});

_.extend(VOLO.FullAddressVendorsSearchView.prototype, VOLO.DetectIE);
