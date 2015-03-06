<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class MockUpController extends Controller {

	public function indexPage()
	{
		return view('pages/home');
	}
}
