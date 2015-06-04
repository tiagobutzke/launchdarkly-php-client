var DeliveryCheck = function () {
    this.ajax = null;
};

_.extend(DeliveryCheck.prototype, {

    _cancel: function () {
        if (this.ajax) {
            this.ajax.abort();
        }
    },

    isValid: function (data) {
        this._cancel();
        this.ajax = $.get(Routing.generate('vendor_delivery_validation_by_gps', data));

        return this.ajax;
    }
});
