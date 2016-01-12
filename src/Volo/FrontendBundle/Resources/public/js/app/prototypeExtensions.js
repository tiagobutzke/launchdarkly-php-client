(function () {
    HTMLElement.prototype._getBoundingClientRect = HTMLElement.prototype.getBoundingClientRect;
    HTMLElement.prototype.getBoundingClientRect = function () {
        var box = {
            top: this.offsetTop,
            left: this.offsetLeft
        };
        try {
            box = this._getBoundingClientRect();
        } catch (e) {}

        return box;
    };
})();
