var TimePickerView = Backbone.View.extend({
    events: {
        'change #order-delivery-time': 'updateDeliveryTime',
        'change #order-delivery-date': 'updateDeliveryTime'
    },

    initialize: function () {
        this.vendor_id = this.$el.data().vendor_id;

        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'change:orderTime', this.render, this);
    },

    render: function () {
        var date = this.model.getCart(this.vendor_id).get('orderTime');

        if (_.isDate(date)) {
            var dateKey = date.toISOString().split('T')[0],
                timeKey = date.toTimeString().substring(0, 5);

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
        }
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
    },

    updateDeliveryTime: function () {
        var vendorCart = this.model.getCart(this.vendor_id),
            time = this.$('#order-delivery-time').val().split(':'),
            date = this.$('#order-delivery-date').val().split('-'),
            datetime = new Date(date[0], date[1] - 1, date[2], time[0], time[1]);

        vendorCart.set('orderTime', datetime);
        vendorCart.updateCart();
    }
});
