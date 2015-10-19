<?php

use VacStatus\Models\Profile;
use VacStatus\Models\User;

use VacStatus\Models\Announcement;
use VacStatus\Models\DonationLog;
use VacStatus\Models\DonationPerk;
use VacStatus\Models\News;

use VacStatus\Models\ProfileBan;
use VacStatus\Models\ProfileOldAlias;

use VacStatus\Models\UserList;
use VacStatus\Models\UserListProfile;
use VacStatus\Models\UserMail;
use VacStatus\Models\Subscription;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$mainUser = [
	'small_id'			=> 60051399,
	'display_name'		=> 'Jung',
	'privacy'			=> 3, // public (steam)
	'avatar_thumb'		=> 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/b3/b369c03ae8b247b737b60e6f19d8c0008fedbb11.jpg',
	'avatar'			=> 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/b3/b369c03ae8b247b737b60e6f19d8c0008fedbb11_full.jpg',
	'profile_created'	=> 1264458278,
	'alias'				=> json_encode([]),

	'donation'			=> 0,
	'site_admin'		=> 0,
	'beta'				=> 0,
	'user_key'			=> 'asdf',
	'friendslist'		=> json_encode([]),
];

/**
 * VacStatus\Models\Profile
 */
$factory->define(Profile::class, function () use ($mainUser) {
	return [
		'small_id'			=> $mainUser['small_id'],
		'display_name'		=> $mainUser['display_name'],
		'privacy'			=> $mainUser['privacy'],
		'avatar_thumb'		=> $mainUser['avatar_thumb'],
		'avatar'			=> $mainUser['avatar'],
		'profile_created'	=> $mainUser['profile_created'],
		'alias'				=> $mainUser['alias'],
	];
});

$factory->defineAs(Profile::class, 'private', function () {
	return array_merge(
		$factory->raw(Profile::class), [ 'privacy' => 1 ]
	);
});

/**
 * VacStatus\Models\User
 */
$factory->define(User::class, function () use ($mainUser) {
	return [
		'small_id'		=> $mainUser['small_id'],
		'display_name'	=> $mainUser['display_name'],
		'donation'		=> $mainUser['donation'],
		'site_admin'	=> $mainUser['site_admin'],
		'beta'			=> $mainUser['beta'],
		'user_key'		=> $mainUser['user_key'],
		'friendslist'	=> $mainUser['friendslist'],
	];
});

$factory->defineAs(User::class, 'admin', function () {
	return array_merge(
		$factory->raw(User::class), [ 'site_admin' => 1 ]
	);
});

$factory->defineAs(User::class, 'donator', function () {
	return array_merge(
		$factory->raw(User::class), [ 'donation' => 10.00 ]
	);
});

$factory->defineAs(User::class, 'beta', function () {
	return array_merge(
		$factory->raw(User::class), [ 'beta' => 1 ]
	);
});

/**
 * VacStatus\Models\Announcement
 */
$factory->define(Announcement::class, function () {
	return [ 'value' => 'hello' ];
});

/**
 * VacStatus\Models\DonationLog
 */
$factory->define(DonationLog::class, function () use ($mainUser) {
	return [
		'small_id'			=> $mainUser['small_id'],
		'status'			=> 'Completed',
		'original_amount'	=> 5,
		'amount'			=> 4.5,
	];
});

/**
 * VacStatus\Models\DonationPerks
 */
$factory->define(DonationPerk::class, function () {
	return [
		'perk'		=> 'test_perk',
		'desc'		=> 'test desc',
		'amount'	=> 0.00
	];
});

$factory->defineAs(DonationPerk::class, '$1.00', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'donor_label', 'amount' => 1.00 ]
	);
});

$factory->defineAs(DonationPerk::class, '$2.50', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'list_1', 'amount' => 2.50 ]
	);
});

$factory->defineAs(DonationPerk::class, '$3.00', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'subscription', 'amount' => 3.00 ]
	);
});

$factory->defineAs(DonationPerk::class, '$5.00', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'user_1', 'amount' => 5.00 ]
	);
});

$factory->defineAs(DonationPerk::class, '$7.50', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'search_1', 'amount' => 7.50 ]
	);
});

$factory->defineAs(DonationPerk::class, '$10.00', function () {
	return array_merge(
		$factory->raw(DonationPerk::class),
		[ 'perk' => 'green_name', 'amount' => 10.00 ]
	);
});

/**
 * VacStatus\Models\News
 */
$factory->define(News::class, function (Faker\Generator $faker) {
	return [
		'title'	=> $faker->sentence(4)
		'body'	=> $faker->paragraph(5),
	];
});

/**
 * VacStatus\Models\ProfileBan
 */
$factory->define(ProfileBan::class, function () {
	return [
		'profile_id'	=> factory(Profile::class)->create()->id,
		'community'		=> 0,
		'trade'			=> 0,
		'vac_bans'		=> 0,
		'game_bans'		=> 0,
		'last_ban_date'	=> (new \DateTime())->format('Y-m-d')
	];
});

/**
 * VacStatus\Models\ProfileOldAlias
 */
$factory->define(ProfileOldAlias::class, function (Faker\Generator $faker) {
	return [
		'profile_id'	=> factory(Profile::class)->create()->id,
		'seen'			=> (new \DateTime())->format('Y-m-d'),
		'seen_alias'	=> $faker->name,
	];
});

/**
 * VacStatus\Models\UserList
 */
$factory->define(UserList::class, function (Faker\Generator $faker) {
	return [
		'user_id' => factory(User::class)->create()->id,
		'title' => $faker->sentence(3),
		'privacy' => 1, // public
	];
});

$factory->defineAs(UserList::class, 'friends', function () {
	return array_merge( $factory->raw(UserList::class), [ 'privacy' => 2 ] );
});

$factory->defineAs(UserList::class, 'private', function () {
	return array_merge( $factory->raw(UserList::class), [ 'privacy' => 3 ] );
});

/**
 * VacStatus\Models\UserListProfile
 */
$factory->define(UserListProfile::class, function (Faker\Generator $faker) {
	$profile = factory(Profile::class)->create();
	return [
		'user_list_id' => factory(UserList::class)->create()->id,
		'profile_id' => $profile->id,
		'profile_name' => $profile->display_name,
		'profile_description' => $faker->sentence(3),
	];
});

/**
 * VacStatus\Models\UserMail
 */
$factory->define(UserMail::class, function (Faker\Generator $faker) {
	return [
		'user_id' => factory(User::class)->create()->id,
		'email' => null,
		'verify' => null,
		'pushbullet' => null,
		'pushbullet_verify' => null,
	];
});

/**
 * VacStatus\Models\Subscription
 */
$factory->define(Subscription::class, function () {
	return [
		'user_id' => factory(User::class)->create()->id,
		'user_list_id' => factory(UserList::class)->create()->id,
	];
});