#Volo Frontend [![build Status](https://magnum.travis-ci.com/foodpanda/volo-frontend.svg?token=9eHFdnBaxCRVqqTYivpW&branch=develop)](https://magnum.travis-ci.com/foodpanda/volo-frontend) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/quality-score.png?b=develop&s=fe3b7820a25ed19b25e9e9a98e300497928310f7)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/badges/coverage.png?b=develop&s=b5c39cd699602731616d7b2838bb994235c57317)](https://scrutinizer-ci.com/g/foodpanda/volo-frontend/?branch=develop)
---

Brief description
---
[VOLO](https://www.volo.de) uses [FoodPanda’s API](https://api-st.foodpanda.in/doc/v4/).

The VOLO website is built using vanilla Symfony2.

Installation
---

You'll need **PHP ~5.5.14**, **node.js**, **NPM**, **Bower**, **SASS ~3.4.13** & **Grunt** (cli) installed

**One time installation:**

```
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

Useful links
---
* [https://github.com/foodpanda/vagrant-dev](https://github.com/foodpanda/vagrant-dev) — setting up the vagrant machine
