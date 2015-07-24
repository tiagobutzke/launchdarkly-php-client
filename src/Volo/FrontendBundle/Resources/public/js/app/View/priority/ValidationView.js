var ValidationView = Backbone.View.extend({
    constraints: {},
    inputErrorClass: null, //error class for input
    _errorMessages: null,
    _defaultConstraints: {},

    events: {
        'blur input[name], select[name]': '_validateField',
        'keyup input[name], select[name]': '_hideMessage',
        'click button': '_validateForm'
    },

    initialize: function(options) {
        _.bindAll(this);

        this.constraints = _.assign(_.cloneDeep(this._defaultConstraints), options.constraints);
        this.inputErrorClass = options.errorClass || 'validation__error';
        this._errorMessages = {};
    },

    _validateField: function(e) {
        var target = e.target,
            $target = $(target),
            value = target.value || '',
            formValues = validate.collectFormValues(this.el),
            doValidate = validate.async(formValues, this.constraints);

        doValidate.then($.noop, function(invalidObj) {
            if (invalidObj && invalidObj[target.name] && value !== '') {
                this._displayMessage(target);
            }

            $target.toggleClass(this.inputErrorClass, !!invalidObj);
        }.bind(this));
    },

    _validateForm: function(e) {
        var formValues = validate.collectFormValues(this.el),
            errors = validate(formValues, this.constraints);

        if (errors) {
            e.preventDefault();

            _.each(this.$("input[name], select[name]"), function(input) {
                if (errors[input.name]) {
                    this._displayMessage(input);
                }
            }, this);
        }

        return true;
    },

    _displayMessage: function(target) {
        var view = this._errorMessages[target.name];
        if (view) {
            view.show();
        } else {
            var msg = $('<span class="form__error-message">'+target.getAttribute('data-validation-msg')+'</span>');
            this._errorMessages[target.name] = msg;

            msg.insertAfter(target);
        }
    },

    _hideMessage: function(e) {
        if (e.keyCode !== 13 && this._errorMessages[e.target.name]) {
            this._errorMessages[e.target.name].hide();
        }
    },

    unbind: function() {
        _.invoke(this._errorMessages, 'remove');

        this.undelegateEvents();
    }
});
