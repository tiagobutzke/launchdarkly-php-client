var TimePickerView = Backbone.View.extend({
    events: {
        'change #order-delivery-time': 'updateDeliveryTime',
        'change #order-delivery-date': 'updateDeliveryTime'
    },

    initialize: function (options) {
        _.bindAll(this);
        this.template = _.template($('#template-time-picker').html());
        this.vendor_id = options.vendor_id;

        var vendorCart = this.model.getCart(this.vendor_id);
        this.listenTo(vendorCart, 'change:order_time', this.render, this);
    },

    render: function () {
        console.log('TimePickerView.render ', this.cid);
        this.$el.html(this.template());

        var md = new MobileDetect(window.navigator.userAgent);
        if (md.mobile()) {
            this.$('select').selectpicker('mobile');
        } else {
            this.$('select').selectpicker();
        }

        var date = this.model.getCart(this.vendor_id).get('order_time');

        if (date) {
            var dateObj = date === 'now' ? moment() : moment(date),
                dateKey = dateObj.format('YYYY-MM-DD'),
                timeKey = dateObj.format('HH:mm');

            if ($("#order-delivery-date option[value='" + dateKey + "']").length > 0) {
                this.$('#order-delivery-date').val(dateKey);
            }

            this.renderTimeSelect();

            if ($("#order-delivery-time option[value='" + timeKey + "']").length > 0) {
                this.$('#order-delivery-time').val(timeKey);
            }
        } else {
            this.model.getCart(this.vendor_id).set('order_time', this._getDateFromForm());
        }

        this.$('select').selectpicker('refresh');

        return this;
    },

    renderTimeSelect: function () {
        this.$('#order-delivery-time').empty();
        _.forEach(this.$('#order-delivery-date option:selected').data('delivery-hours'), function (element, key) {
            this.$('#order-delivery-time').append(
                $('<option/>').val(key).text(element)
            );
        }, this);

        this.$('select').selectpicker('refresh');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
        this.$('select').selectpicker('destroy');
    },

    updateDeliveryTime: function () {
        var vendorCart = this.model.getCart(this.vendor_id);
        var data = this.$('#order-delivery-date option:selected').data('delivery-hours');

        if (_.isUndefined(data[this.$('#order-delivery-time').val()])) {
            this.renderTimeSelect();
        }

        vendorCart.set('order_time', this._getDateFromForm());
        vendorCart.updateCart();
    },

    _getDateFromForm: function() {
        var time = this.$('#order-delivery-time').val(),
            date = this.$('#order-delivery-date').val();

        return time === 'now' ? time : moment(date+time, "YYYY-MM-DDHH:mm").tz(VOLO.configuration.timeZone).format();
    }
});
