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

    var env = grunt.option('env') || 'dev';

    var jsSources = {};

    jsSources.head = [
        'bower_components/jquery/dist/jquery.js',
        'web/bundles/heltheturbolinks/js/jquery.turbolinks.js',
        'web/bundles/heltheturbolinks/js/turbolinks.js',
        frontendAssetPath('/js/main.js')
    ];


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
            sourceMapIncludeSources: (env === 'dev'),
            compress: (env !== 'dev'),
            beautify: (env === 'dev')
        },
        head: {
            src: jsSources.head,
            dest: frontendAssetPath('/js/dist/head.js')
        }
    };

    config.jshint = {
        options: {
            force: true,
            ignores: [
                'bower_components/jquery/dist/jquery.js',
                'web/bundles/heltheturbolinks/js/jquery.turbolinks.js',
                'web/bundles/heltheturbolinks/js/turbolinks.js'
            ]
        },
        gruntfile: {
            src: 'Gruntfile.js'
        },
        head: {
            src: jsSources.head
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
                '<%= uglify.head.src %>'
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

    config.bower = {
        install: {
            options: {
                targetDir: './build/bower',
                cleanTargetDir: true
            }
        }
    };

    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-spritesmith');
    grunt.loadNpmTasks('grunt-bower-task');

    // grunt additional tasks
    grunt.registerTask('default', ['bower:install', 'less', 'uglify', 'copy', 'jshint']);
    grunt.registerTask('deploy', ['bower:install', 'less', 'uglify']);

    //grunt.registerTask('default', ['sprite', 'less', 'uglify', 'copy', 'jshint']);
    //grunt.registerTask('deploy', ['sprite', 'less', 'uglify']);
};
