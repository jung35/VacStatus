VacStatus
===========

Keep track of people's VAC ban status in a list

Info
------

VacStatus is currently using `` Laravel `` and is currently maintained by [Jung3o][jung]

1. Install dependencies using [Composer][composer] `composer install`
2. edit [.env.php][env] to your own settings
3. Run `php artisan migrate` to run migration
3. Install the requirements for [Foundation][foundation] (DO NOT RUN `compass watch` yet!!!)
4. Run `bower install` to install any Foundation related files
5. Run `compass watch` to compile style sheets

Rename `.env.php.sample` to `.env.local.php` and configure

Cron Job settings

`` */1 * * * * php artisan vacStatus ``

[jung]: https://github.com/jung3o
[composer]: http://daringfireball.net/projects/markdown/syntax#list
[env]: .env.php
[foundation]: http://foundation.zurb.com/docs/sass.html

Contributing
----

Please follow the [CONTRIBUTING.md][co]

[co]: CONTRIBUTING.md
