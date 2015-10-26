VOLO.deliveryLocationValidator = function(value) {
    var service = new PostalCodeGeocodingService(VOLO.configuration.countryCode),
        formValues = $('#delivery-information-form').serializeJSON(),
        address = _.get(formValues, 'customer_address.address_line1', '') + ' ' +
            _.get(formValues, 'customer_address.address_line2', '') + ', ' +
            _.get(formValues, 'customer_address.postcode', '') + ', ' +
            _.get(formValues, 'customer_address.city', '');

    return new Promise(function(resolve, reject) {
        service.geocodeAddress({
            address: address
        }).then(function(response) {
            if (response) {
                resolve();
            } else {
                reject('delivery not valid');
            }
        }, function() {
            reject('delivery not valid');
        });
    }.bind(this));
}.bind(this);
