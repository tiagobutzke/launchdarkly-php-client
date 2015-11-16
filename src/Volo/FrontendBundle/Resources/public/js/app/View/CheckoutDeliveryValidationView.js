var VOLO = VOLO || {};

VOLO.CheckoutDeliveryValidationView = ValidationView.extend({
    initialize: function(options) {
        ValidationView.prototype.initialize.apply(this, arguments);
        this.constraints = {
            "customer_address[postcode]": {
                presence: true
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
    },

    events: function() {
        return _.extend({}, ValidationView.prototype.events.apply(this), {
            'blur input[name], select[name]': $.noop(),
            'keyup input[name]': _.debounce(this._validateField, 300, {
                leading: false
            }),
            'click button': $.noop
        });
    },

    submit: function(callback) {
        this._validateForm()
            .then(this._geoCodeAndValidateDelivery, this._disableContinueButton)
            .then(_.curry(this._afterValidate, 2)(callback), this._disableContinueButton);
    },

    validateForm: function() {
        this._doValidate().then(this._continueCheckout, this._disableContinueButton);
    },

    _continueCheckout: function () {
        this._geoCodeAndValidateDelivery()
            .then(_.curry(this._afterValidate, 2)(this._enableContinueButton), this._disableContinueButton);
    },

    _afterValidate: function(callback, result) {
        if (result) {
            this._hideNotValidAddressError();
            callback(result);
        } else {
            this._displayNotValidAddressError();
            this._disableContinueButton();
        }
    },

    _displayNotValidAddressError: function() {
        this.$('.form__error-message--address-not-valid').removeClass('hide');
    },

    _hideNotValidAddressError: function() {
        this.$('.form__error-message--address-not-valid').addClass('hide');
    },

    _enableContinueButton: function() {
        this.$("#delivery-information-form-button").removeClass('button--disabled');
    },

    _disableContinueButton: function() {
        this.$("#delivery-information-form-button").addClass('button--disabled');
    },

    _validateField: function (e) {
        var target = e.target,
            $target = $(target),
            value = target.value || '',
            doValidate = this._doValidate();

        doValidate.then(this._continueCheckout,
            function(invalidObj) {
                if (invalidObj) {
                    this._disableContinueButton();
                }

                if (invalidObj && invalidObj[target.name] && value !== '') {
                    this._displayMessage(target);
                }

                $target.toggleClass(this.inputErrorClass, !!invalidObj);
            }.bind(this));
    },

    _validateForm: function() {
        var doValidate = this._preValidateLocation().then(this._doValidate),
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

    _preValidateLocation: function () {
        var address = this._getAddressValues();

        return validate.async({address: address}, {address: {deliveryLocation: true}});
    },

    _doValidate: function () {
        var formValues = validate.collectFormValues(this.el);

        return validate.async(formValues, this.constraints);
    },

    _geoCodeAndValidateDelivery: function() {
        return this._geocode({
            city: this.$('#delivery-information-city').val(),
            postcode: this.$('#delivery-information-postal-index').val()
        });
    },

    isValidForm: function() {
        return this.$('#delivery-information-address-line1').val().length > 0 && this.$('#delivery-information-address-line2').val().length > 0 &&
            this.$('#delivery-information-postal-index').val().length > 0 && this.$('#delivery-information-city').val().length > 0;
    },

    _geocode: function(locationData) {

        console.log('_geocode ', this.cid, locationData);
        //todo inject it, don't use global objects

        var addressGeocode = this._geocodeAddress();
        if (VOLO.isFullAddressAutoComplete()) {
            var deferred = $.Deferred();

            addressGeocode.done(function(result) {
                this._validateDelivery({lat: result.lat(), lng: result.lng()}).done(deferred.resolve);
            }.bind(this));

            addressGeocode.fail(function(result) {
                if (result && result === 'form not valid') {
                    deferred.resolve(true);
                } else {
                    deferred.resolve(false);
                }
            });

            return deferred;
        } else {
            return this._geocodePostalCode(locationData);
        }
    },

    _geocodePostalCode: function(locationData) {
        var deferred = $.Deferred(),
            getPostalCode = this._postalCodeGeocodingService.geocodeCenterPostalcode({
            address: locationData.postcode + ", " + locationData.city,
            postalCode: locationData.postcode,
            city: locationData.city
        });

        getPostalCode.done(function(result) {
            this._validateDelivery({lat: result.lat(), lng: result.lng()}).then(function(result) {
                console.log(result);
                deferred.resolve(result);
            });
        }.bind(this));

        getPostalCode.fail(function(results, status) {
            if (_.isString(status) && status === 'ZERO_RESULTS') {
                deferred.resolve(false);
            } else {
                this._validateDelivery({lat: this._locationModel.get('latitude'), lng: this._locationModel.get('longitude')}).done(function(result) {
                    deferred.resolve(this.isValidForm() && result);
                }.bind(this));
            }
        }.bind(this));

        return deferred;
    },

    _getAddressValues: function() {
        return this.$('#delivery-information-address-line1').val() + ' ' +
            this.$('#delivery-information-address-line2').val() + ', ' +
            this.$('#delivery-information-postal-index').val() + ', ' +
            this.$('#delivery-information-city').val();
    },

    _geocodeAddress: function() {
        var deferred = $.Deferred();

        if (this.isValidForm()) {
            var address = this._getAddressValues(),
                geocode = this._postalCodeGeocodingService.geocodeAddress({
                    address: address
                });

            console.log('getting lat/lng for: ' + address);

            geocode.done(function(result) {
                console.log('lat/lng for ' + address);
                console.log(result.lat(), result.lng());
                this.$("#delivery-information-address-latitude").val(result.lat());
                this.$("#delivery-information-address-longitude").val(result.lng());
                deferred.resolve(result);
            }.bind(this));

            geocode.fail(function() {
                this.$("#delivery-information-address-latitude").val('');
                this.$("#delivery-information-address-longitude").val('');
                deferred.reject();
            }.bind(this));
        } else {
            deferred.reject('form not valid');
        }

        return deferred;
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
