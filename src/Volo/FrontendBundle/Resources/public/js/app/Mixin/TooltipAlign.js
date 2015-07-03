/**
 * VOLO.TooltipAlignMixin
 * Mixin which provides align methods for Backbone tooltips
 */
var VOLO = VOLO || {};
VOLO.TooltipAlignMixin = {
    tooltipAlignLeftClass: 'tooltip--align-left',
    tooltipAlignRightClass: 'tooltip--align-right',

    isTooltipEnabled: function(target) {
        return target instanceof jQuery && !jQuery.isEmptyObject(target) && _.isFunction(target.tooltip);
    },

    tooltipAlignLeft: function (target) {
        if (this.isTooltipEnabled(target)) {
            target.tooltip().data('bs.tooltip').tip()
                .removeClass(this.tooltipAlignRightClass)
                .addClass(this.tooltipAlignLeftClass);
        }
    },

    tooltipAlignRight: function (target) {
        if (this.isTooltipEnabled(target)) {
            target.tooltip().data('bs.tooltip').tip()
                .removeClass(this.tooltipAlignLeftClass)
                .addClass(this.tooltipAlignRightClass);
        }
    },

    tooltipAlignRemove: function (target) {
        if (this.isTooltipEnabled(target)) {
            target.tooltip().data('bs.tooltip').tip()
                .removeClass(this.tooltipAlignRightClass)
                .removeClass(this.tooltipAlignLeftClass);
        }
    }
};
