#Volo Frontend [![build Status](https://magnum.travis-ci.com/foodpanda/volo-frontend.svg?token=9eHFdnBaxCRVqqTYivpW&branch=develop)](https://magnum.travis-ci.com/foodpanda/volo-frontend) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/quality-score.png?b=develop&s=fe3b7820a25ed19b25e9e9a98e300497928310f7)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/coverage.png?b=develop&s=b5c39cd699602731616d7b2838bb994235c57317)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=develop)
---

Brief description
---
[VOLO](https://www.volo.de) uses [FoodPanda’s API](https://api-st.foodpanda.in/doc/v4/).

The VOLO website is built using vanilla Symfony2.

Installation
---

You'll need **PHP ~5.5.14**, **node.js**, **NPM**, **Bower**, **SASS ~3.4.13** & **Grunt** (cli) installed

**One time installation (Linux users: YMMV :-)):**

```
$ brew install php55 --with-fpm
$ brew install php55-xdebug
$ brew install nginx
$ # TODO: install node/npm instructions go here
$ npm install -g grunt-cli
$ npm install -g bower
$ gem install sass -v '~> 3.4.13'
```

**Setup the project (dependecies and assets):**

```
$ npm install
$ php composer.phar install
$ grunt
```

Setup
---

***nginx configuration***

```
server {
    server_name ~^(?<country_code>.+)\.volo\.dev$;
    root /path/to/volo/web;

    location / {
        # try to serve file directly, fallback to app.php
        try_files $uri /app_dev.php$is_args$args;
    }
    # DEV
    # This rule should only be placed on your development environment
    # In production, don't include this and don't deploy app_dev.php or config.php
    location ~ ^/(app_dev|config)\.php(/|$) {
        fastcgi_pass unix:/tmp/php5-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
        fastcgi_param  COUNTRY_CODE $country_code;
    }
    # PROD
    location ~ ^/app\.php(/|$) {
        fastcgi_pass unix:/tmp/php5-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
        fastcgi_param  COUNTRY_CODE $country_code;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/app.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    error_log /path/to/nginx/logs/volo_error.log;
    access_log /path/to/nginx/logs/volo_access.log;
}
```

Please add in your ```.bashrc``` or ```.zshrc```

```
export COUNTRY_CODE=de
```

Add a local alias:

```
echo "127.0.0.1 de.volo.dev" >> /etc/hosts
```

Go to http://de.volo.dev/ in your browser.

Parameters
---

The parameters are defined in ```app/config/countries_parameters```, for each country defined in ```composer.json```, ```composer install``` will create a file (e.g. ```de.yml```) in ```app/config/countries_parameters``` based on ```app/config/parameters.yml.dist```

Translation
---
The dictionary is saved at [https://webtranslateit.com/en/projects/11407-Volo](https://webtranslateit.com/en/projects/11407-Volo) (next: WTI). English language is used as a fallback language. Site's locale should be ```de```.

Translations are being imported to our project using the command ```app/console foodpanda:translations:sync```. The translation dictionary

Example of a non-plural translation

	<h1 class="text-center">{{ 'general.motto'|trans }}</h1>

Example of a plural translation

	<h2 class="text-center">{{ 'vendors.list.no_location'|transchoice(2) }}</h2>

A key of a translation is set in WTI's master file (which is English). You can edit the master file either in WTI UI, or using your favorite text editor and ```wti``` utility (github: [https://github.com/AtelierConvivialite/webtranslateit](https://github.com/AtelierConvivialite/webtranslateit)). In a project root execute ```wti pull```. The master file and translations (that has nothing to do with the actual symfony translations) will be pulled to ```app/Resources/translations```. There you can edit the master file ```en.yml``` and then ```wti push```. After that go to WTI UI, perform translations and ```app/console foodpanda:translations:sync```.

Useful links
---
* [https://github.com/foodpanda/vagrant-dev](https://github.com/foodpanda/vagrant-dev) — setting up the vagrant machine
