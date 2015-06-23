var PostalCodeGeocodingService = function(locale) {
    this.locale = locale;
    this.geocoder = new google.maps.Geocoder();
};

_.extend(PostalCodeGeocodingService.prototype, Backbone.Events, {
    setLocation: function(latLng) {
        if (_.isNumber(latLng.latitude) && _.isNumber(latLng.longitude)) {
            var circle = new google.maps.Circle({
                center: new google.maps.LatLng(latLng.latitude, latLng.longitude),
                radius: 100000 // 100km
            });
            this.bounds = circle.getBounds();
        }
    },

    geoCodePostalCode: function(options) {
        var requestParameters = {
            address: options.postalCode + ', ' + options.city,
            componentRestrictions: {
                country: this.locale,
                postalCode: options.postalCode,
                locality: options.city
            }
        };

        if (this.bounds) {
            requestParameters.bounds = this.bounds;
        }

        this.geocoder.geocode(requestParameters, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var result = results[0];
                options.success(result.geometry.location);
            } else {
                options.error(results, status);
            }
        });
    }
});
