var GeocodingService = function(locale) {
    var autocomplete = null;

    var init = function($input) {
        autocomplete = new google.maps.places.Autocomplete(
            $input[0],
            {
                types: ['(regions)'],
                componentRestrictions: {
                    country: locale
                }
            }
        );

        _initListeners($input);
    };

    var _initListeners = function($input) {
        google.maps.event.addDomListener($input[0], 'keydown', function(e) {
            if (e.keyCode === 13) { //enter
                $input.trigger('autocomplete:submit_pressed');
            } else if (e.keyCode === 9) { //tab
                $input.trigger('autocomplete:tab_pressed');
            }
        });

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            $input.trigger('autocomplete:place_changed');
        });

        google.maps.event.addDomListener($input[0], 'blur', function() {
            google.maps.event.trigger(this, 'keydown', {
                keyCode: 9
            });

            $input.trigger('autocomplete:place_changed');
        });
    };

    /**
     * @returns $.Deferred
     */
    var getLocation = function($input) {
        var deferred = $.Deferred();

        _getSearchPlace($input, autocomplete)
            .then(_getLocationMeta, deferred.reject)
            .done(deferred.resolve);

        return deferred;
    };

    var _getSearchPlace = function($input, autocomplete) {
        var deferred = $.Deferred(),
            place = autocomplete.getPlace();

        if (!place || !place.place_id) {
            _selectFirstResult($input).done(deferred.resolve).fail(deferred.reject);
        } else {
            deferred.resolve(place);
        }

        return deferred;
    };

    var _selectFirstResult = function($input) {
        var deferred = $.Deferred(),
            firstResult = $(".pac-container .pac-item:first").text();

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({"address":firstResult }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                $(".pac-container .pac-item:first").addClass("pac-selected");
                $(".pac-container").css("display","none");

                results[0].place = {
                    geometry: results[0].geometry
                };

                deferred.resolve(results[0]);
            } else {
                deferred.reject();
            }
        });

        return deferred;
    };

    var _getLocationMeta = function(place) {
        var meta = _getLocationMetaFromGeocode(place),
            deferred = $.Deferred();

        if (!_hasPostalCode(meta)) {
            _enhanceLocationWithGeocode(meta).done(deferred.resolve);
        } else {
            deferred.resolve(meta);
        }

        return deferred;
    };

    var _getLocationMetaFromGeocode = function(place) {
        return {
            formattedAddress: place.formatted_address,
            city: _findLocalityInAddressComponents(place.address_components),
            postalCode: {
                value: _findPostalCodeInAddressComponents(place.address_components),
                isReversed: false
            },
            lat: place.geometry.location.A,
            lng: place.geometry.location.F
        };
    };

    var _hasPostalCode = function(meta) {
        return !!meta.postalCode.value;
    };

    var _enhanceLocationWithGeocode = function(meta) {
        var geocoder = new google.maps.Geocoder(),
            deferred = $.Deferred();

        geocoder.geocode({'latLng': new google.maps.LatLng(meta.lat, meta.lng)},
            function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    meta.postalCode.value = _findPostalCodeInGeocodeResults(results);
                    meta.postalCode.isReversed = true;
                }

                deferred.resolve(meta);
            }
        );

        return deferred;
    };

    var _findPostalCodeInGeocodeResults = function(results) {
        var postalCode = null;
        $.each(results, function (i, result) {
            if (postalCode === null) {
                postalCode = _findPostalCodeInAddressComponents(result.address_components);
            }
        });

        return postalCode;
    };

    var _findPostalCodeInAddressComponents = function (addressComponents) {
        return _.pluck(_.where(addressComponents, {'types': ['postal_code']}), 'short_name')[0];
    };

    var _findLocalityInAddressComponents = function (addressComponents) {
        return _.pluck(_.where(addressComponents, {'types': ['locality']}), 'long_name')[0];
    };

    return {
        getLocation: getLocation,
        init: init
    };
};
