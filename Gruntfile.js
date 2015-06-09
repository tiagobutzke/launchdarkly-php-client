/* global require */
var md5File = require('md5-file');
var md5 = require('MD5');
var _ = require('lodash');

/*global module:false*/
module.exports = function (grunt) {

    function frontendAssetPath(path) {
        var assetsPath = 'web/bundles/volofrontend';
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
    var debug = (env === 'dev');

    var jsSources = {};

    jsSources.head = [
        'web/bower_components/jquery/dist/jquery.js',
        'web/bundles/heltheturbolinks/js/turbolinks.js',
        'web/js/lib/andr3pt-blazy.js',
        'web/bundles/fosjsrouting/js/router.js',
        'web/js/fos_js_routes.js',
        'web/thumbor/configuration.js',
        'web/bower_components/lodash/lodash.js',
        'web/bower_components/backbone/backbone-min.js',
        'web/bower_components/geocomplete/jquery.geocomplete.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/modal.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/tooltip.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/dropdown.js',
        'web/bower_components/adyen-cse-js/js/adyen.encrypt.js',
        'web/bower_components/spin.js/spin.js',
        'web/bower_components/validate/validate.js',
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
            loadPath: process.cwd(),
            style: 'compressed',
            sourcemap: (env === 'dev') ? 'file' : 'none'
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
            sourceMap: debug,
            compress: debug ? false : {
                drop_console: true
            },
            beautify: debug,
            mangle: !debug
        },
        head: {
            src: jsSources.head,
            dest: frontendWebPath('/js/dist/head.js')
        },
        intl: {
            src: 'web/bower_components/intl/Intl.js',
            dest: frontendWebPath('/js/dist/intl.js')
        }
    };

    config.copy = {
        main: {
            src: 'web/bower_components/intl/locale-data/json/*',
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
                'web/bower_components/jquery/dist/jquery.js',
                'web/bundles/heltheturbolinks/js/turbolinks.js',
                'web/js/lib/andr3pt-blazy.js',
                'web/bower_components/lodash/lodash.js',
                'web/bower_components/backbone/backbone-min.js',
                'web/bundles/fosjsrouting/js/router.js',
                'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/modal.js',
                'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/tooltip.js',
                'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/dropdown.js',
                'web/bower_components/adyen-cse-js/js/adyen.encrypt.js',
                'web/bower_components/intl/Intl.js',
                'web/bower_components/spin.js/spin.js',
                'web/bower_components/validate/validate.js'
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
            tasks: ['uglify', 'jshint']
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
                'web/bower_components/jquery/dist/jquery.js',
                'web/bower_components/lodash/lodash.js',
                'web/bower_components/backbone/backbone.js',
                'web/bundles/fosjsrouting/js/router.js',
                'web/js/fos_js_routes.js',
                'web/bower_components/blazy/blazy.js',
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
