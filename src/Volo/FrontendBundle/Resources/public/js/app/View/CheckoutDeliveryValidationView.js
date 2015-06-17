var CheckoutDeliveryValidationView = Backbone.View.extend({
    events: {
        'focus #formatted_address': '_hideTooltip',
        'click': '_hideTooltip'
    },

    initialize: function (options) {
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        this.vendorId = this.$('#postal_index_form_input').data('vendor_id');
        this.geocodingService.init(this.$('#formatted_address'), []);
        this.deliveryCheck = options.deliveryCheck;
        this._jsValidationView = new ValidationView({
            el: this.el,
            constraints: {
                "customer_address[postcode]": {
                    presence: true
                },
                "customer_address[city]": {
                    presence: true
                },
                "formatted_address": {
                    presence: true
                }
            }
        });
        this.$('#formatted_address').tooltip({
            placement: 'top',
            html: true,
            trigger: 'manual'
        });

        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
    },

    unbind: function () {
        this._jsValidationView.unbind();
        this.geocodingService.removeListeners(this.$('#postal_index_form_input'));
        this.stopListening();
        this.undelegateEvents();
    },

    _tabPressed: function () {
        this._getNewLocation(this.$('#postal_index_form_input')).fail(this._notFound);
    },

    _notFound: function () {
        console.log('not found');
    },

    _getNewLocation: function ($input) {
        var deferred = $.Deferred();
        var continueButton = this.$("#delivery_information_form_button");
        this.geocodingService.getLocation($input)
            .fail(deferred.reject, this)
            .done(function (locationMeta) {
                var data = this._getDataFromMeta(locationMeta, $input);
                var addressLine1 = data.street + ' ' + data.building;

                this.$("#city").val(data.city);
                this.$('#postal_index_form_input').val(data.postcode);
                this.$('#formatted_address').val($.trim(addressLine1));
                this.$('#address_line1').val(data.street);
                this.$('#address_line2').val(data.building);
                this.$('#address_latitude').val(data.lat);
                this.$('#address_longitude').val(data.lng);
                this._validateDelivery(
                    data,
                    continueButton
                );
                deferred.resolve(data);
            }.bind(this));

        return deferred;
    },

    _getDataFromMeta: function (locationMeta, $input) {
        console.log(locationMeta);
        var postCode = (locationMeta.postalCode && locationMeta.postalCode.value) || $input.val();

        var formattedAddress = postCode + ", " + locationMeta.city;

        return {
            formattedAddress: formattedAddress,
            postcode: postCode,
            building: locationMeta.building,
            street: locationMeta.street,
            lat: locationMeta.lat,
            lng: locationMeta.lng,
            city: locationMeta.city
        };
    },

    _hideTooltip: function () {
        this.$('#formatted_address').tooltip('hide');
    },

    _showInputPopup: function (inputNode, text, isBlocking) {
        inputNode.attr({
            'data-is-blocking-popup': isBlocking !== undefined ? isBlocking : false,
            'title': text
        }).tooltip('fixTitle');
        inputNode.tooltip('show');
    },

    _validateAddressFields: function () {
        if (this.$('#address_line1').val() === '' || this.$('#address_line2').val() === '') {
            this._showInputPopup(
                this.$('#formatted_address'),
                this.$('#formatted_address').data('msg_ensure_full_address')
            );
        }
    },

    _validateDelivery: function (locationData, continueButton) {
        if ($('#delivery-modal').hasClass('in')) {
            return;
        }

        continueButton.attr('disabled', true);
        this._hideTooltip();
        this._validateAddressFields();

        var deliveryCheckData = {
            vendorId: this.vendorId,
            latitude: locationData.lat,
            longitude: locationData.lng
        };
        this.deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                if (resultData.result === true) {
                    continueButton.attr('disabled', false);
                    if (this.$('#formatted_address').data('is-blocking-popup')) {
                        this._hideTooltip();
                    }
                } else {
                    this._showInputPopup(
                        this.$('#formatted_address'),
                        this.$('#formatted_address').data('validation-msg'),
                        true
                    );
                }
            }.bind(this));
    }
});
