<?php namespace VacStatus\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

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
	/*	
		$userKey = $request->input('_key');
		if($userKey && !empty($userKey))
		{
			$user = User::whereKey($userKey)->first();
			if(isset($user->id))
			{

			}
		}
*/
		return parent::handle($request, $next);
	}

}
