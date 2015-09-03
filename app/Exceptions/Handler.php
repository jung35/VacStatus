<?php namespace VacStatus\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Auth;

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
		if ($e instanceof \Illuminate\Session\TokenMismatchException) return;

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
		if ($e instanceof \Illuminate\Session\TokenMismatchException) {
			\Log::info('Illuminate\Session\TokenMismatchException', [
           		'url' => $request->url(),
           		'inputs' => $request->all(),
           		'auth' => Auth::check() ? Auth::user()->id : null
           	]);
		}

		if($e instanceof \GuzzleHttp\Exception\TransferException) {
			\Log::info('GuzzleHttp\Exception\TransferException', [
				'request' => $e->getRequest(),
				'response' => $e->hasResponse() ? $e->getResponse() : null
			]);
		}

		return parent::render($request, $e);
	}

}
