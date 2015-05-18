var GeocodingHandlersHome = {
    handle: function(form) {
        form.submit(function(e){
            var lat = form.find('input[name="lat"]').val();
            var lng = form.find('input[name="lng"]').val();

            if (lat === '' || lng === '') {
                alert(VOLOTranslations.home.postal_index_form_please_select_option);

                return false;
            }
        });
    }
};
