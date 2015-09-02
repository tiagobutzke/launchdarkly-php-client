var VendorsSearchView = HomeSearchView.extend({
    events: {
        'submit': '_submitPressed',
        "click #change_user_location_box_button": "buttonClick"
    },

    render: function() {
        if (!this.isIE()) {
            this.$('.home__teaser__form-input').focus();
        }
    },

    /**
     * @override
     */
    postInit: function() {
    },

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
    }
});

_.extend(VendorsSearchView.prototype, VOLO.DetectIE);
