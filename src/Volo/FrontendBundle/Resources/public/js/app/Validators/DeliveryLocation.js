VOLO.deliveryLocationValidator = function (value) {
    var service = new PostalCodeGeocodingService(VOLO.configuration.countryCode);

    return new validate.Promise(function (resolve, reject) {
        service.geocodeAddress({
            address: value
        }).then(function (res) {
            if (res) {
                resolve();
            } else {
                reject('Delivery location is not valid');
            }
        }, function () {
            reject('Delivery location is not valid');
        });
    });
};
