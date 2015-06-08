var ValidationView = Backbone.View.extend({
    _defaultConstraints: {},
    constraints: {},
    inputErrorClass: null, //error class for input

    events: {
        'blur input[name]': '_validateField',
        'blur select[name]': '_validateField',

        'keyup input[name]': '_hideMessage',
        'keyup select[name]': '_hideMessage',

        'click button': '_validateForm'
    },

    initialize: function(options) {
        _.bindAll(this, '_validateField', '_displayMessage', '_validateForm', '_hideMessage');

        this.constraints = _.assign(_.cloneDeep(this._defaultConstraints), options.constraints);
        this.inputErrorClass = options.errorClass || 'validation__error';
        this._errorMessages = {};
    },

    _validateField: function(e) {
        var target = e.target,
            $target = $(target),
            value = target.value || '',
            obj = {},
            invalidObj;

        obj[target.name] = target.value;
        invalidObj = validate(obj, this.constraints);

        if (invalidObj && invalidObj[target.name] && value !== '') {
            this._displayMessage(target);
        }

        $target.toggleClass(this.inputErrorClass, !!invalidObj);
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

    _errorMessages: null,
    _displayMessage: function(target) {
        var view = this._errorMessages[target.name];
        if (view) {
            view.$el.show();
        } else {
            view = new ValidationMessageView({
                model: new Backbone.Model({
                    message: target.title
                })
            });

            this._errorMessages[target.name] = view;
            view.render().$el.insertAfter(target);
        }
    },

    _hideMessage: function(e) {
        if (e.keyCode !== 13 && this._errorMessages[e.target.name]) {
            this._errorMessages[e.target.name].$el.hide();
        }
    },

    remove: function() {
        _.invoke(this._errorMessages, 'remove');

        Backbone.View.prototype.remove.apply(this, arguments);
    }
});
