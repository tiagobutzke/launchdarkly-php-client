#Volo Frontend

[![Build Status](https://magnum.travis-ci.com/foodpanda/volo-frontend.svg?token=9eHFdnBaxCRVqqTYivpW&branch=master)](https://magnum.travis-ci.com/foodpanda/volo-frontend)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/quality-score.png?b=master&s=fe3b7820a25ed19b25e9e9a98e300497928310f7)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/coverage.png?b=master&s=b5c39cd699602731616d7b2838bb994235c57317)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/df9c2144-acd0-4df1-90e1-86d4408fe520/mini.png)](https://insight.sensiolabs.com/projects/df9c2144-acd0-4df1-90e1-86d4408fe520)

Brief description
---
[Foodora](https://www.foodora.de) uses [FoodPanda’s API](http://docs.foodpandapublicmobileapi.apiary.io/).

The VOLO website is built using vanilla Symfony2.

To get access to SensioLabsInsight, create https://insight.sensiolabs.com/ account and ask @mathias-work to be added

Installation
===

You'll need **PHP ~5.5.14**, **node.js**, **NPM**, **Bower**, **SASS ~3.4.13** & **Grunt** (cli) installed

One time installation
---

## OSX

**Make sure to use a case-sensitve partition**

Setup the `homebrew/versions` tap which has dependencies we need:

```shell
$ brew tap homebrew/dupes
$ brew tap homebrew/versions
$ brew tap homebrew/homebrew-php
```

```shell
$ brew install php55 --with-fpm
$ brew install php55-curl
$ brew install php55-intl
$ brew install php55-redis
$ brew install php55-xdebug
$ brew install redis
$ brew install nginx
$ brew install nodejs
$ gem install sass -v '~> 3.4.13'
$ npm install -g grunt-cli
$ npm install -g bower
$ brew install imagemagick
```

## Debian / Ubuntu

```shell
$ sudo apt-get install nginx graphicsmagick php5 php5-xdebug php5-intl php5-curl php55-redis redis-server nodejs
```

One time setup
---

## PHP

Add custom configuration for PHP

```INI
date.timezone = GMT

xdebug.remote_enable=1
xdebug.remote_port=9001
xdebug.max_nesting_level = 300
xdebug.cli_color=1
xdebug.file_link_format = "phpstorm://open?file=%f&line=%l"

memory_limit = 1024M
max_execution_time = 300
```
*OSX* `/usr/local/etc/php/5.5/conf.d/zzz_custom.ini`

*Debian/Ubuntu* `/etc/php5/fpm/conf.d/zzz_custom.ini` 

```shell
$ gem install sass --user-install -v '~> 3.4.13'
$ npm install -g grunt-cli
$ npm install -g bower
$ brew install imagemagick
$ curl -sS https://getcomposer.org/installer | php
```
## Nginx

```nginx
server {
    listen       80;

    server_name ~^(?<country_code>.+)\.volo\.dev$;
    root /path/to/volo/web;

    gzip             on;
    gzip_types       text/plain application/json application/javascript application/x-javascript text/javascript text/xml text/css;
    gzip_comp_level  5;

    location / {
        # try to serve file directly, fallback to app.php
        try_files $uri /app_dev.php$is_args$args;
    }
    # DEV
    # This rule should only be placed on your development environment
    # In production, don't include this and don't deploy app_dev.php or config.php
    location ~ ^/(app_dev|config)\.php(/|$) {
        fastcgi_pass 127.0.0.1; # or unix:/tmp/php5-fpm.sock
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_param COUNTRY_CODE $country_code;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS $https;
    }

    error_log /usr/local/var/log/nginx/volo_error.log;
    access_log /usr/local/var/log/nginx/volo_access.log;
}
```
Adjust `root`, `error_log` and `access_log` according to your system

## Environment setup

Please add in your ```.bashrc``` or ```.zshrc```

```
export COUNTRY_CODE=de
```

run
```shell
$ source ~/.bashrc # or ~/.zshrc
```

Add to `/etc/hosts`

```shell
# Volo
127.0.0.1 de.volo.dev
127.0.0.1 ae.volo.dev
127.0.0.1 at.volo.dev
127.0.0.1 au.volo.dev
127.0.0.1 ca.volo.dev
127.0.0.1 ch.volo.dev
127.0.0.1 es.volo.dev
127.0.0.1 dk.volo.dev
127.0.0.1 fi.volo.dev
127.0.0.1 fr.volo.dev
127.0.0.1 ie.volo.dev
127.0.0.1 it.volo.dev
127.0.0.1 nl.volo.dev
127.0.0.1 se.volo.dev
127.0.0.1 gb.volo.dev
127.0.0.1 no.volo.dev
127.0.0.1 hk.volo.dev
127.0.0.1 sg.volo.dev
```

## Install dependecies and assets

```shell
$ npm install
$ php composer.phar install --no-interaction
$ grunt --sass-countries=all && grunt watch
```

Go to http://de.volo.dev/ in your browser.

PHPStorm tricks
---

- Install symfony2 plugin
- *Mark directoy as -> Exclude*
  - app/cache
  - app/logs
  - web/bundles

Deploy
---

```shell
$ gem install --user-install capistrano
$ gem install --user-install capistrano-symfony
$ gem install --user-install capistrano-composer
$ gem install --user-install capistrano-file-permissions
```

To deploy:
```shell
$ branch=<branch_name> cap <stage> deploy
```
where stage could be qa1, qa2, etc and `branch` is the branch name

Parameters
---

The parameters are defined in ```app/config/countries_parameters```,
for each country defined in ```composer.json```, ```composer install``` will create a file (e.g. ```de.yml```)
in ```app/config/countries_parameters``` based on ```app/config/countries_parameters/dist/<country_code>.yml.dist```

Translation
---
The dictionary is saved at [https://webtranslateit.com/en/projects/11407-Volo](https://webtranslateit.com/en/projects/11407-Volo) (next: WTI).
English language is used as a fallback language. Site's locale should be ```de```.

Translations are being imported to our project using the command

```shell
$ app/console foodora:translations:sync
```

The translation dictionary

Example of a non-plural translation

	<h1 class="text-center">{{ 'general.motto'|trans }}</h1>

Example of a plural translation

	<h2 class="text-center">{{ 'vendors.list.no_location'|transchoice(2) }}</h2>

A key of a translation is set in WTI's master file (which is English).
You can edit the master in [WTI web site](https://webtranslateit.com/).
The translations will be downloaded to ```Volo/FrontendBundle/Resources/translations```.

Useful links
---
* [https://github.com/foodpanda/vagrant-dev](https://github.com/foodpanda/vagrant-dev) — setting up the vagrant machine
