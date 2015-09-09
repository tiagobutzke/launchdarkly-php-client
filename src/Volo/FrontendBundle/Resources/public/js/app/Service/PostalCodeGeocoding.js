var PostalCodeGeocodingService = function(countryCode) {
    this.countryCode = countryCode;
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

    geocodeCenterPostalcode: function(options) {
        var deferred = $.Deferred(),
            requestParameters = this.createComponentRestrictions(options);

        if (this.bounds) {
            requestParameters.bounds = this.bounds;
        }

        this.geocoder.geocode(requestParameters, function(results, status) {
            var result = _.findWhere(results, {types: ['postal_code']});

            if (result && status === google.maps.GeocoderStatus.OK) {
                deferred.resolve(result.geometry.location, status);
            } else {
                deferred.reject(results, status);
            }
        });

        return deferred;
    },

    geocodeAddress: function(options) {
        var requestParameters = this.createComponentRestrictions(options);

        if (this.bounds) {
            requestParameters.bounds = this.bounds;
        }

        this.geocoder.geocode(requestParameters, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                options.success(results[0].geometry.location);
            } else {
                options.error(results, status);
            }
        });
    },

    createComponentRestrictions: function(options) {
        var componentRestrictions = {
            country: this.countryCode
        };
        if (options.city) {
            componentRestrictions.locality = options.city;
        }
        if (options.postalCode) {
            componentRestrictions.postalCode = options.postalCode;
        }

        return {
            address: options.address,
            componentRestrictions: _.clone(componentRestrictions)
        };
    }
});
