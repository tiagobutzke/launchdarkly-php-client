/* global require */
var md5File = require('md5-file');
var md5 = require('MD5');
var _ = require('lodash');

/*global module:false*/
module.exports = function (grunt) {

    function frontendAssetPath(path) {
        var bundlePublicPath = 'src/Volo/FrontendBundle/Resources/public';

        if (Array.isArray(path)) {
            for (var i = 0; i < path.length; i++) {
                path[i] = bundlePublicPath + path[i];
            }
        } else {
            path = bundlePublicPath + path;
        }
        return path;
    }

    function md5Files(paths) {
        var i;
        var hash = [];
        var srcFiles = grunt.file.expand(paths);

        for (i in srcFiles) {
            hash.push(md5File(srcFiles[i]));
        }

        return md5(hash.join(''));
    }

    var env = grunt.option('env') || 'prod';

    var jsSources = {};

    jsSources.head = jsSources.headIE = [
        'bower_components/labjs/LAB.src.js',
        frontendAssetPath('/js/tracking.js'),
        frontendAssetPath('/js/FdNamespace.js'),
        frontendAssetPath('/js/onloadFix.js'),
        frontendAssetPath('/js/minimalJsSupportDetector.js'),
        frontendAssetPath('/js/removeNoJs.js')
    ];

    jsSources.head = [
        frontendAssetPath('/js/jsErrorLog.js')
    ].concat(jsSources.head);

    jsSources.headIE = [
        frontendAssetPath('/js/jsErrorLog.js'),
        'bower_components/html5shiv/dist/html5shiv.js',
        'bower_components/respond/dest/respond.src.js'
    ].concat(jsSources.headIE);

    jsSources.shop = [
        frontendAssetPath('/js/spinPresets.js'),
        frontendAssetPath('/js/trait/*.js'),
        frontendAssetPath('/js/Base.js'),
        frontendAssetPath('/js/controller/*.js'),
        frontendAssetPath('/js/InitController.js'),
    ];

    jsSources.main = jsSources.mainIE = [
        'bower_components/joii/src/joii.js',
        'bower_components/Subtopic/subtopic.js',
        'bower_components/Subtopic/jquery-subtopic.js',
        'bower_components/bootstrap/js/button.js',
        'bower_components/bootstrap/js/dropdown.js',
        'bower_components/bootstrap/js/modal.js',
        'bower_components/bootstrap/js/tooltip.js',
        'bower_components/bootstrap/js/popover.js',
        'bower_components/bootstrap/js/scrollspy.js',
        'bower_components/bootstrap/js/tab.js',
        'bower_components/bootstrap/js/transition.js',
        'bower_components/typeahead.js/dist/typeahead.jquery.js',
        'bower_components/typeahead.js/dist/bloodhound.js',
        'bower_components/sticky/jquery.sticky.js',
        'bower_components/spin.js/spin.js',
        'bower_components/spin.js/jquery.spin.js',
        'bower_components/jquery.smartbanner/jquery.smartbanner.js',
        'bower_components/jquery.scrollTo/jquery.scrollTo.js',
        'bower_components/lazyloadxt/dist/jquery.lazyloadxt.js',
        'bower_components/jquery.payment/lib/jquery.payment.js',
        'bower_components/jquery.cookie/jquery.cookie.js',
        'bower_components/jquery-maskedinput/src/jquery.maskedinput.js',
        'bower_components/df-visible/jquery.visible.js',
        //'bower_components/jquery-star-rating/src/rating.js'
    ].concat(jsSources.shop);

    jsSources.main = [
        'bower_components/jquery-modern/dist/jquery.js',
        frontendAssetPath('/js/jquerypp.js'),
        'bower_components/jquery-requestAnimationFrame/dist/jquery.requestAnimationFrame.js'
    ].concat(jsSources.main);

    jsSources.mainIE = [
        'bower_components/jquery/dist/jquery.js'
    ].concat(jsSources.mainIE);

    // quality is a misnomer, it actually refers to the level of compression,
    // where 0 is uncompressed and 100 is compressed to the highest degree
    // imgOpts: {
    //    quality: 100
    // }
    var spriteCommonConfig = {
        padding: 50,
        engine: 'gmsmith',
        cssFormat: 'less',
        algorithm: 'top-down',
        cssVarMap: function (sprite) {
            sprite.name = 'sprite-' + sprite.name;
        },
        imgOpts: {
            quality: 100
        }
    };

    var sprite = {
        common: {},
        homepage: {},
        checkout: {},
        customer: {},
        basePath: '/bundles/foodpandashopsite/images'
    };
    sprite.common.src = frontendAssetPath('/images/sprite/common/*.png');
    sprite.common.hash = md5Files(sprite.common.src);
    sprite.homepage.src = frontendAssetPath('/images/sprite/home/*.png');
    sprite.homepage.hash = md5Files(sprite.homepage.src);
    sprite.checkout.src = frontendAssetPath('/images/sprite/checkout/*.png');
    sprite.checkout.hash = md5Files(sprite.checkout.src);
    sprite.customer.src = frontendAssetPath('/images/sprite/customer/*.png');
    sprite.customer.hash = md5Files(sprite.customer.src);

    var config = {};

    config.sprite = {};

    config.sprite.common = _.extend(
        {},
        spriteCommonConfig,
        {
            src: sprite.common.src,
            dest: frontendAssetPath('/dist/sprite-' + sprite.common.hash + '.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-common.less'),
            imgPath: '/bundles/foodpandashopsite/dist/sprite-' + sprite.common.hash + '.png',
        }
    );

    config.sprite.homepage = _.extend(
        {},
        spriteCommonConfig,
        {
            src: sprite.homepage.src,
            dest: frontendAssetPath('/dist/sprite-' + sprite.homepage.hash + '.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-home.less'),
            imgPath: '/bundles/foodpandashopsite/dist/sprite-' + sprite.homepage.hash + '.png',
        }
    );

    config.sprite.checkout = _.extend(
        {},
        spriteCommonConfig,
        {
            src: sprite.checkout.src,
            dest: frontendAssetPath('/dist/sprite-' + sprite.checkout.hash + '.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-checkout.less'),
            imgPath: '/bundles/foodpandashopsite/dist/sprite-' + sprite.checkout.hash + '.png',
        }
    );

    config.sprite.customer = _.extend(
        {},
        spriteCommonConfig,
        {
            src: sprite.customer.src,
            dest: frontendAssetPath('/dist/sprite_' + sprite.customer.hash + '.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-customer.less'),
            imgPath: '/bundles/foodpandashopsite/dist/sprite_' + sprite.customer.hash + '.png'
        }
    );

    config.less = {
        options: {
            sourceMap: (env === 'dev'),
            outputSourceFiles: (env === 'dev'),
            sourceMapFileInline: (env === 'dev'),
            ieCompat: true,
            strictImports: true,
            modifyVars: {
                "image-base-path": "'" + sprite.basePath + "'"
            },
            plugins: [
                new (require('less-plugin-autoprefix'))({
                    browsers: [
                        '> 1%',
                        'last 3 versions',
                        'Firefox ESR',
                        'Opera >= 12.1',
                        'Android >= 2.3',
                        'BlackBerry >= 10',
                        'iOS >= 4',
                        'last 4 ChromeAndroid version',
                        'last 4 FirefoxAndroid versions',
                        'last 4 ExplorerMobile versions'
                    ],
                    cascade: false
                }),
                new (require('less-plugin-clean-css'))({
                    compatibility: 'ie7'
                })
            ]
        },
        siteBundleStyle: {
            files: [{
                src: frontendAssetPath('/css/main.less'),
                dest: frontendAssetPath('/dist/style.css')
            }]
        },
        siteBundleStyleRTL: {
            files: [{
                src: frontendAssetPath('/css/main-rtl.less'),
                dest: frontendAssetPath('/dist/style-rtl.css')
            }]
        }
    };

    config.uglify = {
        options: {
            sourceMap: (env === 'dev'),
            sourceMapIncludeSources: (env === 'dev')
        },
        head: {
            src: jsSources.head,
            dest: frontendAssetPath('/dist/head.js')
        },
        headIE: {
            options: {
                compress: false,
                mangle: false
            },
            src: jsSources.headIE,
            dest: frontendAssetPath('/dist/head.ie.js')
        },
        main: {
            src: jsSources.main,
            dest: frontendAssetPath('/dist/main.js')
        },
        mainIE: {
            src: jsSources.mainIE,
            dest: frontendAssetPath('/dist/main.ie.js')
        }
    };

    config.jshint = {
        options: {
            jshintrc: true,
            force: true
        },
        gruntfile: {
            src: 'Gruntfile.js'
        },
        shop: {
            src: jsSources.shop
        }
    };

    config.watch = {
        options: {
            livereload: true
        },
        sprite: {
            files: [
                '<%= sprite.common.src %>',
                '<%= sprite.homepage.src %>',
                '<%= sprite.checkout.src %>',
                '<%= sprite.customer.src %>'
            ],
            tasks: ['sprite']
        },
        css: {
            files: frontendAssetPath('/css/**/*.{less,css}'),
            tasks: ['less']
        },
        js: {
            files: [
                '<%= uglify.head.src %>',
                '<%= uglify.headIE.src %>',
                '<%= uglify.main.src %>',
                '<%= uglify.mainIE.src %>'
            ],
            tasks: ['uglify', 'copy', 'jshint']
        }
    };

    config.copy = {
        main: {
            files: [{
                expand: true,
                flatten: true,
                cwd: './',
                src: frontendAssetPath('/dist/*.map'),
                dest: 'web/js/',
                filter: 'isFile'
            }]
        }
    };

    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-spritesmith');

    // grunt additional tasks
    grunt.registerTask('default', ['less']);
    grunt.registerTask('deploy', ['less', 'uglify']);

    //grunt.registerTask('default', ['sprite', 'less', 'uglify', 'copy', 'jshint']);
    //grunt.registerTask('deploy', ['sprite', 'less', 'uglify']);
};
