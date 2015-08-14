<?php namespace VacStatus\Update;

use VacStatus\Models\UserMail;
use VacStatus\Models\Subscription;
use VacStatus\Models\UserListProfile;
use VacStatus\Models\ProfileBan;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use DateTime;
use DateInterval;

/*

	-> Grab all profiles from userlist
		-> loop through userlist
			-> add profile_id to an array under userlist_id key
				e.) user_list_id => [profile_id]
			-> add 'Profile' to big array and dont add duplicate profile_id
				e.) $profiles = [Profile] -> not ONLY profile_id
		->  Loop through

 */

class SubscriptionCheck
{
	private $userMail;
	private $userLists;
	private $subscriptionIds;
	private $profiles;
	private $error;

	private $sendEmail;
	private $sendPushbullet;
	private $sendProfiles;

	public function __construct($lastCheckedSubscription)
	{
		$userMail = UserMail::whereRaw('user_mail.id > ? and (user_mail.verify = ? or user_mail.pushbullet_verify = ?)', array($lastCheckedSubscription, 'verified', 'verified'))
			->first();

		if(!isset($userMail->user_id))
		{
			$lastCheckedSubscription = -1;

			$userMail = UserMail::whereRaw('user_mail.id > ? and (user_mail.verify = ? or user_mail.pushbullet_verify = ?)', array($lastCheckedSubscription, 'verified', 'verified'))
				->first();
		}

		$userLists = Subscription::where('subscription.user_id', $userMail->user_id)
			->whereNull('user_list.deleted_at')
			->whereNull('subscription.deleted_at')
			->leftJoin('user_list', 'subscription.user_list_id', '=', 'user_list.id')
			->distinct()
			->get([
				'user_list.id',

				'subscription.id as sub_id',
				'subscription.created_at',
				'subscription.updated_at'
			]);

		$subscriptionIds = $userLists->lists('sub_id');

		$profiles = UserListProfile::whereIn('user_list_profile.user_list_id', $userLists->lists('id'))
			->leftJoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftJoin('profile_ban', 'profile_ban.profile_id', '=', 'profile.id')
			->whereNull('user_list_profile.deleted_at')
			->distinct()
			->get([
				'user_list_profile.user_list_id',

				'profile.id',
				'profile.display_name',
				'profile.small_id',
				'profile.avatar_thumb',

				'profile_ban.vac_bans',
				'profile_ban.game_bans',
				'profile_ban.last_ban_date',
				'profile_ban.community',
				'profile_ban.trade',
				'profile_ban.created_at',
				'profile_ban.updated_at',
			]);

		$this->profiles = $profiles;
		$this->userMail = $userMail;
		$this->userLists = $userLists;
		$this->subscriptionIds = $subscriptionIds;
	}

	public function setSubscription()
	{
		return $this->userMail->id;
	}

	protected function error($reason)
	{
		return ['error' => $reason ];
	}

	public function errorMessage()
	{
		return $this->error;
	}

	public function run()
	{
		$send = $this->check();

		if(isset($send['error'])) 
		{
			$this->error = $send['error'];
			return false;
		}

		$toUpdate = Subscription::whereIn('id', $this->subscriptionIds)->get();
		foreach($toUpdate as $subscription) $subscription->touch();

		return true;
	}

	private function check()
	{
		$userMail = $this->userMail;
		$userLists = $this->userLists;	
		$profiles = $this->profiles;

		$profilesToSendForNotification = [];
		$getSmallIds = [];

		foreach($userLists as $userList)
		{
			$userListProfiles = $profiles->where('user_list_id', $userList->id);
			foreach($userListProfiles as $profile)
			{
				if($userList->updated_at->timestamp < $profile->updated_at->timestamp
				   && $profile->updated_at->timestamp <= time()
				   && $profile->updated_at->timestamp != $profile->created_at->timestamp
				   && $userList->updated_at->timestamp != $userList->created_at->timestamp)
				{ 
					$profilesToSendForNotification[$profile->id] = $profile;
				}

				if(!in_array($profile->small_id, $getSmallIds)) $getSmallIds[] = $profile->small_id;
			}
		}


		$steamAPI = new SteamAPI($getSmallIds, true);
		$steamBans = $steamAPI->fetch('ban');

		if(isset($steamBans['error'])) return $this->error($steamBans['error']);
		if(!isset($steamBans['players'][0])) return $this->error('profile_null');

		$steamBans = $steamBans['players'];

		$indexSave = [];
		foreach($steamBans as $k => $ban)
		{
			$indexSave[Steam::toSmallId($ban['SteamId'])] = $k;
		}

		foreach($getSmallIds as $k => $smallId)
		{
			if(!isset($indexSave[$smallId])) continue;
			
			$steamBan = $steamBans[$indexSave[$smallId]];
			$profile = $profiles->where('small_id', $smallId)->first();

			$apiLatestBanDate = new DateTime();
			$apiLatestBanDate->sub(new DateInterval("P{$steamBan['DaysSinceLastBan']}D"));

			$apiVacBans = (int) $steamBan['NumberOfVACBans'];
			$apiGameBans = (int) $steamBan['NumberOfGameBans'];

			$profileBan = [
				'vac_bans' => $apiVacBans,
				'game_bans' => $apiGameBans,
				'last_ban_date' => $apiLatestBanDate->format('Y-m-d'),
				'community' => $steamBan['CommunityBanned'],
				'trade' => $steamBan['EconomyBan'] != 'none',
			];

			if($profile->vac_bans != $profileBan['vac_bans']
			   || $profile->game_bans != $profileBan['game_bans']
			   || $profile->community != $profileBan['community']
			   || $profile->trade != $profileBan['trade'])
			{
				$oldProfileBan = ProfileBan::where('profile_id', $profile->id)->first();

				if($this->community != $steamBan['CommunityBanned']
				   || $this->trade != ($steamBan['EconomyBan'] != 'none'))
				{
					$oldProfileBan->timestamps = false;
				}

				if($profile->vac_bans > $profileBan['vac_bans']
				   || $profile->game_bans > $profileBan['game_bans'])
				{
					$oldProfileBan->timestamps = false;
				}

				$oldProfileBan->vac_bans = $profileBan['vac_bans'];
				$oldProfileBan->game_bans = $profileBan['game_bans'];
				$oldProfileBan->community = $profileBan['community'];
				$oldProfileBan->last_ban_date = $profileBan['last_ban_date'];
				$oldProfileBan->trade = $profileBan['trade'];
				$oldProfileBan->save();

				$profile->vac_bans = $profileBan['vac_bans'];
				$profile->game_bans = $profileBan['game_bans'];
				$profile->last_ban_date = $profileBan['last_ban_date'];
				$profile->community = $profileBan['community'];
				$profile->trade = $profileBan['trade'];

				$profilesToSendForNotification[$profile->id] = $profile;
			}
		}

		if(count($profilesToSendForNotification) == 0) return $this->error('no_notify_method');

		$this->sendEmail = $userMail->verify == "verified" ? $userMail->email : false;
		$this->sendPushbullet = $userMail->pushbullet_verify == "verified" ? $userMail->pushbullet : false;
		$this->sendProfiles = $profilesToSendForNotification;

		return true;
	}

	public function sendEmail($cb)
	{
		$email = $this->sendEmail;

		if(!$email) return;

		return $cb($email, $this->sendProfiles);
	}

	public function sendPushbullet($cb)
	{
		$email = $this->sendPushbullet;

		if(!$email) return;

		return $cb($email, $this->sendProfiles);
	}


}