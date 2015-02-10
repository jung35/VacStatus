<?php namespace VacStatus\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Foundation\Application as App;
use Illuminate\Http\Response as Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
	    if($e instanceof NotFoundHttpException)
	    {
	        return (new Response((new File)->get('angular.html')));
	    }

	    return parent::render($request, $e);

		// if ($this->isHttpException($e))
		// {
		// 	return $this->renderHttpException($e);
		// }
		// else
		// {
		// 	return parent::render($request, $e);
		// }
	}


}
