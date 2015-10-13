var VOLO = VOLO || {};

VOLO.VendorsSearchView = HomeSearchView.extend({
    events: function() {
        return {
            'submit': '_submitPressed',
            'click #change_user_location_box_button': '_handleClickOnLocationChangeButton',
            'keyup .restaurants__search__input': _.debounce(this.search, 150, {leading: false}),
            'click .restaurants__location__cancel-icon': 'hidePostalCodeForm',
            'click .restaurants__search': '_handleClickOnSearchButton',
            'click .restaurants__search__cancel-icon': '_handleClickOnSearchCancelButton'
        };
    },

    initialize: function(options) {
        HomeSearchView.prototype.initialize.apply(this, arguments);
        this.vendorCollection = options.vendorCollection;

        this.listenTo(this.vendorCollection, 'reset', this._hideRestaurantsSearch);
    },

    /**
     * @override
     */
    postInit: $.noop,

    _applyNewLocationData: function (locationMeta) {
        HomeSearchView.prototype._applyNewLocationData.apply(this, arguments);
        this._disableInputNode();
        this._submitPressed();
    },

    _showRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').addClass('active-search');
        this.$('.restaurants__search__input').focus();
        return false;
    },

    _hideRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').removeClass('active-search');
        this.$('.restaurants__list__item').removeClass('hide');
        if (this.vendorCollection.length !== 0) {
            this._hideNotFoundMessage();
        }
        this.$('.restaurants__search__input').val('').blur();
    },

    _showPostalCodeForm: function() {
        this.$('.restaurants__tool-box').addClass('active-location-form');

        if (!this.isIE()) {
            this.$('#delivery-information-postal-index').focus();
        }

        return false;
    },

    _hidePostalCodeForm: function() {
        this.$('.restaurants__tool-box').removeClass('active-location-form');
        this.$('#delivery-information-postal-index').val('').blur();

        return false;
    },

    _handleClickOnLocationChangeButton: function() {
        this._showPostalCodeForm();
        this._hideRestaurantsSearch();
        this._displayAllRestaurants();
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

    search: function() {
        var query = this.$('.restaurants__search__input').val(),
            results;

        if (this.vendorCollection.length > 0) {
            this._hideNotFoundMessage();
        }

        if (!query) {
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

    _displayAllRestaurants: function() {
        this.vendorCollection.each(function(vendor) {
            vendor.trigger('view:show');
        });
    },

    _hideNotFoundMessage: function() {
        this.$('.restaurants__search__not-found-message').addClass('hide');
    },

    _showNotFoundMessage: function() {
        this.$('.restaurants__search__not-found-message').removeClass('hide');
    }
});

_.extend(VOLO.VendorsSearchView.prototype, VOLO.DetectIE);
