<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use VacStatus\Steam\Steam;

use Carbon;

class UserListProfile extends Model
{
	use SoftDeletes;
	
	protected $table = 'user_list_profile';
    protected $fillable = ['profile_id', 'profile_name', 'profile_description'];
    protected $dates = ['deleted_at', 'last_ban_date'];
    protected $casts = [
    	// users.*
        'site_admin' => 'integer', 
        'donation' => 'integer',
        'beta' => 'integer',
        // total added
        'total' => 'integer',
    ];

	public function UserList()
	{
		return $this->belongsTo(\VacStatus\Models\UserList::class, 'user_list_id', 'id');
	}

	public function Profile()
	{
		return $this->belongsTo(\VacStatus\Models\Profile::class, 'id');
	}

	public function scopeGetProfiles($query, $amount = 40)
	{
		return $query->whereNull('user_list_profile.deleted_at')
			->leftjoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->whereNotNull('profile.id')
			->groupBy('profile.id')
			->take($amount)
			->get([
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

				\DB::raw('max(user_list_profile.created_at) as last_added_at'),
				\DB::raw('count(distinct user_list_profile.user_list_id) as total'),
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

	public function getLastAddedAtAttribute($lastAddedAt)
	{
		return (new Carbon($lastAddedAt))->format("M j Y");
	}
	
	public function getLastBanDateAttribute($lastBanDate)
	{
		return (new Carbon($lastBanDate))->format("M j Y");
	}

}
