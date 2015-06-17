var TimePickerView = Backbone.View.extend({
    events: {
        'change #order-delivery-time': 'updateDeliveryTime',
        'change #order-delivery-date': 'updateDeliveryTime'
    },

    initialize: function () {
        this.vendor_id = this.$el.data().vendor_id;

        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'change:order_time', this.render, this);
    },

    render: function () {
        var date = this.model.getCart(this.vendor_id).get('order_time');

        if (date) {
            var dateObj = date === 'now' ? moment() : moment(date),
                dateKey = dateObj.format('YYYY-MM-DD'),
                timeKey = dateObj.format('HH:mm');

            if ($("#order-delivery-date option[value='" + dateKey + "']").length > 0) {
                this.$('#order-delivery-date').val(dateKey);
            }

            this.$('#order-delivery-time').empty();
            _.forEach(this.$('#order-delivery-date option:selected').data('delivery-hours'), function (element, key) {
                this.$('#order-delivery-time').append(
                    $('<option/>').val(key).text(element)
                );
            }, this);

            if ($("#order-delivery-time option[value='" + timeKey + "']").length > 0) {
                this.$('#order-delivery-time').val(timeKey);
            }
        } else {
            this.model.getCart(this.vendor_id).set('order_time', this._getDateFromForm());
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    updateDeliveryTime: function () {
        var vendorCart = this.model.getCart(this.vendor_id);

        vendorCart.set('order_time', this._getDateFromForm());
        vendorCart.updateCart();
    },

    _getDateFromForm: function() {
        var time = this.$('#order-delivery-time').val(),
            date = this.$('#order-delivery-date').val();

        return time === 'now' ? time : moment(date+time, "YYYY-MM-DDHH:mm").toISOString();
    }
});
