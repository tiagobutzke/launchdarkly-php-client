VOLO.emailApiValidator = function (value) {
    return new validate.Promise(function (resolve, reject) {
        $.ajax({
            url: Routing.generate('customer_validate_email', {email: encodeURIComponent(value)}),
            success: resolve,
            error: function () {
                reject('Email validation failed');
            }
        });
    });
};
