var VOLO = VOLO || {};
VOLO.GeocodingService = {
    attach: function(input, callbackResult) {
        callbackResult = callbackResult || function() {};

        input
            .geocomplete({
                types: ["geocode", "establishment"],
                country: VOLO.Configuration.countryCode
            })
            .bind("geocode:result", function(event, result){
                callbackResult(result.geometry);
            })
            .bind("geocode:error", function(event, result){
                // TODO: splash error
            });
    }
};

VOLO.GeocodingHandlersHome = {
    handle: function() {
        var form = $('#postal_index_form');

        VOLO.GeocodingService.attach(form.find('input[name="formatted_address"]'), function(geometry){
            var form = $('#postal_index_form');
            form.find('input[name="lat"]').val(geometry.location.A);
            form.find('input[name="lng"]').val(geometry.location.F);
            form.submit();
        });

        form.submit(function(e){
            var lat = form.find('input[name="lat"]').val();
            var lng = form.find('input[name="lng"]').val();

            if (lat === '' || lng === '') {
                alert(VOLO.Translations.home.postal_index_form_please_select_option);

                return false;
            }
        });
    }
};

VOLO.GeocodingHandlersCheckout = {
    handle: function() {
        var form = $('#checkout_form');
        VOLO.GeocodingService.attach(form.find('input[name="shipping_address_formatted"]'), function(geometry){
            form.find('input[name="lat"]').val(geometry.location.A);
            form.find('input[name="lng"]').val(geometry.location.F);
        });
    }
};
