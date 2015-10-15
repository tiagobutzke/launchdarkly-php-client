_.extend(Backbone.LocalStorage.prototype, {
    localStorage: function() {
        try {
            if (!window.fakeLocalStorage) {
                // localStorage.setItem throws an exception during private browsing
                // this is just a test to see if localStorage.setItem works
                localStorage.setItem('test', 'test');
            }

            return localStorage;
        } catch (Error) {
            if (!window.fakeLocalStorage) {
                window.fakeLocalStorage = {
                    _data: {},
                    setItem: function (id, val) {
                        this._data[id] = String(val);
                    },
                    getItem: function (id) {
                        return this._data.hasOwnProperty(id) ? this._data[id] : undefined;
                    },
                    removeItem: function (id) {
                        return delete this._data[id];
                    },
                    clear: function () {
                        this._data = {};
                    }
                };
            }

            return window.fakeLocalStorage;
        }
    }
});
