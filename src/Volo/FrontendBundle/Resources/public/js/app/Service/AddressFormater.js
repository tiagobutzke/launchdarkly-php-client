VOLO = VOLO || {};
VOLO.Service = VOLO.Service || {};

VOLO.Service.AddressFormater = function(appConfig) {
    _.bindAll(this);
    this.appConfig = appConfig;
};
_.extend(VOLO.Service.AddressFormater.prototype, {
    getFormattedStreet: function(address) {
        var street = address.split(',')[0].trim(),
            addressComponents = this.appConfig.address_config.format.split(' '),
            streetComponent = _.find(addressComponents, function(component) {
                return component.indexOf(':street') !== -1;
            });

        street = streetComponent.replace(':street', street);

        /*
         * if building number is at the beginning, we place space on the beginning (user will put number there)
         * otherwise it's at the end
         */
        if (this.getBuildingNumberIndex() === 0) {
            return ' ' + street;
        } else {
            return street + ' ';
        }
    },

    getFormattedStreetAndBuilding: function(address) {
        var result = this.appConfig.address_config.format,
            cityReplacement = '';

        if (this._getPlaceholderIndex(':city') < this._getPlaceholderIndex(':plz')) {
            cityReplacement = address.city;
        }

        result = result.replace(':street', address.street)
                    .replace(':building', address.building)
                    .replace(':city', cityReplacement)
                    .replace(':plz', '')
                    .replace(/\s{2,}/g, ' ')
                    .trim();

        return result + ' ';
    },

    getBuildingNumberIndex: function() {
        return this.appConfig.address_config.format.indexOf(':building');
    },

    _getPlaceholderIndex: function(placeHolder) {
        return this.appConfig.address_config.format.indexOf(placeHolder);
    }
});
