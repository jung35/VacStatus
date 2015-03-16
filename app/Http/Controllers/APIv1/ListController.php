<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Update\MostTracked;
use VacStatus\Update\LatestTracked;

class ListController extends Controller {

	public function mostTracked()
	{
		$mostTracked = new MostTracked;

		$return = [
			'title' => 'Most Tracked Users',	
			'list' => $mostTracked->getMostTracked()
		];


		return $return;
	}

	public function latestTracked()
	{
		$latestTracked = new LatestTracked();

		$return = [
			'title' => 'Latest Tracked Users',
			'list' => $latestTracked->getLatestTracked()
		];

		return $return;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
