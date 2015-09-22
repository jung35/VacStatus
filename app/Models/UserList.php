<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use VacStatus\Steam\Steam;

use Carbon;

class UserList extends Model
{
	use SoftDeletes;
	
	protected $table = 'user_list';
	protected $dates = ['deleted_at'];
    protected $casts = [
    	// users.*
        'site_admin' => 'integer', 
        'donation' => 'integer',
        'beta' => 'integer',
        // total added
        'total' => 'integer',
    ];

	public function UserListProfile()
	{
		return $this->hasMany('VacStatus\Models\UserListProfile');
	}

	public function User()
	{
		return $this->belongsTo('VacStatus\Models\User', 'user_id', 'id');
	}
	
	public function canSubscribe($user_id)
	{
		return $this->user_id == $user_id || $this->privacy != 3;
	}

	public function scopeCheckExistingUser($query, $profileId)
	{
		return $query->join('user_list_profile', function($join) use ($profileId)
		{
			$join->on('user_list_profile.user_list_id', '=', 'user_list.id')
				->where('user_list_profile.profile_id', '=', $profileId)
				->whereNull('user_list_profile.deleted_at');
		});
	}

	public function scopeGetListProfiles($query, $listId)
	{
		return $query->where('user_list.id', $listId)
			->leftJoin('user_list_profile as ulp_1', function($join)
			{
				$join->on('ulp_1.user_list_id', '=', 'user_list.id')
					->whereNull('ulp_1.deleted_at');
			})
			->leftjoin('profile', 'ulp_1.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->leftJoin('user_list_profile as ulp_2', function($join)
			{
				$join->on('ulp_2.profile_id', '=', 'ulp_1.profile_id')
					->whereNull('ulp_2.deleted_at');
			})
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})
			->groupBy('profile.id')
			->orderBy('ulp_1.id', 'desc')
			->get([
		      	'ulp_1.profile_name',
		      	'ulp_1.profile_description',
		      	'ulp_1.created_at as added_at',

				'profile.id',
				'profile.display_name',
				'profile.avatar_thumb',
				'profile.small_id',

				'profile_ban.vac_bans',
				'profile_ban.game_bans',
				'profile_ban.last_ban_date',
				'profile_ban.community',
				'profile_ban.trade',

				'users.site_admin',
				'users.donation',
				'users.beta',

				\DB::raw('max(ulp_1.created_at) as last_added_at'),
				\DB::raw('count(distinct ulp_2.user_list_id) as total'),
				\DB::raw('count(distinct subscription.id) as sub_count'),
			]);
	}

	public function toArray()
	{
		$array = parent::toArray();
		$array['steam_64_bit'] = $this->steam_64_bit;

		return $array;
	}

	public function getSteam64BitAttribute()
	{
		return Steam::to64Bit($this->small_id);
	}
	
	public function getLastBanDateAttribute($lastBanDate)
	{
		return (new Carbon($lastBanDate))->format("M j Y");
	}
	
	public function getAddedAtAttribute($addedAt)
	{
		return (new Carbon($addedAt))->format("M j Y");
	}
}