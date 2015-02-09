# Angular - Laravel 4 Workflow

This is a customization of my generic [workflow](https://github.com/Foxandxss/fox-angular-gulp-workflow) to be used inside a Laravel4 application.

For a complete tutorial, follow my [blog post](http://angular-tips.com/blog/2014/10/working-with-a-laravel-4-plus-angular-application/)

## Creating the project

Here is the basic installation:

```
$ laravel new foo
$ cd foo
$ git clone https://github.com/Foxandxss/angular-laravel4-workflow angular
```

We create a Laravel 4 application and inside it, we clone this workflow. By default this workflow is configured to sit the root of our Laravel application and the name of it doesn't matter, I chose `angular` so we can easily differentiate the Laravel application with the Angular side, you can pick any name.

If you want to chose another inner directory like `app/assets` or `app/angular` you can easily adapt the workflow changing:

```javascript
var publicFolderPath = '../public';
```

to:

```javascript
var publicFolderPath = '../../public';
```

## Working with our Angular

All we need to do is simply:

```
$ cd angular
$ gulp
```

That will start all the machinery for us, we just need to develop our application like we used to with my workflow.

## Tell Laravel about our Angular application

So, what's the difference between this workflow and my standard one? Your angular application is going to work on your `/public` directory, which is the directory that Laravel uses to serve stuff.

What we need to do is to create a route to serve our angular, we can do something like:

```php
Route::get('{angular?}', function() {
	return File::get(public_path().'/index.html');
})->where('angular', '.*');
```

If we put that as the last route in our `routes.php`, when we put a route that doesn't match with our `API`, it will serve the Angular application which is living under `/public`. The good thing about that route is that it will work whether we have `html5mode` or not.

You can certainly use a `controller` to serve that `index.html` file, that is up to you.

The **TL;DR** here is that you only need to put a route to serve the angular living at `/public/index.html`, nothing else.

## Sending our application to production

How you deploy your Laravel is up to you, but for the Angular side we just need to:

```
$ gulp clean && gulp production
```

That will create the same structure at `/public` but using production ready files.
