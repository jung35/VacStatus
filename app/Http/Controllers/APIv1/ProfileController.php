<?php

namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\SingleProfile;
use VacStatus\Steam\Steam;

class ProfileController extends Controller
{

	public function index($steam64BitId)
	{
		$smallId = Steam::toSmallId($steam64BitId);
		if(is_array($smallId))
		{
			return ['error' => 'invalid_small_id'];
		}
		$singleProfile = new SingleProfile($smallId);

		return $singleProfile->getProfile();
	}

}
