var VOLO = VOLO || {};

VOLO.CheckoutDeliveryValidationView = ValidationView.extend({
    initialize: function(options) {
        ValidationView.prototype.initialize.apply(this, arguments);
        this.constraints = {
            "customer_address[postcode]": {
                presence: true,
                deliveryValidation: true
            },
            "customer_address[city]": {
                presence: true
            },
            "customer_address[address_line1]": {
                presence: true
            },
            "customer_address[address_line2]": {
                presence: true
            }
        };

        this._deliveryCheck = options.deliveryCheck;
        this._locationModel = options.locationModel;
        this._postalCodeGeocodingService = options.postalCodeGeocodingService;

        this._createDeliveryAsyncValidation();
        this._toggleContinueButton();
    },

    events: function() {
        return _.extend({}, ValidationView.prototype.events, {
            'click button': $.noop
        });
    },

    submit: function(callback) {
        this._validateForm().done(callback);
    },

    _toggleContinueButton: function() {
        var doValidate = this._doValidate();

        doValidate.then(function() {
            this.$("#delivery-information-form-button").removeClass('button--disabled');
        }.bind(this), function() {
            this.$("#delivery-information-form-button").addClass('button--disabled');
        }.bind(this));
    },

    _validateField: function (e) {
        var target = e.target,
            $target = $(target),
            value = target.value || '',
            doValidate = this._doValidate();

        doValidate.then(
            function() {
                this.$("#delivery-information-form-button").removeClass('button--disabled');
            }.bind(this),
            function(invalidObj) {
                if (invalidObj) {
                    this.$("#delivery-information-form-button").addClass('button--disabled');
                }

                if (invalidObj && invalidObj[target.name] && value !== '') {
                    this._displayMessage(target);
                }

                $target.toggleClass(this.inputErrorClass, !!invalidObj);
            }.bind(this));
    },

    _validateForm: function() {
        var doValidate = this._doValidate(),
            deferred = $.Deferred();

        doValidate.then(deferred.resolve, function(errors) {
            _.each(this.$("input[name], select[name]"), function(input) {
                if (errors[input.name]) {
                    this._displayMessage(input);
                }
            }, this);

            deferred.reject();
        }.bind(this));

        return deferred;
    },

    _doValidate: function() {
        var formValues = validate.collectFormValues(this.el);

        return validate.async(formValues, this.constraints);
    },

    _createDeliveryAsyncValidation: function() {
        if (!validate.validators.deliveryValidation) {
            validate.validators.deliveryValidation = function() {
                return new Promise(function(resolve, reject) {
                    this._geoCodeAndValidateDelivery().then(function(res) {
                        if (res) {
                            resolve();
                        } else {
                            reject('delivery not valid');
                        }
                    });
                }.bind(this));
            }.bind(this);
        }
    },

    _geoCodeAndValidateDelivery: function() {
        return this._geocodePostalCode({
            city: this.$('#delivery-information-city').val(),
            postcode: this.$('#delivery-information-postal-index').val()
        });
    },

    isValidForm: function() {
        return this.$('#delivery-information-address-line1').val().length > 0 && this.$('#delivery-information-address-line2').val().length > 0 &&
            this.$('#delivery-information-postal-index').val().length > 0 && this.$('#delivery-information-city').val().length > 0;
    },

    _geocodePostalCode: function(locationData) {
        var deferred = $.Deferred();

        console.log('_geocodePostalCode ', this.cid, locationData);
        this._geocodeAddress(); //todo remove this side effect

        var getPostalCode = this._postalCodeGeocodingService.geocodeCenterPostalcode({
            address: locationData.postcode + ", " + locationData.city,
            postalCode: locationData.postcode,
            city: locationData.city
        });

        getPostalCode.done(function(result) {
            this._validateDelivery({lat: result.lat(), lng: result.lng()}).then(deferred.resolve);
        }.bind(this));

        getPostalCode.fail(function(results, status) {
            if (_.isString(status) && status === 'ZERO_RESULTS') {
                deferred.resolve(false);
            } else {
                this._validateDelivery({lat: this._locationModel.get('latitude'), lng: this._locationModel.get('longitude')}).done(deferred.resolve).done(function(result) {
                    deferred.resolve(this.isValidForm() && result);
                }.bind(this));
            }
        }.bind(this));

        return deferred;
    },

    _geocodeAddress: function() {
        if (!this.isValidForm()) return;

        this._postalCodeGeocodingService.geocodeAddress({
            address: this.$('#delivery-information-address-line1').val() + ' ' + this.$('#delivery-information-address-line2').val() + ', ' + this.$('#delivery-information-postal-index').val() + ', ' + this.$('#delivery-information-city').val(),

            success: function(result) {
                console.log(result);
                this.$("#delivery-information-address-latitude").val(result.lat());
                this.$("#delivery-information-address-longitude").val(result.lng());
            }.bind(this),

            error: function() {
                this.$("#delivery-information-address-latitude").val('');
                this.$("#delivery-information-address-longitude").val('');
            }.bind(this)
        });
    },

    _validateDelivery: function (locationData) {
        var deferred = $.Deferred(),
            deliveryCheckData = {
                vendorId: this.$('#delivery-information-postal-index').data('vendor_id'),
                latitude: locationData.lat,
                longitude: locationData.lng
            };
        this._deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                deferred.resolve(!!resultData.result);
            });

        return deferred;
    }
});
