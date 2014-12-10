<?php

use Illuminate\Console\Command;
use Steam\Steam as Steam;

class vacStatus extends Command {
      protected $log;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vacStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks ban.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    $this->log = Log::getMonolog();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $userMail = UserMail::checkUserList();
        $subscription = $userMail->Subscription;

        $sendEmail = false;
        $emailArr = array();

        foreach($subscription as $sub_key => $sub) {
            $userList = $sub->UserList;

            $emailArr[$sub_key] = array(
                              'id'       => $userList->id,
                              'user_id'  => $userList->user_id,
                              'title'    => $userList->title,
                              'profiles' => array()
                              );

            $userListId = $userList->id;
            // $this->info(dd($userList));

            $profiles = UserListProfile::whereUserListId($userListId)
                            ->join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
                            ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
                            ->orderBy('user_list_profile.id','desc')
                            ->get([
                                'user_list_profile.id',
                                'user_list_profile.profile_id',
                                'user_list_profile.user_list_id',

                                'profile.small_id',
                                'profile.display_name',

                                'profile_ban.community',
                                'profile_ban.vac',
                                'profile_ban.vac_days',
                                'profile_ban.trade',
                                'profile_ban.unban',
                                'profile_ban.created_at',
                                'profile_ban.updated_at',
                            ]);
            $steamIds = array();
            $sortedProfiles = array();
            foreach($profiles as $profile) {
                if(($profile->community || $profile->vac || $profile->trade) &&
                   strtotime($profile->updated_at) != strtotime($profile->created_at) &&
                   strtotime($profile->updated_at) > strtotime($userMail->updated_at)) {
                    $sendEmail = true;
                    $emailArr[$sub_key]['profiles'][$profile->small_id] = array(
                      'display_name' => $profile->display_name,
                      'vac_days'     => !$profile->vac ?: date('M j Y', strtotime($profile->updated_at) - $profile->vac_days * 86400),
                      'community'    => $profile->community,
                      'trade'        => $profile->trade
                      );
                }

                $sortedProfiles[$profile->small_id] = (object) array(
                    'profile_id'   => $profile->profile_id,
                    'small_id'     => $profile->small_id,
                    'display_name' => $profile->display_name,
                    'community'    => $profile->community,
                    'vac'          => $profile->vac,
                    'vac_days'     => $profile->vac_days,
                    'trade'        => $profile->trade,
                    'updated_at'   => $profile->updated_at
                    );

                $steamIds[] = Steam::toBigId($profile->small_id);
                // $this->info(var_dump($profile));
            }

            if(count($steamIds) == 0) continue;

            /*
            Grab information about ban
            */

            // Grab user detailed ban info
            $steamAPI_Bans = Steam::cURLSteamAPI('ban', $steamIds);
            if(isset($steamAPI_Bans->type) && $steamAPI_Bans->type == 'error') {
                continue;
            }

            $steamAPI_Bans = $steamAPI_Bans->players;

            foreach($steamAPI_Bans as $steamAPI_Ban) {
                $profile = $sortedProfiles[Steam::toSmallId($steamAPI_Ban->SteamId)];

                $banChange = false;

                if((($steamAPI_Ban->NumberOfVACBans != $profile->vac) ||
                   ($steamAPI_Ban->CommunityBanned != $profile->community) ||
                   ($steamAPI_Ban->EconomyBan != $profile->trade)))
                {
                    $sendEmail = true;
                    $banChange = true;
                }

                if($banChange)
                {
                    $emailArr[$sub_key]['profiles'][$profile->small_id] = array(
                      'display_name' => $profile->display_name,
                      'vac_days'     => $profile->vac ? date('M j Y', strtotime($profile->updated_at) - $profile->vac_days * 86400): false,
                      'community'    => $profile->community,
                      'trade'        => $profile->trade
                      );

                    $updateProfile = ProfileBan::find($profile->profile_id)->first();

                    $updateProfile->vac = $steamAPI_Ban->NumberOfVACBans;
                    $updateProfile->community = $steamAPI_Ban->CommunityBanned;
                    $updateProfile->trade = $steamAPI_Ban->EconomyBan != 'none';
                    $updateProfile->vac_days = $steamAPI_Ban->DaysSinceLastBan;

                    $updateProfile->save();
                }

            }

            // Profile::updateMulitipleProfile

            // $this->info(var_dump($userList->UserListProfile()));
        }

        $userMail->touch();

        $email = $userMail->email;

        $queries = DB::getQueryLog();
        $this->info(count($queries));

        $this->log->addInfo("mail", array(
          "email" => $email,
          "send" => $sendEmail,
          "queries" => count($queries),
        ));

        if(!$sendEmail) {
            $this->info('no email sent');
            return;
        }

        Mail::send('emails.hacker', array('emailArr' => $emailArr), function($message) use ($email)
        {
            $message->to($email)->subject('Bans Found!');
        });
        $this->info('email sent');
    }

}
