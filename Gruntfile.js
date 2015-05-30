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
        'web/bundles/heltheturbolinks/js/turbolinks.js',
        'web/js/lib/andr3pt-blazy.js',
        'web/bundles/fosjsrouting/js/router.js',
        'web/js/fos_js_routes.js',
        'web/thumbor/configuration.js',
        'bower_components/lodash/lodash.js',
        'bower_components/backbone/backbone.js',
        'bower_components/geocomplete/jquery.geocomplete.js',
        'bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/modal.js',
        'bower_components/adyen-cse-js/js/adyen.encrypt.js',
        frontendAssetPath('/js/**/*.js')
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
            destCss: frontendAssetPath('/dist/sass/sprite-common.scss'),
            imgPath: '/img/dist/sprite-' + sprite.common.hash + '.png',
            algorithm: 'top-down'
        },
        retina: {
            src: sprite.common.src,
            dest: frontendWebPath('/img/dist/sprite-' + sprite.common.hash + '.png'),
            retinaSrcFilter:  sprite.common.srcFolder + ['*-2x.png'],
            retinaDest: frontendWebPath('/img/dist/sprite-' + sprite.common.hash + '-2x.png'),
            destCss: frontendAssetPath('/dist/sass/sprite-common.scss'),
            imgPath: '/img/dist/sprite-' + sprite.common.hash + '.png',
            retinaImgPath: '/img/dist/sprite-' + sprite.common.hash + '-2x.png',
            algorithm: 'top-down'
        }
    };

    config.sass = {
        options: {
            style: 'compressed',
            sourcemap: (env === 'dev') ? 'inline' : 'none'
        },
        siteBundleStyle: {
            files: [{
                src: frontendAssetPath('/css/main.scss'),
                dest: frontendWebPath('/css/dist/style.css')
            }]
        }
    };

    config.uglify = {
        options: {
            sourceMap: (env === 'dev'),
            sourceMapIncludeSources: (env === 'dev'),
            compress: (env !== 'dev'),
            beautify: false,
            mangle: (env !== 'dev')
        },
        head: {
            src: jsSources.head,
            dest: frontendWebPath('/js/dist/head.js'),
            sourceMapRoot: frontendWebPath('/js/dist/head.js.map'),
            sourceMapName: frontendWebPath('/js/dist/head.js.map')
        },
        intl: {
            src: 'bower_components/intl/Intl.js',
            dest: frontendWebPath('/js/dist/intl.js'),
            sourceMapRoot: frontendWebPath('/js/dist/intl.js.map'),
            sourceMapName: frontendWebPath('/js/dist/intl.js.map')
        }
    };

    config.copy = {
        main: {
            src: 'bower_components/intl/locale-data/json/*',
            dest: frontendWebPath('/js/dist/intl/locale/'),
            expand: true,
            flatten: true,
            filter: 'isFile'
        }
    };

    config.jshint = {
        options: {
            force: true,
            ignores: [
                'bower_components/jquery/dist/jquery.js',
                'web/bundles/heltheturbolinks/js/turbolinks.js',
                'web/js/lib/andr3pt-blazy.js',
                'bower_components/lodash/lodash.js',
                'bower_components/backbone/backbone.js',
                'web/bundles/fosjsrouting/js/router.js',
                'bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/modal.js',
                'bower_components/adyen-cse-js/js/adyen.encrypt.js',
                'bower_components/intl/Intl.js'
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
                '<%= sprite.spritesheet.src %>'
            ],
            tasks: ['sprite']
        },
        css: {
            files: frontendAssetPath('/css/**/*.{scss,css}'),
            tasks: ['sass']
        },
        js: {
            files: [
                'Gruntfile.js',
                '<%= uglify.head.src %>'
            ],
            tasks: ['copy', 'uglify', 'jshint']
        }
    };

    config.bower = {
        install: {
            options: {
                targetDir: './build/bower',
                cleanTargetDir: true,
                copy: false
            }
        }
    };


    config.jasmine = {
        src: [
            "src/Volo/FrontendBundle/Resources/public/js/**/*.js",
            "!src/Volo/FrontendBundle/Resources/public/js/bootstrap.js"
        ],
        options: {
            specs: "spec/**/*Spec.js",
            vendor: [
                'bower_components/jquery/dist/jquery.js',
                'bower_components/lodash/lodash.js',
                'bower_components/backbone/backbone.js',
                'web/bundles/fosjsrouting/js/router.js',
                'web/js/fos_js_routes.js',
                'bower_components/blazy/blazy.js',
                'vendor/helthe/turbolinks/Resources/public/js/turbolinks.js'
            ]
        }
    };

    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-spritesmith');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-jasmine');

    // grunt additional tasks
    grunt.registerTask('default', ['bower:install', 'sprite', 'sass', 'copy', 'uglify', 'jshint']);
    grunt.registerTask('deploy', ['bower:install', 'sprite', 'sass', 'copy', 'uglify']);
    grunt.registerTask('test', ['jshint', 'jasmine']);
};
