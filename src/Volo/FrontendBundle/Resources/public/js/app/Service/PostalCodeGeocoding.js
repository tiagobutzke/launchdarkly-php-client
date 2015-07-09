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

    geocode: function(options) {
        var componentRestrictions = {
            country: this.locale
        };
        if (options.city) {
            componentRestrictions.locality = options.city;
        }
        if (options.postalCode) {
            componentRestrictions.postalCode = options.postalCode;
        }
        var requestParameters = {
            address: options.address,
            componentRestrictions: componentRestrictions
        };

        if (this.bounds) {
            requestParameters.bounds = this.bounds;
        }

        this.geocoder.geocode(requestParameters, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                var result = _.findWhere(results, {types: ['postal_code']});
                if (result) {
                    options.success(result.geometry.location);
                } else {
                    options.error(results, status);
                }
            } else {
                options.error(results, status);
            }
        });
    }
});
