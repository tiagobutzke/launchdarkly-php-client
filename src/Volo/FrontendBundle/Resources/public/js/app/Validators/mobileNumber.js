VOLO.mobileNumberValidator = function (value) {
    return new validate.Promise(function (resolve, reject) {
        $.ajax({
            url: Routing.generate('customer_validate_phone_number', {phoneNumber: encodeURIComponent(value)}),
            success: resolve,
            error: function () {
                reject('Mobile number validation failed');
            }
        });
    });
};
