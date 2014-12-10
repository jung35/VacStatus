<?php

class DonationPerkTableSeeder extends Seeder {

	public function run()
	{
    DB::table('donation_perk')->delete();

    DB::table('donation_perk')->insert(Array(
      array('perk' => 'donor_label', 'desc' => 'Donator Label on Profile', 'amount' => 1),
      array('perk' => 'list_10', 'desc' => 'Change List Limit to 20', 'amount' => 2.5),
      array('perk' => 'subscription', 'desc' => 'Subscription limit to 25', 'amount' => 3),
      array('perk' => 'user_50', 'desc' => 'Change User Limit to 75 per list', 'amount' => 5),
      array('perk' => 'search_50', 'desc' => 'Change Multi Search Limit to 70', 'amount' => 7.5),
      array('perk' => 'green_name', 'desc' => 'Green Username', 'amount' => 10),
    ));
	}

}
