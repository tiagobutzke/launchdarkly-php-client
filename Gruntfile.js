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

    var env = grunt.option('env') || 'dev',
        allCountriesOption = ('dev' === env && !grunt.option('sass-countries')) ? 'de' : grunt.option('sass-countries'),
        debug = (env === 'dev'),
        allCountriesSass = grunt.file.readJSON('app/config/countries.json'),
        jsSources = {};

    if (allCountriesOption) {
        allCountriesSass = allCountriesOption.split(',');
    }

    jsSources.libs = [
        'web/bower_components/jquery/dist/jquery.js',
        'web/bower_components/real-shadow/realshadow.js',
        'web/js/lib/andr3pt-blazy.js'
    ];

    jsSources.comingSoon = [
        'web/bower_components/jquery/dist/jquery.js',
        'web/js/lib/andr3pt-blazy.js',
        frontendAssetPath('/js/app/Animation/*.js')
    ];

    jsSources.allLibs = jsSources.libs.concat([
        'web/bundles/heltheturbolinks/js/turbolinks.js',
        'web/bundles/fosjsrouting/js/router.js',
        'web/thumbor/configuration.js',
        'web/bower_components/lodash/lodash.js',
        'web/bower_components/backbone/backbone.js',
        'web/bower_components/backbone.localStorage/backbone.localStorage.js',
        'web/bower_components/geocomplete/jquery.geocomplete.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/modal.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/tooltip.js',
        'web/bower_components/twbs-bootstrap-sass/assets/javascripts/bootstrap/dropdown.js',
        'web/bower_components/adyen-cse-js/js/adyen.encrypt.js',
        'web/bower_components/spin.js/spin.js',
        'web/bower_components/validate/validate.js',
        'web/bower_components/mobile-detect/mobile-detect.js',
        'web/bower_components/jquery.payment/lib/jquery.payment.js',
        'web/bower_components/moment/moment.js',
        'web/bower_components/bootstrap-select/dist/js/bootstrap-select.js',
        'web/bower_components/moment-timezone/builds/moment-timezone-with-data-2010-2020.js',
        'web/bower_components/js-cookie/src/js.cookie.js',
        'web/bower_components/promise-polyfill/Promise.js',
        'web/bower_components/jquery.serializeJSON/jquery.serializejson.js',
        'web/bower_components/devicejs/lib/device.min.js'
    ]);

    jsSources.head = jsSources.allLibs.concat([
        // if a priorityN (priority0, priority1 etc...) subfolder is found anywhere
        // than the script inside it are loaded before all the other scripts in the containing folder
        // Example: js/app/Views/priority0/utility.js will be loaded before js/app/Views/myView.js
        // you can have multiple priority folders like this:
        // js/app/Views/priority0
        // js/app/Models/priority0
        // js/app/Models/priority1 (scripts inside this are loaded after priority0)
        frontendAssetPath('/js/app/**/priority*/**/*.js'),

        // after all priorityN folders are loaded (if any) the scripts in mixin folder are loaded
        frontendAssetPath('/js/app/mixin/**/*.js'),

        // than all the other (normal priority) scripts are loaded
        frontendAssetPath('/js/**/*.js')
    ]);

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
        }
    };
    _.each(allCountriesSass, function(country) {
        var countryLabel = 'country_' +  country;

        config.sass[countryLabel] = {
            files: [{
                src: frontendAssetPath('/css/countries/' + country + '.scss'),
                dest: frontendWebPath('/css/dist/style-' + country + '.css')
            }]
        };
    });


    config.uglify = {
        options: {
            sourceMap: debug,
            compress: debug ? false : {
                drop_console: true
            },
            beautify: debug,
            mangle: !debug
        },
        libs: {
            src: jsSources.libs,
            dest: frontendWebPath('/js/dist/libs.js')
        },
        head: {
            src: jsSources.head,
            dest: frontendWebPath('/js/dist/head.js')
        },
        intl: {
            src: 'web/bower_components/intl/Intl.js',
            dest: frontendWebPath('/js/dist/intl.js')
        },
        commingSoon: {
            src: jsSources.comingSoon,
            dest: frontendWebPath('/js/dist/coming-soon.js')
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
            ignores: jsSources.allLibs,
            expr: true,
            debug: debug
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
            "src/Volo/FrontendBundle/Resources/public/js/**/priority*/**/*.js",
            "src/Volo/FrontendBundle/Resources/public/js/**/*.js",
            "!src/Volo/FrontendBundle/Resources/public/js/bootstrap.js"
        ],
        options: {
            specs: "spec/**/*Spec.js",
            vendor: jsSources.allLibs
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
