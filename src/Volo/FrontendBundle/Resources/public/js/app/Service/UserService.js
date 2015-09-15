VOLO = VOLO || {};

VOLO.UserService = function() {};

_.extend(VOLO.UserService.prototype, {
    register: function(userData, addressData) {
        var requestData = this._prepareData(userData, addressData);

        return $.ajax({
            type: "POST",
            url: Routing.generate('customer.create'),
            data: requestData
        });
    },

    login: function(userData, addressData) {
        var requestData = this._prepareData(userData, addressData);

        return $.ajax({
            type: 'POST',
            url: Routing.generate('login_check'),
            data: requestData
        });
    },

    _getGuestAddressFormat: function(address) {
        var result = {};

        _.each(_.keys(address), function(key) {
            result['guest_address['+key+']'] = address[key];
        });

        return result;
    },

    _prepareData: function(userData, addressData) {
        userData =  userData || {};
        addressData = addressData || {};

        return _.extend({}, userData, this._getGuestAddressFormat(addressData));
    }
});


