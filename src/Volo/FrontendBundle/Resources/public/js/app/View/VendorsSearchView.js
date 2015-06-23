var VendorsSearchView = HomeSearchView.extend({
    events: {
        'submit': '_submitPressed',
        "click #change_user_location_box_button": "buttonClick"
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
        this.$('#postal_index_form_input').removeClass('hide').focus();
        this.$('.restaurants__location__title').hide();

        return false;
    }
});
