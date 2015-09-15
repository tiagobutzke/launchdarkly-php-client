VOLO.CustomerModel = Backbone.Model.extend({
    defaults: {
        first_name: '',
        last_name: '',
        email: '',
        mobile_number: '',
        mobile_country_code: ''
    },

    initialize: function (data, options) {
        _.bindAll(this);

        //todo isGuest should be a method
        this.isGuest = options.isGuest;
        if (this.isGuest) {
            this.set('id', 'anon.');
        }
    },

    urlRoot: function() {
        return Routing.generate('api_customers_get');
    },

    validate: function(attrs, options) {
        if (!attrs.first_name) {
            return 'first_name not valid';
        }
        if (!attrs.last_name) {
            return 'last_name not valid';
        }
        if (!attrs.email) {
            return 'email not valid';
        }
        if (!attrs.mobile_number) {
            return 'mobile number not valid';
        }
    },

    getFullMobileNumber: function () {
        var mobileNumber = '',
            cleanMobileNumber = this.get('mobile_number');

        if (this.get('mobile_country_code') && cleanMobileNumber) {
            mobileNumber = VOLO.configuration.countryCode === 'it' ? '0' + cleanMobileNumber : cleanMobileNumber;
            mobileNumber = this.get('mobile_country_code') + ' ' + mobileNumber;
        }

        return mobileNumber;
    },

    getFullName: function() {
        if (this.isValid()) {
            return this.get('first_name') + ' ' + this.get('last_name');
        }

        return '';
    }
});
