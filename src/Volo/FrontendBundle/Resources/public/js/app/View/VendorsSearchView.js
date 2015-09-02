var VendorsSearchView = HomeSearchView.extend({
    events: {
        'submit': '_submitPressed',
        "click #change_user_location_box_button": "buttonClick",
        "keyup .restaurants__search__input": "search"
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

    buttonClick: function() {
        var $postal_index_form_input = $('#delivery-information-postal-index');

        $postal_index_form_input.removeClass('hide');
        if (!this.isIE()) {
            $postal_index_form_input.focus();
        }
        this.$('.restaurants__location__title').hide();

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
