<?php

namespace VacStatus\Http\Middleware;

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
		if($request->has('_key'))
		{
			$user = User::where('user_key', $request->input('_key'))->first();

			if(Auth::check())
			{
				$prevuser = Auth::user();
				Auth::logout();
			}

			if(isset($user->id))
			{
				Auth::login($user);

				$response = $next($request);

				Auth::logout();

				if(isset($prevuser) && isset($prevuser->id)) Auth::login($prevuser);

				return $response;
			} else {
				$response = $next($request);
			}
			
			if(isset($prevuser)) Auth::login($prevuser);
			
			return $response;
		}

		if($request->is('api/v1/donate/ipn')) return $next($request);

		return parent::handle($request, $next);
	}

}
