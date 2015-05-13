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
        'web/js/lib/andr3pt-blazy.js',
        'web/bundles/fosjsrouting/js/router.js',
        'web/js/fos_js_routes.js',
        'bower_components/lodash/lodash.js',
        'bower_components/backbone/backbone.js',
        'bower_components/geocomplete/jquery.geocomplete.js',
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
            sourcemap: (env === 'dev') ? 'auto' : 'none'
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
            beautify: (env === 'dev'),
            sourceMapName: frontendWebPath('/js/dist/head.js.map')
        },
        head: {
            src: jsSources.head,
            dest: frontendWebPath('/js/dist/head.js'),
            sourceMapRoot: frontendWebPath('/js/dist/head.js.map')
        }
    };

    config.jshint = {
        options: {
            force: true,
            ignores: [
                'bower_components/jquery/dist/jquery.js',
                'web/bundles/heltheturbolinks/js/jquery.turbolinks.js',
                'web/bundles/heltheturbolinks/js/turbolinks.js',
                'web/js/lib/andr3pt-blazy.js',
                'bower_components/lodash/lodash.js',
                'bower_components/backbone/backbone.js',
                'web/bundles/fosjsrouting/js/router.js'
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


    config.jasmine = {
        src: [
            "src/Volo/FrontendBundle/Resources/public/js/**/*.js",
            "!src/Volo/FrontendBundle/Resources/public/js/main.js"
        ],
        options: {
            specs: "spec/**/*Spec.js",
            vendor: [
                'build/bower/lodash/lodash.js',
                'build/bower/jquery/jquery.js',
                'build/bower/backbone/backbone.js',
                'web/bundles/fosjsrouting/js/router.js',
                'web/js/fos_js_routes.js',
                'build/bower/blazy/blazy.js',
                'vendor/helthe/turbolinks/Resources/public/js/jquery.turbolinks.js',
                'vendor/helthe/turbolinks/Resources/public/js/turbolinks.js'
            ]
        }
    };

    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-spritesmith');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-jasmine');

    // grunt additional tasks
    grunt.registerTask('default', ['bower:install', 'sprite', 'sass', 'uglify', 'jshint']);
    grunt.registerTask('deploy', ['bower:install', 'sprite', 'sass', 'uglify']);
    grunt.registerTask('test', ['jshint', 'jasmine']);
};
