var CheckoutDeliveryValidationView = Backbone.View.extend({
    events: {},

    initialize: function (options) {
        _.bindAll(this);
        this.geocodingService = options.geocodingService;
        var $input = this.$('#postal_index_form_input');
        this.vendorId = $input.data('vendor_id');
        this.continueButton = this.$("#delivery_information_form_button");
        this.geocodingService.init($input);
        this.deliveryCheck = options.deliveryCheck;
        this.listenTo(this.geocodingService, 'autocomplete:submit_pressed', this._submitPressed);
        this.listenTo(this.geocodingService, 'autocomplete:place_changed', this._tabPressed);
        this.listenTo(this.geocodingService, 'autocomplete:tab_pressed', this._tabPressed);
    },

    unbind: function () {
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
        var continueButton = this.continueButton;
        this.geocodingService.getLocation($input)
            .fail(deferred.reject, this)
            .done(function (locationMeta) {
                var data = this._getDataFromMeta(locationMeta, $input);
                $input.val(data.postcode);
                this.$("#city").val(data.city);
                this._validateDelivery(
                    data,
                    continueButton
                );
                deferred.resolve(data);
            }.bind(this));

        return deferred;
    },

    _getDataFromMeta: function (locationMeta, $input) {
        var formattedAddress = locationMeta.formattedAddress;

        var postCode = (locationMeta.postalCode && locationMeta.postalCode.value) || $input.val();

        if (!formattedAddress.match(postCode)) {
            formattedAddress = postCode + " " + formattedAddress;
        }

        return {
            formattedAddress: formattedAddress,
            postcode: postCode,
            lat: locationMeta.lat,
            lng: locationMeta.lng,
            city: locationMeta.city
        };
    },

    _validateDelivery: function (locationData, continueButton) {
        continueButton.attr('disabled', true);

        var deliveryCheckData = {
            "vendorId": this.vendorId,
            "latitude": locationData.lat,
            "longitude": locationData.lng
        };

        this.deliveryCheck.isValid(deliveryCheckData)
            .done(function (resultData) {
                if (resultData.result === true) {
                    continueButton.attr('disabled', false);
                } else {
                    var view = new CheckoutNoDeliveryView({
                        el: '.modal-dialogs',
                        locationData: locationData
                    });
                    view.render(); //render dialog
                    $('#delivery-modal').modal();
                }
            });
    }
});

var CheckoutNoDeliveryView = Backbone.View.extend({
    initialize: function (options) {
        this.template = _.template($('#template-delivery-modal').html());
        this.locationData = options.locationData;
    },

    events: {
        'click .delivery-error__find-restaurants': '_findRestaurants',
        'click .modal-close-button': '_closeModal'
    },

    render: function () {
        this.$el.html(this.template());

        return this;
    },

    _findRestaurants: function () {
        var data = this.locationData;
        Turbolinks.visit(Routing.generate('volo_location_search_vendors_by_gps', {
            city: data.city,
            address: data.formattedAddress,
            longitude: data.lng,
            latitude: data.lat,
            postcode: data.postcode
        }));
    },

    _closeModal: function () {
        this.undelegateEvents();
        this.$('#delivery-modal').modal('hide');
    }
});

