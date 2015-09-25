var ValidationView = Backbone.View.extend({
    constraints: {},
    inputErrorClass: null, //error class for input
    _errorMessages: null,
    _defaultConstraints: {},

    events: function() {
        return {
            'blur input[name], select[name]': '_validateField',
            'keyup input[name], select[name]': '_hideMessage',
            'click button': '_validateForm'
        };
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
            formValues = validate.collectFormValues(this.el),
            doValidate = validate.async(formValues, this.constraints);

        doValidate.then(function() {
            this.trigger('form:valid');
        }.bind(this), function(invalidObj) {
            if (invalidObj && invalidObj[target.name]) {
                this._displayMessage(target);
                this.trigger('form:error');
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
            this.trigger('form:error');
        } else {
            this.trigger('form:valid');
            this.cleanErrorMessages();
        }

        return true;
    },

    _displayMessage: function(target) {
        var view = this._errorMessages[target.name];
        if (view) {
            view.removeClass('hide');
        } else {
            this._errorMessages[target.name] = this.createErrorMessage(target.getAttribute('data-validation-msg'), target);
        }
    },

    createErrorMessage: function(errorMessage, target) {
        var msg = $('<span class="form__error-message"></span>').text(errorMessage);
        msg.insertAfter(target);

        return msg;
    },

    _hideMessage: function(e) {
        if (e.keyCode !== 13 && this._errorMessages[e.target.name]) {
            this._errorMessages[e.target.name].addClass('hide');
        }
    },

    cleanErrorMessages: function() {
        _.invoke(this._errorMessages, 'remove');
        this._errorMessages = {};
    },

    unbind: function() {
        this.cleanErrorMessages();
        this.undelegateEvents();
    }
});
