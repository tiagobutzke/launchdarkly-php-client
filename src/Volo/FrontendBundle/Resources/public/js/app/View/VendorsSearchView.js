var VendorsSearchView = HomeSearchView.extend({
    events: {
        "click #change_user_location_box_button": "buttonClick"
    },

    buttonClick: function() {
        this.$('#postal_index_form_input').removeClass('hide').focus();
        this.$('.restaurants__location__title').hide();

        return false;
    }
});
