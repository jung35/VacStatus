<?php

namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;

use VacStatus\Models\Profile;
use VacStatus\Models\ProfileBan;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use DateTime;
use DateInterval;

class profileBanConvert extends Command
{
    protected $signature = 'profile:ban:convert';
    protected $description = 'Update profile bans to seperate vac bans and game bans.';

    public function handle()
    {
        $profileNeedsUpdate = [];

        Profile::leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
            ->where('profile_ban.vac_bans', '>', '0')
            ->chunk(100, function($profiles)
        {
            $smallIDs = $profiles->lists('small_id')->toArray();

            $steamAPI = new SteamAPI($smallIDs, true);
            $steamBans = $steamAPI->fetch('ban');

            if(isset($steamBans['error'])) return $this->error($steamBans['error']);
            if(!isset($steamBans['players'][0])) return $this->error('profile_null');

            $steamBans = $steamBans['players'];

            $indexSave = [];
            foreach($steamBans as $k => $ban)
            {
                $indexSave[Steam::toSmallId($ban['SteamId'])] = $k;
            }

            foreach($smallIDs as $k => $smallId)
            {
                if(!isset($indexSave[$smallId])) {
                    $profileNeedsUpdate[] = $smallId;
                }
                
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

                $oldProfileBan = ProfileBan::where('profile_id', $profile->id)->first();

                if($oldProfileBan == null) 
                {
                    $this->error("unknown: $smallId");
                    continue;
                }

                $oldProfileBan->timestamps = false;
                $oldProfileBan->vac_bans = $profileBan['vac_bans'];
                $oldProfileBan->game_bans = $profileBan['game_bans'];
                $oldProfileBan->community = $profileBan['community'];
                $oldProfileBan->last_ban_date = $profileBan['last_ban_date'];
                $oldProfileBan->trade = $profileBan['trade'];
                $oldProfileBan->save();

                $this->info("updated: $smallId");
            }
        });

        dump('still needs update');
        dump($profileNeedsUpdate);
    }
}
