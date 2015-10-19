<?php

namespace VacStatus\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

use GuzzleHttp\Stream\Stream;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;

class SteamProvider extends AbstractProvider implements ProviderInterface
{

	protected $openIdURL = 'https://steamcommunity.com/openid/login';
	protected $stateless = true;

	protected function getAuthUrl($state)
	{

		$params = [
			'openid.ns'         => 'http://specs.openid.net/auth/2.0',
			'openid.ns.reg'     => 'http://openid.net/extensions/sreg/1.1',
			'openid.mode'       => 'checkid_setup',
			'openid.return_to'  => url('').'/auth/check',
			'openid.realm'      => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
			'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
			'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
			'state'             => $state,
		];

		$prepareURL = $this->openIdURL . '?' . http_build_query($params);

		return $prepareURL;
	}

	protected function getTokenUrl()
	{
		return $this->openIdURL;
	}

	public function getAccessToken($code)
	{
		$response = $this->getHttpClient()->post($this->openIdURL, [
			'body' => $code,
		]);

		if(preg_match('/is_valid\s*:\s*true/i', $response->getBody()) !== 1) return false;

		return str_replace('http://steamcommunity.com/openid/id/', '', $code['openid.claimed_id']);
	}

	protected function getUserByToken($token)
	{
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/";

		$response = $this->getHttpClient()->get($url . '?' . http_build_query([ 'key' => env('STEAM_API'), 'steamids' => $token ]), [
			'headers' => [ 'Accept' => 'application/json' ]
		]);

		$user = json_decode($response->getBody(), true);

		if(!isset($user['response']['players']) || count($user['response']['players']) !== 1)
		{
			return ['steamid' => null, 'personaname' => null];
		}

		return $user['response']['players'][0];
	}

	protected function mapUserToObject(array $user)
	{
		return (new User)->setRaw($user)->map([
			'id'		=> $user['steamid'],
			'nickname'	=> $user['personaname'],
			'name'		=> isset($user['realname']) ? $user['realname'] : null,
			'email'		=> null,
		]);
	}

	protected function getCode()
	{
		$postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

		$params = [
			'openid.assoc_handle' => $this->request->input('openid_assoc_handle'),
			'openid.signed'       => $this->request->input('openid_signed'),
			'openid.sig'          => $this->request->input('openid_sig'),
			'openid.ns'           => 'http://specs.openid.net/auth/2.0',
		];

		$signed = explode(',', $this->request->input('openid_signed'));

		foreach ($signed as $item)
		{
			$cleanedItem = str_replace('.', '_', $item);
			$val = $this->request->input("openid_{$cleanedItem}");

			$params["openid.{$item}"] = get_magic_quotes_gpc() ? stripslashes($val) : $val; 
		}

		$params['openid.mode'] = 'check_authentication';

		return $params;
	}
}
