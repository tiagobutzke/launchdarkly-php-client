VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.PlaceDeserializer = function() {},
_.extend(VOLO.Geocoding.PlaceDeserializer.prototype, {
    deserialize: function(geocodingPlace, appConfig) {
        var lat = geocodingPlace.geometry.location.lat(),
            lng = geocodingPlace.geometry.location.lng(),
            addressComponents = geocodingPlace.address_components || [],
            formattedAddress = geocodingPlace.formatted_address,
            postalCodeFields = _.get(appConfig, 'address_config.postal_code_field', ['postal_code']),
            streetFields = _.get(appConfig, 'address_config.street_field', ['route']);

        return {
            location_type: "polygon",
            latitude: lat,
            longitude: lng,
            postcode: this._getValueFromAddressObj(postalCodeFields, addressComponents),
            city: this._getValueFromAddressObj(['locality', 'administrative_area_level_2', 'administrative_area_level_3', 'administrative_area_level_1'], addressComponents),
            address: formattedAddress,
            formattedAddress: formattedAddress,
            street: this._getValueFromAddressObj(streetFields, addressComponents),
            building: this._getValueFromAddressObj(['street_number'], addressComponents),
            placeId: geocodingPlace.place_id
        };
    },

    _getValueFromAddressObj: function(typesArray, addressComponents) {
        //first find type which is in addressComponent
        var matchedComponentType = _.find(typesArray, function(type) {
                return _.findWhere(addressComponents, {types: [type]});
            }),
            matchedComponent;

        if (matchedComponentType) {
            //then find address component
            matchedComponent =_.find(addressComponents, function(addressComponent) {
                return _.findWhere(addressComponent.types, matchedComponentType);
            });
        }

        //return long name if we have component, else return null
        return _.get(matchedComponent, 'long_name', null);
    }
});
