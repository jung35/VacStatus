<?php

class DonationPerkTableSeeder extends Seeder {

	public function run()
	{
    DB::table('donation_perk')->delete();

    DB::table('donation_perk')->insert(Array(
      array('perk' => 'donor_label', 'desc' => 'Donator Label on Profile', 'amount' => 1),
      array('perk' => 'list_10', 'desc' => 'Change List Limit to 10', 'amount' => 2.5),
      array('perk' => 'user_30', 'desc' => 'Change User Limit to 30', 'amount' => 5),
      array('perk' => 'search_50', 'desc' => 'Change Multi Search Limit to 50', 'amount' => 7.5),
      array('perk' => 'green_name', 'desc' => 'Green Username', 'amount' => 10),
    ));
	}

}
