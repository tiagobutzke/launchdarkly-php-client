var ValidationView = Backbone.View.extend({
    constraints: {}, //default constraints
    inputErrorClass: null, //error class for input

    initialize: function(options) {
        _.bindAll(this, '_validateField', '_displayMessage', '_validateForm', '_hideMessage');

        this.constraints = _.assign(this.constraints, options.constraints);
        this.inputErrorClass = options.errorClass || 'validation__error';
    },

    attach: function() {
        this.$('input[name], select[name]').on('blur', this._validateField);
        this.$('input[name], select[name]').on('keyup', this._hideMessage);

        this.$('button').on('click', this._validateForm);
    },

    _validateField: function(e) {
        var target = e.target,
            $target = $(target),
            value = target.value || '',
            obj = {},
            invalidObj;

        obj[target.name] = target.value;
        invalidObj = validate(obj, this.constraints);


        if (invalidObj && value !== '') {
            this._displayMessage($target);
        }

        $target.toggleClass(this.inputErrorClass, !!invalidObj);
    },

    _validateForm: function(e) {
        var formValues = validate.collectFormValues(this.el),
            errors = validate(formValues, this.constraints);

        if (errors) {
            e.preventDefault();

            _.each(this.$("input[name], select[name]"), function(input) {
                this._displayMessage($(input));
            }, this);
        }

        return true;
    },

    _displayMessage: function($target) {
        $target
            .tooltip({
                trigger: 'manual',
                placement: 'auto left'
            })
            .addClass(this.inputErrorClass)
            .tooltip('show');
    },

    _hideMessage: function(e) {
        if (e.keyCode !== 13) { //do not hide errors on submit
            $(e.target).tooltip('hide');
        }
    }
});
