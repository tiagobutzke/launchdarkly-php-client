var VOLO = VOLO || {};
VOLO.GeocodingService = {
    attach: function (input, callbackResult) {
        callbackResult = callbackResult || function () {
            };

        input
            .geocomplete({
                types: ["(regions)"],
                country: VOLO.Configuration.countryCode
            })
            .bind("geocode:result", function (event, result) {
                google.maps.event.clearListeners(input[0], 'blur');

                var locationMeta = {
                    formattedAddress: result.formatted_address,
                    postalCode: {
                        value: VOLO.GeocodingService.findPostalCodeInAddressComponents(result.address_components),
                        isReversed: false
                    },
                    lat: result.geometry.location.A,
                    lng: result.geometry.location.F
                };

                if (locationMeta.postalCode.value === null) {
                    var geocoder = new google.maps.Geocoder();

                    geocoder.geocode(
                        {
                            'latLng': new google.maps.LatLng(locationMeta.lat, locationMeta.lng)
                        },
                        function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                locationMeta.postalCode.value =
                                    VOLO.GeocodingService.findPostalCodeInGeocodeResults(results);
                                locationMeta.postalCode.isReversed = true;
                            }

                            if (locationMeta.postalCode.value === null) {
                                // TODO: sexy notification
                                console.log("Unfortunately we do not deliver to that area. Please select another one");
                                return;
                            }

                            callbackResult(locationMeta);
                        }
                    );

                    return true;
                }

                callbackResult(locationMeta);
            })
            .bind("geocode:error", function (event, result) {
                callbackResult(result);
            });
    },

    findPostalCodeInAddressComponents: function (addressComponents) {
        var postalCode = null;
        $.each(addressComponents, function (index, value) {
            if (value.types[0] !== undefined && value.types[0] === 'postal_code') {
                postalCode = value.short_name;
            }
        });

        return postalCode;
    },

    findPostalCodeInGeocodeResults: function (results) {
        var postalCode = null;
        $.each(results, function (i, result) {
            if (postalCode === null) {
                postalCode = VOLO.GeocodingService.findPostalCodeInAddressComponents(result.address_components);
            }
        });

        return postalCode;
    }
};

VOLO.GeocodingHandlersHome = {
    suggestionsWereShown: false,
    suggestionsAreShown: false,
    handle: function () {
        var form = $('#postal_index_form');
        var geocodingInputField = form.find('#postal_index_form_input');

        VOLO.GeocodingService.attach(geocodingInputField, function (locationMeta) {
            if (locationMeta === 'ZERO_RESULTS') {
                // TODO: sexy notification
                console.log("Unfortunately no results for your query");

                locationMeta = {
                    formattedAddress: '',
                    postalCode: {
                        value: '',
                        isReversed: false
                    },
                    lat: '',
                    lng: ''
                };
            }

            VOLO.GeocodingHandlersHome.populateFormFields(geocodingInputField, locationMeta);

            if (locationMeta.postalCode.isReversed) {
                console.log("Hey, we guess you wanted to use '" + locationMeta.postalCode.value + "' as your postal code");
            }
        });

        setInterval(function () {
            var isVisible = $('.pac-container').is(':visible');

            VOLO.GeocodingHandlersHome.suggestionsWereShown =
                VOLO.GeocodingHandlersHome.suggestionsWereShown || isVisible;
            VOLO.GeocodingHandlersHome.suggestionsAreShown = isVisible;
        }, 1000);

        geocodingInputField.unbind('keypress.geocomplete');
        geocodingInputField.on('keypress.volo', function (event) {
            if (event.keyCode === 13) {
                if (
                    VOLO.GeocodingHandlersHome.suggestionsWereShown ||
                    form.find('input[name="formatted_address"]').val() === ''
                ) {
               search_vendorggestionsWereShown = false;
                    return false;
                }

                Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps') + '?' + form.serialize());
            }
        });

        form.find('#postal_index_form_submit').click(function () {
            geocodingInputField.geocomplete('selectFirstResult')
                .geocomplete('find')
                .bind("volo:fieldsPopulated", function (locationMeta) {
                    Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps') + '?' + form.serialize());
                });
        });
    },

    populateFormFields: function (geocodingInputField, locationMeta) {
        var form = $('#postal_index_form');
        var formattedGeocodingInputFieldValue = locationMeta.formattedAddress;
        if (!formattedGeocodingInputFieldValue.match(locationMeta.postalCode.value)) {
            formattedGeocodingInputFieldValue = locationMeta.postalCode.value + " " + formattedGeocodingInputFieldValue;
        }

        geocodingInputField.val(formattedGeocodingInputFieldValue);
        form.find('input[name="formatted_address"]').val(formattedGeocodingInputFieldValue);
        form.find('input[name="post_code"]').val(locationMeta.postalCode.value);
        form.find('input[name="lat"]').val(locationMeta.lat);
        form.find('input[name="lng"]').val(locationMeta.lng);

        geocodingInputField.trigger('volo:fieldsPopulated', locationMeta);
    }
};

VOLO.GeocodingHandlersCheckout = {
    handle: function () {
        var form = $('#checkout_form');
        VOLO.GeocodingService.attach(form.find('input[name="shipping_address_formatted"]'), function (locationMeta) {
            form.find('input[name="lat"]').val(locationMeta.lat);
            form.find('input[name="lng"]').val(locationMeta.lng);
        });
    }
};
