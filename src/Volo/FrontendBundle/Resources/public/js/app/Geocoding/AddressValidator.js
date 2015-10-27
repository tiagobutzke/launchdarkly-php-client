VOLO = VOLO || {};
VOLO.Geocoding = VOLO.Geocoding || {};

VOLO.Geocoding.AddressValidator = function() {};

_.extend(VOLO.Geocoding.AddressValidator.prototype, {
    isValid: function(address, appConfig) {
        var requiredFields = appConfig.address_config.required_fields,
            missingField = _.find(requiredFields, function(field) {
                return !address[field];
            });

        return !missingField;
    }
});
