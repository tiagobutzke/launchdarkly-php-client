var VOLO = VOLO || {};
VOLO.FullWindowHeight = {
    $windowCached: null,
    onResize: function() {
        console.log('FullWindowHeight.onResize');
        var $targets = $('.fullWindowHeight');
        if ($targets.length) {
            $targets.height(this.$windowCached.height());
        }
    },
   init: function() {
       this.$windowCached = $(window);
       this.$windowCached.resize(this.onResize.bind(this));
   }
};
