VOLO.mobileNumberValidator = function (value) {
    return new validate.Promise(function (resolve, reject) {
        if (!value) {
            return reject('Mobile number validation failed because null');
        }

        $.ajax({
            url: Routing.generate('customer_validate_phone_number', {phoneNumber: encodeURIComponent(value)}),
            success: resolve,
            error: function () {
                reject('Mobile number validation failed');
            }
        });
    });
};
