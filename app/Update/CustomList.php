<?php

namespace VacStatus\Update;

use Carbon;
use DateTime;
use Auth;

use VacStatus\Models\User;
use VacStatus\Models\UserList;
use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class CustomList
{
	private $userList;
	private $error = null;

	function __construct($userList)
	{
		if(!isset($userList->id))
		{
			$this->error = "404";
			return;
		}

		if(Auth::check()) 
		{
			$user = Auth::User();
			$userFriends = json_decode($user->friendslist);


			if($user->id != $userList->user_id)
			{
				$listAuthor = User::whereId($userList->user_id)->first();
				if(($listAuthor->exists() && !empty($userFriends) && !in_array($listAuthor->small_id, $userFriends) && $userList->privacy == 2) || $userList->privacy == 3)
				{
					$this->error = "forbidden";
					return;
				}
			}

		} else if($userList->privacy == 2 || $userList->privacy == 3)
		{
			$this->error = "forbidden";
			return;
		}

		$this->userList = $userList;
	}

	public function error()
	{
		return is_null($this->error) ? false : $this->error;
	}

	public function myList()
	{
		return (Auth::check() && $this->userList->user_id == Auth::user()->id);
	}

	public function getCustomList()
	{
		if($this->error()) return $this->error();
		$userList = $this->userList;

		$userListProfiles = UserList::getListProfiles($userList->id);

		$canSub = false;
		$subscription = null;

		if(Auth::check())
		{
			$user = Auth::user();
			$userMail = $user->UserMail;
			$subscription = $user->Subscription
				->where('user_list_id', $userList->id)
				->first();
			if($userMail &&
			   ($userMail->verify == "verified" || $userMail->pushbullet_verify == "verified")) $canSub = true;
		}

		$multiProfile = new MultiProfile($userListProfiles, $userList->id);

		$return = [
			'list_info' => [
				'id'			=> $userList->id,
				'title'			=> $userList->title,
				'author'		=> $userList->user->display_name,
				'my_list'		=> $this->myList(),
				'can_sub'		=> $canSub,
				'subscription'	=> $subscription,
				'privacy'		=> $userList->privacy,
				'sub_count'		=> isset($userListProfiles[0]) ? $userListProfiles[0]->sub_count : 0,
			],
			'profiles'			=> $multiProfile->run(),
		];

		return $return;
	}
}