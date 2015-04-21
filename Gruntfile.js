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
        padding: 5,
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
        basePath: '/bundles/volofrontend/images'
    };
    sprite.common.src = frontendAssetPath('/images/sprite/*.png');
    sprite.common.srcFolder = frontendAssetPath('/images/sprite/');
    sprite.common.hash = md5Files(sprite.common.src);

    var config = {};

    config.sprite = {};

    config.sprite = {
        spritesheet: {
            src: sprite.common.src,
            dest: frontendAssetPath('/dist/sprite-' + sprite.common.hash + '.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-common.less'),
            imgPath: '/bundles/volofrontend/dist/sprite-' + sprite.common.hash + '.png'
        },
        retina: {
            src: sprite.common.src,
            dest: frontendAssetPath('/dist/sprite-' + sprite.common.hash + '.png'),
            retinaSrcFilter:  sprite.common.srcFolder + ['*-2x.png'],
            retinaDest: frontendAssetPath('/dist/sprite-' + sprite.common.hash + '-2x.png'),
            destCss: frontendAssetPath('/css/styles/common/sprite-common.less'),
            imgPath: '/bundles/volofrontend/dist/sprite-' + sprite.common.hash + '.png',
            retinaImgPath: '/bundles/volofrontend/dist/sprite-' + sprite.common.hash + '-2x.png'
        }
    };

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
                '<%= sprite.spritesheet.src %>',
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
    grunt.registerTask('default', ['bower:install', 'sprite', 'less', 'uglify', 'copy', 'jshint']);
    grunt.registerTask('deploy', ['bower:install', 'less', 'uglify']);

    //grunt.registerTask('default', ['sprite', 'less', 'uglify', 'copy', 'jshint']);
    //grunt.registerTask('deploy', ['sprite', 'less', 'uglify']);
};
