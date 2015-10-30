<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use VacStatus\Steam\Steam;

use Carbon;

class Profile extends Model
{
	protected $table = 'profile';
	protected $fillable = ['small_id'];
	protected $dates = ['last_ban_date'];
    protected $casts = [
    	// users.*
        'site_admin' => 'integer', 
        'donation' => 'integer',
        'beta' => 'integer',

		// total added
        'total' => 'integer', 
    ];

	public function ProfileOldAlias()
	{
		return $this->hasMany(\VacStatus\Models\ProfileOldAlias::class);
	}

	public function ProfileBan()
	{
		return $this->hasOne(\VacStatus\Models\ProfileBan::class);
	}

	public function UserListProfile()
	{
		return $this->hasMany(\VacStatus\Models\UserListProfile::class);
	}

	public function scopeGetProfileData($query)
	{
		return $query->groupBy('profile.id')
			->leftJoin('user_list_profile', function($join)
			{
				$join->on('user_list_profile.profile_id', '=', 'profile.id')
					->whereNull('user_list_profile.deleted_at');
			})
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->get([
				'profile.id',
				'profile.display_name',
				'profile.avatar',
				'profile.avatar_thumb',
				'profile.small_id',
				'profile.profile_created',
				'profile.privacy',
				'profile.alias',
				'profile.created_at',

				'profile_ban.vac_bans',
				'profile_ban.game_bans',
				'profile_ban.last_ban_date',
				'profile_ban.community',
				'profile_ban.trade',

				'users.site_admin',
				'users.donation',
				'users.beta',

				\DB::raw('max(user_list_profile.created_at) as last_added_at'),
				\DB::raw('count(user_list_profile.id) as total')
			]);
	}

	public function toArray()
	{
		$array = parent::toArray();
		$array['steam_64_bit'] = $this->steam_64_bit;
		$array['steam_32_bit'] = $this->steam_32_bit;

		return $array;
	}

	public function getSteam64BitAttribute()
	{
		return Steam::to64Bit($this->small_id);
	}

	public function getSteam32BitAttribute()
	{
		return Steam::to32Bit($this->steam_64_bit);
	}

	public function getProfileCreatedAttribute($profileCreated = null)
	{
		return $profileCreated ? date("M j Y", $profileCreated) : "Unknown";
	}

	public function getAliasAttribute($alias)
	{
		return Steam::friendlyAlias(json_decode($alias, true));
	}

	public function getCreatedAtAttribute($createdAt)
	{
		return (new Carbon($createdAt))->format("M j Y");
	}
	
	public function getLastBanDateAttribute($lastBanDate)
	{
		return (new Carbon($lastBanDate))->format("M j Y");
	}

	public function getLastAddedAtAttribute($lastAddedAt)
	{
		return (new Carbon($lastAddedAt))->format("M j Y");
	}

	public function getTotalAttribute($total)
	{
		return is_numeric($total) ? $total : 0;
	}

	public function isPrivate()
	{
		return $this->privacy != 3;
	}

	public function getSteamCreation()
	{
		if(isset($this->profile_created)) return date('M j Y', $this->profile_created);
		return "Unknown";
	}

	public function getAlias()
	{
		return json_decode($this->alias);
	}
}