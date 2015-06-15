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
        var dateObj = this.model.getCart(this.vendor_id).get('order_time');

        if (_.isDate(dateObj)) {
            var dateKey = dateObj.toISOString().split('T')[0],
                timeKey = dateObj.toTimeString().substring(0, 5);

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
            var time = this.$('#order-delivery-time').val().split(':'),
                date = this.$('#order-delivery-date').val().split('-');

            this.model.getCart(this.vendor_id).set(
                'order_time',
                new Date(date[0], date[1] - 1, date[2], time[0], time[1])
            );
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

        vendorCart.set('order_time', datetime);
        vendorCart.updateCart();
    }
});
