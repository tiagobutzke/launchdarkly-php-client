var VOLO = VOLO || {};

if (!validate.Promise) {
    validate.Promise = window.Promise;
}

validate.validators.deliveryLocation = VOLO.deliveryLocationValidator;
validate.validators.mobileNumber = VOLO.mobileNumberValidator;
validate.validators.emailApi = VOLO.emailApiValidator;
