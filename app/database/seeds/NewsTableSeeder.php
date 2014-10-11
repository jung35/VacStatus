<?php

class NewsTableSeeder extends Seeder {

	public function run()
	{
    DB::table('news')->delete();

    DB::table('news')->insert(Array(
      array( 'title' => 'It\"s finally up!',
            'body' => '<p>If anything breaks, please contact me via email, reddit, or facepunch</p>',
            'created_at' => '2014-03-17 19:51:00',
            'updated_at' => '0000-00-00 00:00:00'
            ),

      array( 'title' => 'Version 0.2 now up!',
            'body' => '<p>I made some changes in the back end and made profile viewing and searching for public. I&#39;ve also added new tab that you guys can see to check who is the most tracked person.</p>',
            'created_at' => '2014-03-18 04:00:00',
            'updated_at' => '2014-03-18 04:00:00'
            ),

      array( 'title' => 'New Update! (v0.4)',
            'body' => '<p>Yay! I&#39;ve added a new feature and fixed few bugs that was reported. I&#39;m still going to be working on this so keep on reporting! Thank you to everyone that has reported bugs and suggested improvements. I may not respond to all of them, but I read them all! Also, I&#39;ve uploaded source code on Github!</p>',
            'created_at' => '2014-04-23 04:00:00',
            'updated_at' => '2014-04-23 04:00:00'
            ),

      array( 'title' => 'Mail Notification added!',
            'body' => '<p>Right now my to-do list is getting smaller and smaller. Check your menu to subscribe to mail notification.</p>',
            'created_at' => '2014-05-03 04:00:00',
            'updated_at' => '2014-05-03 04:00:00'
            ),

      array( 'title' => 'Ajax Update',
            'body' => '<p>Finally! I&#39;ve been working on this while finishing up school (still in it) sorry for being so slow :(</p><p>When the user needs to be updated, they are now updated after page loads so you do not have to wait for the player to be updated while page loads.</p><p>(This is only for listing players. When viewing players, you will have to wait for the player to be updated.)</p><p>&nbsp;</p><p>I&#39;ve also been working on Admin Panel before I worked on the ajax update (also working on project related to CS:GO with someone.)</p>',
            'created_at' => '2014-05-21 20:42:59',
            'updated_at' => '2014-05-21 20:42:59'
            ),

      array( 'title' => 'Less requests to the database',
            'body' => '<p>I forgot that requesting database was a big issue when it came to speed while making this site. Then, someone reminded me on steam saying that the site was slow! It took me a while, but I realized I never optimized what I request and now, there are less queries to the database and pageload time should be shortened.</p><p>Also found out that the excessive queries to database has been giving some people errors, that should also be fixed now.</p>',
            'created_at' => '2014-05-26 20:42:59',
            'updated_at' => '2014-05-26 20:42:59'
            )
    ));
	}

}
