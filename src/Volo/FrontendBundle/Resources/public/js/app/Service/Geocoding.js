var GeocodingService = function(locale) {
    this.autocomplete = null;
    this._listeners = [];
    this.locale = locale;
    this.defaultTypes = ['(regions)'];
};

_.extend(GeocodingService.prototype, Backbone.Events, {
    init: function ($input, config) {
        _.bindAll(this);
        this.autocomplete = new google.maps.places.Autocomplete(
            $input[0],
            {
                types: config || this.defaultTypes,
                componentRestrictions: {
                    country: this.locale
                }
            }
        );

        this._initListeners($input);
    },

    setLocation: function(latLng) {
        if (this.isNumeric(latLng.latitude) && this.isNumeric(latLng.longitude)) {
            var geolocation = new google.maps.LatLng(latLng.latitude, latLng.longitude);
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: 100000 // 100km
            });
            this.autocomplete.setBounds(circle.getBounds());
        }
    },

    isNumeric: function(str) {
        return !isNaN(parseFloat(str)) && isFinite(str);
    },

    _initListeners: function ($input) {
        this._listeners.push(this.autocomplete.addListener('place_changed', this._onPlaceChanged));
        this._listeners.push(google.maps.event.addDomListener($input[0], 'blur', this._onBlur));
    },

    _onBlur: function(e) {
        google.maps.event.trigger(e.target, 'focus');
        google.maps.event.trigger(e.target, 'keydown', {
            keyCode: 13 //enter
        });
    },

    _onPlaceChanged: function () {
        console.log('place_changed');
        var getLocation = this.getLocation();
        getLocation.done(this._onGetLocationDone);
        getLocation.fail(this._onGetLocationFail);
    },

    getLocationByZipCode: function (zipCode) {
        console.log('place_changed');
        var getLocation = this.updateByZipCode(zipCode);
        getLocation.done(this._onGetLocationDone);
        getLocation.fail(this._onGetLocationFail);
    },

    _onGetLocationDone: function(locationMeta) {
        this.trigger('autocomplete:place_changed', locationMeta);
    },

    _onGetLocationFail: function() {
        this.trigger('autocomplete:not_found');
    },

    removeListeners: function ($input) {
        console.log('removeListener ', this.autocomplete);
        google.maps.event.clearListeners($input[0]);
        //google.maps.event.clearListeners(this.autocomplete);
        _.each(this._listeners, function(listener) {
            google.maps.event.removeListener(listener);
        }, this);
        $(".pac-container").remove();
    },

    /**
     * @returns $.Deferred
     */
    getLocation: function () {
        var deferred = $.Deferred();

        this._getSearchPlace()
            .then(this._getLocationMeta, deferred.reject)
            .done(deferred.resolve);

        return deferred;
    },

    /**
     * @returns $.Deferred
     */
    updateByZipCode: function (zipCode) {
        var deferred = $.Deferred();

        this.serachByZipcode(zipCode)
            .then(this._getLocationMeta, deferred.reject)
            .done(deferred.resolve);

        return deferred;
    },

    _getSearchPlace: function () {
        var deferred = $.Deferred(),
            place = this.autocomplete.getPlace();

        console.log(place);
        if (!place || !place.place_id) {
            this._selectFirstResult().done(deferred.resolve).fail(deferred.reject);
        } else {
            deferred.resolve(place);
        }

        return deferred;
    },

    serachByZipcode: function (zipCode) {
        var geocoder = new google.maps.Geocoder();
        var deferred = $.Deferred();

        geocoder.geocode({
            "address": zipCode + '',
            componentRestrictions: {
                country: this.locale
            }
        }, function (results, status) {
            console.log(status);
            console.log(results);

            results[0].place = {
                geometry: results[0].geometry
            };

            deferred.resolve(results[0]);
        });

        return deferred;
    },

    _selectFirstResult: function () {
        var deferred = $.Deferred(),
            place = $(".pac-container .pac-item:first .pac-item-query").text(),
            region = $(".pac-container .pac-item:first span:last").text();

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({"address": [place,region].join(' ')}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                $(".pac-container .pac-item:first").addClass("pac-selected");
                $(".pac-container").css("display", "none");

                results[0].place = {
                    geometry: results[0].geometry
                };

                deferred.resolve(results[0]);
            } else {
                deferred.reject();
            }
        });

        return deferred;
    },

    _getLocationMeta: function (place) {
        var meta = this._getLocationMetaFromGeocode(place),
            deferred = $.Deferred();

        if (this._hasPostalCode(meta)) {
            meta.postcodeGuessed = false;
            deferred.resolve(meta);
        } else {
            this._enhanceLocationWithGeocode(meta).done(deferred.resolve);
        }

        return deferred;
    },

    _getLocationMetaFromGeocode: function (place) {
        console.log('place meta ', place);
        return {
            formattedAddress: place.formatted_address,
            city: this._findCityInAddressComponents(place.address_components),
            postalCode: {
                value: this._findPostalCodeInAddressComponents(place.address_components),
                isReversed: false
            },
            street: this._findFieldInAddressComponents(place.address_components, 'route'),
            building: this._findFieldInAddressComponents(place.address_components, 'street_number'),
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        };
    },

    _hasPostalCode: function (meta) {
        return !!meta.postalCode.value;
    },

    _enhanceLocationWithGeocode: function (meta) {
        var geocoder = new google.maps.Geocoder(),
            deferred = $.Deferred(),
            self = this;

        geocoder.geocode({'latLng': new google.maps.LatLng(meta.lat, meta.lng)},
            function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    meta.postalCode.value = self._findPostalCodeInGeocodeResults(results);
                    meta.postalCode.isReversed = true;
                }

                meta.postcodeGuessed = true;
                deferred.resolve(meta);
            }
        );

        return deferred;
    },

    _findPostalCodeInGeocodeResults: function (results) {
        var postalCode = null;
        _.each(results, function (result) {
            if (postalCode === null) {
                postalCode = this._findPostalCodeInAddressComponents(result.address_components);
            }
        }, this);

        return postalCode;
    },

    _findPostalCodeInAddressComponents: function (addressComponents) {
        return _.pluck(_.where(addressComponents, {'types': ['postal_code']}), 'short_name')[0];
    },

    _findCityInAddressComponents: function (addressComponents) {
        var cityName = null;

        _.each(['locality', 'administrative_area_level_2', 'administrative_area_level_3'], function(type) {
            if (!cityName) {
                cityName = this._findFieldInAddressComponents(addressComponents, type);
            }
        }, this);

        return cityName || '-';
    },

    _findFieldInAddressComponents: function(addressComponents, fieldName) {
        return _.chain(addressComponents)
            .where({types: [fieldName]})
            .pluck('long_name')
            .first()
            .thru(function(first) {
                return first || '';
            })
            .value();
    }
});
