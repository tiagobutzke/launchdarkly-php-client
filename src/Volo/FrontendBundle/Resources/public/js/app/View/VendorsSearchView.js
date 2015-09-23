var VendorsSearchView = HomeSearchView.extend({
    events: {
        'submit': '_submitPressed',
        'click #change_user_location_box_button': function() {
            this.showPostalCodeForm();
            this.hideRestaurantsSearch();
        },
        'keyup .restaurants__search__input': 'search',
        'click .restaurants__location__cancel-icon': 'hidePostalCodeForm',
        'click .restaurants__search': function() {
            this.showRestaurantsSearch();
            this.hidePostalCodeForm();
        },
        'click .restaurants__search__cancel-icon': 'hideRestaurantsSearch'
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

    showRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').addClass('active-search');
        this.$('.restaurants__search__input').focus();
        return false;
    },

    hideRestaurantsSearch: function() {
        this.$('.restaurants__tool-box').removeClass('active-search');
        this.$('.restaurants__list__item').removeClass('hide');
        this.$('.restaurants__search__input').val('').blur();
        return false;
    },

    showPostalCodeForm: function() {
        this.$('#delivery-information-postal-index-form').removeClass('hide');
        if (!this.isIE()) {
            this.$('#delivery-information-postal-index').focus();
        }
        this.$('.restaurants__location__title').hide();

        return false;
    },

    hidePostalCodeForm: function() {
        this.$('#delivery-information-postal-index-form').addClass('hide');
        this.$('#delivery-information-postal-index').val('').blur();
        this.$('.restaurants__location__title').show();

        return false;
    },

    search: function () {
        var query = this.$('.restaurants__search__input').val(),
            words = _.map(_.words(query), function (e) {return e.toLowerCase();}),
            restaurants = this.$('.restaurants__list__item');

        this.$('.restaurants__search__not-found-message').addClass('hide');
        if (query.length < 2) {
            restaurants.removeClass('hide');
            return;
        }

        restaurants.addClass('hide');
        restaurants.each(function(key, item) {
            var data = _.chain($(item).data('search'))
                    .values()
                    .flattenDeep()
                    .compact()
                    .map(function (e) {return e.toLowerCase();})
                    .value()
                    .join(' '),
                matches = _.filter(words, function (w) {
                    return data.indexOf(w) != -1;
                });

            if (matches.length === words.length) {
                $(item).removeClass('hide');
            }
        });

        this.$('.restaurants__search__not-found-message').toggleClass('hide', restaurants.not('.hide').length > 0);
    }
});

_.extend(VendorsSearchView.prototype, VOLO.DetectIE);
