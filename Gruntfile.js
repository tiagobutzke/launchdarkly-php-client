/* global require */
var md5File = require('md5-file');
var md5 = require('MD5');
var _ = require('lodash');

/*global module:false*/
module.exports = function (grunt) {

    function frontendAssetPath(path) {
        var assetsPath = 'src/Volo/FrontendBundle/Resources/public';
        return getPath(path, assetsPath);
    }

    function frontendWebPath(path) {
        var assetsPath = 'web';
        return getPath(path, assetsPath);
    }

    function getPath(path, assetsPath) {
        if (Array.isArray(path)) {
            for (var i = 0; i < path.length; i++) {
                path[i] = assetsPath + path[i];
            }
        } else {
            path = assetsPath + path;
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
        'bower_components/blazy/blazy.js',
        frontendAssetPath('/js/main.js'),
    ];

    var sprite = {
        common: {},
        basePath: '/web/img/dist/'
    };
    sprite.common.src = frontendAssetPath('/img/sprite/*.png');
    sprite.common.srcFolder = frontendAssetPath('/img/sprite/');
    sprite.common.hash = md5Files(sprite.common.src);

    var config = {};

    config.sprite = {};

    config.sprite = {
        spritesheet: {
            src: sprite.common.src,
            dest: frontendWebPath('/img/dist/sprite-' + sprite.common.hash + '.png'),
            destCss: frontendAssetPath('/dist/less/sprite-common.less'),
            imgPath: '/img/dist/sprite-' + sprite.common.hash + '.png',
            algorithm: 'top-down'
        },
        retina: {
            src: sprite.common.src,
            dest: frontendWebPath('/img/dist/sprite-' + sprite.common.hash + '.png'),
            retinaSrcFilter:  sprite.common.srcFolder + ['*-2x.png'],
            retinaDest: frontendWebPath('/img/dist/sprite-' + sprite.common.hash + '-2x.png'),
            destCss: frontendAssetPath('/dist/less/sprite-common.less'),
            imgPath: '/img/dist/sprite-' + sprite.common.hash + '.png',
            retinaImgPath: '/img/dist/sprite-' + sprite.common.hash + '-2x.png',
            algorithm: 'top-down'
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
                dest: frontendWebPath('/css/dist/style.css')
            }]
        },
        siteBundleStyleRTL: {
            files: [{
                src: frontendAssetPath('/css/main-rtl.less'),
                dest: frontendWebPath('/css/dist/style-rtl.css')
            }]
        }
    };

    config.uglify = {
        options: {
            sourceMap: (env === 'dev'),
            sourceMapIncludeSources: (env === 'dev'),
            compress: (env !== 'dev'),
            beautify: (env === 'dev'),
            sourceMapName: 'js/dist/head.js.map'
        },
        head: {
            src: jsSources.head,
            dest: frontendWebPath('/js/dist/head.js'),
            sourceMapRoot: 'js/dist/head.js.map'
        }
    };

    config.jshint = {
        options: {
            force: true,
            ignores: [
                'bower_components/jquery/dist/jquery.js',
                'web/bundles/heltheturbolinks/js/jquery.turbolinks.js',
                'web/bundles/heltheturbolinks/js/turbolinks.js',
                'bower_components/blazy/blazy.js',
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
            tasks: ['uglify', 'jshint']
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

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-spritesmith');
    grunt.loadNpmTasks('grunt-bower-task');

    // grunt additional tasks
    grunt.registerTask('default', ['bower:install', 'sprite', 'less', 'uglify', 'jshint']);
    grunt.registerTask('deploy', ['bower:install', 'less', 'uglify']);

    //grunt.registerTask('default', ['sprite', 'less', 'uglify', 'copy', 'jshint']);
    //grunt.registerTask('deploy', ['sprite', 'less', 'uglify']);
};
