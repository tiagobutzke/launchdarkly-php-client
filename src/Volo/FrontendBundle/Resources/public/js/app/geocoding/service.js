var GeocodingService = {
    attach: function(form) {
        form.find('input[name="formatted_address"]')
            .geocomplete({
                types: ["geocode", "establishment"]
            })
            .bind("geocode:result", function(event, result){
                location = Routing.generate(
                    'search_vendors',
                    {
                        lat: result.geometry.location.A,
                        lng: result.geometry.location.F
                    }
                );
                form.find('input[name="lat"]').val(result.geometry.location.A);
                form.find('input[name="lng"]').val(result.geometry.location.F);
            })
            .bind("geocode:error", function(event, result){
                // TODO: splash error
            });
    }
};
