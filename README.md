##VacStatus

Keep track of people's VAC ban status in a list

##Info

VacStatus is currently using `` Laravel `` and is currently maintained by [Jung3o][jung]. I have been using Homestead for development and have not tested development on laravel's built in server.

####Installation
1. Create a copy of `.environment.php.sample` and rename it `.environment.php`.
2. Using the value inside the `.environment.php` file, create a copy of `.env.php.sample` and rename it to the `.env.(return value from environment).php`. Edit the values from `env` file accordingly.

    (Make sure you dont delete the original files `.environment.php.sample` and `.env.php.sample`)
3. Install dependencies using [Composer][composer] `composer install`
4. Run `php artisan migrate` to run migration
5. Install the requirements for [Foundation][foundation] (**DO NOT RUN `compass watch` yet!!!**)
6. Run `bower install` to install any Foundation related files
7. Run `compass watch` to compile style sheets

I also have redis currently selected as driver for [cache.php][cache] and [session.php][session]

####Cron Job settings
```
*/1 * * * * php artisan vacStatus
```

[jung]: https://github.com/jung3o
[composer]: http://daringfireball.net/projects/markdown/syntax#list
[foundation]: http://foundation.zurb.com/docs/sass.html

[cache]: app/config/cache.php#L18
[session]: app/config/session.php#L19

Contributing
----

Please follow the [CONTRIBUTING.md][co]

[co]: CONTRIBUTING.md
