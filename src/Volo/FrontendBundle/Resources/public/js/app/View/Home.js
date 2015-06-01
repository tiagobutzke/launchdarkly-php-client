$(document).ready(function() {
    var $stats = $(".stats__dish"),
        $statsCommentCache;

    new FullWindowHeight();
    if ($stats.length) {
        new RevealOnScroll($stats, $('.stats'), 0.3, 0.07, 85);
        $statsCommentCache = $(".stats__comment");
        new NumberScroller(
            $(".numbers__scroller"),
            function() { return $statsCommentCache.offset().top; },
            29
        );
        $('.city').each(function() {
            var $city = $(this),
                $cityNameHover = $city.find('.city__called-action');

            $cityNameHover.hide();
            $city.mouseover(function() {
                    $cityNameHover.show();
                })
                .mouseout(function() {
                    $cityNameHover.hide();
                });
            new Flipper($city);
        });
    }
});
