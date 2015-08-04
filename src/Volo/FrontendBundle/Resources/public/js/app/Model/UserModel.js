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
        this.isGuest = options.isGuest;
        if (this.isGuest) {
            this.set('id', 'anon.');
        }
    },

    localStorage: function () {
        if (this.isGuest) {
            return new Backbone.LocalStorage('CustomerModel');
        }

        return false;
    },

    urlRoot: function() {
        return Routing.generate('api_customers_update');
    },

    validate: function(attrs, options) {
        if (!_.isString(attrs.first_name) || attrs.first_name.length === 0) {
            return 'not valid';
        }
    },

    getFullMobileNumber: function () {
        var mobileNumber = '';
        if (this.get('mobile_country_code') && this.get('mobile_number')) {
            mobileNumber = '+' + this.get('mobile_country_code') + ' '  + this.get('mobile_number');
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
