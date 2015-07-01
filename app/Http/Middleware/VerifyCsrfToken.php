<?php namespace VacStatus\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

use Auth;
use Route;
use VacStatus\Models\User;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$userKey = $request->input('_key');
		if($userKey && !empty($userKey))
		{
			$user = User::where('user_key', $userKey)->first();

			if($user->exists()) Auth::once($user);
			
			return $next($request);

			// if(Auth::check())
			// {
			// 	$prevuser = Auth::user();
			// 	Auth::logout();
			// }

			// if($user->exists())
			// {
			// 	Auth::login($user);

			// 	$response = $next($request);

			// 	Auth::logout();

			// 	if(isset($prevuser) && isset($prevuser->id)) Auth::login($prevuser);

			// 	return $response;
			// } else {
			// 	$response = $next($request);
			// }
			
			// if(isset($prevuser)) Auth::login($prevuser);

			// return $response;
		}

		if($request->is('api/v1/donate/ipn')) return $next($request);

		return parent::handle($request, $next);
	}

}
