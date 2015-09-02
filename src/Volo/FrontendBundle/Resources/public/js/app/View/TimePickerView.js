var TimePickerView = Backbone.View.extend({
    events: {
        'change #order-delivery-time': 'updateDeliveryTime',
        'change #order-delivery-date': 'updateDeliveryTime'
    },

    initialize: function (options) {
        var vendorCart = this.model.getCart(options.vendor_id);
        _.bindAll(this);

        this.mobileDetect = new MobileDetect(window.navigator.userAgent);
        this.values = options.values;
        this.template = _.template($('#template-time-picker').html());
        this.vendor_id = options.vendor_id;

        if (options.minDeliveryTime) {
            var check = function () {
                var time = vendorCart.get('order_time');
                if (time !== 'now' && moment(time).isBefore(moment().add(options.minDeliveryTime, 'minutes'))) {
                    this.refreshValues().done(_.flow(this.render, this.updateDeliveryTime, _.wrap(this.showSelectionChangedTooltip, _.defer)));
                }
            }.bind(this);

            setInterval(check, 60000);
            check();
        }

        this.listenTo(vendorCart, 'change:order_time', this.render, this);
    },

    render: function () {
        console.log('TimePickerView.render ', this.cid);

        this.$el.html(this.template({
            days: _.zipObject(_.zip(_.keys(this.values), _.map(this.values, 'text'))),
            times: this.values[_.keys(this.values)[0]].times
        }));
        this.mobileDetect.mobile() ? this.$('select').selectpicker('mobile') : this.$('select').selectpicker();

        var date = this.model.getCart(this.vendor_id).get('order_time');

        if (date) {
            var dateObj = date === 'now' ? moment() : moment(date),
                dateKey = dateObj.format('YYYY-MM-DD'),
                timeKey = dateObj.format('HH:mm');

            if ($("#order-delivery-date option[value='" + dateKey + "']").length > 0) {
                this.$('#order-delivery-date').selectpicker('val', dateKey);
            }

            this.renderSelect(this.$('#order-delivery-time'), this.values[this.$('#order-delivery-date').val()].times);

            if ($("#order-delivery-time option[value='" + timeKey + "']").length > 0) {
                this.$('#order-delivery-time').selectpicker('val', timeKey);
            }
        } else {
            this.model.getCart(this.vendor_id).set('order_time', this._getDateFromForm());
        }

        return this;
    },

    renderSelect: function ($target, data) {
        $target.empty();
        _.forEach(data, function (element, key) {
            $target.append($('<option/>').val(key).text(element));
        }, this);

        $target.selectpicker('refresh');
    },

    unbind: function () {
        this.stopListening();
        this.undelegateEvents();
        this.$('select').selectpicker('destroy');
        this.$('.desktop-cart__time__field-time .bootstrap-select').tooltip('destroy');
    },

    updateDeliveryTime: function () {
        var vendorCart = this.model.getCart(this.vendor_id);
        var data = this.values[this.$('#order-delivery-date').val()].times;

        if (_.isUndefined(data[this.$('#order-delivery-time').val()])) {
            this.renderSelect(this.$('#order-delivery-time'), this.values[this.$('#order-delivery-date').val()].times);
        }

        vendorCart.set('order_time', this._getDateFromForm());
        vendorCart.updateCart();
    },

    _getDateFromForm: function() {
        var time = this.$('#order-delivery-time').val(),
            date = this.$('#order-delivery-date').val();

        return time === 'now' ? time : moment(date+time, "YYYY-MM-DDHH:mm").format();
    },

    showSelectionChangedTooltip: function () {
        this.$('.desktop-cart__time__field-time .bootstrap-select').tooltip({
            placement: 'top',
            trigger: 'manual',
            title: this.$('#order-delivery-time').data('tooltip-msg'),
            animation: false
        });

        this.$('.desktop-cart__time__field-time .bootstrap-select').tooltip('show');
    },

    refreshValues: function() {
        return $.ajax({
            url: Routing.generate('api_timepicker_get', {id: this.vendor_id}),
            dataType: 'json',
            success: _.curry(_.set)(this, 'values')
        });
    }
});
