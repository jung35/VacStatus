VacStatus
===========

Keep track of people's VAC ban status in a list

Info
------

VacStatus is currently using `` Laravel `` and is currently maintained by [Jung3o][jung]

1. Install dependencies using [Composer][composer] `composer install`
2. edit [.env.php][env] to your own settings
3. Run `php artisan migrate` to run migration
3. Install the requirements for [Foundation][foundation]
4. Run `compass watch` to compile style sheets
5. Finished!

To untrack `.env.php` you can do

    git update-index --assume-unchanged .env.php
If there is a better way to include and untrack, PLEASE send me a message or a pull request!

[jung]: https://github.com/jung3o
[composer]: http://daringfireball.net/projects/markdown/syntax#list
[env]: .env.php
[foundation]: http://foundation.zurb.com/docs/sass.html

Contributing
----

Please follow the [CONTRIBUTING.md][co]

[co]: CONTRIBUTING.md
