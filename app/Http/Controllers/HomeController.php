<?php

namespace App\Http\Controllers;

use App\Services\Authentication\Models\LoginAttempts;
use App\Services\Authentication\Models\SummarisedLoginAttempt;
use Illuminate\Http\Request;

class HomeController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('pages.home');
	}

	/**
	 * Show summarised login attempts
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function listSummarisedLA()
	{
		SummarisedLoginAttempt::summariseLoginAttempts();
		$loginAttempts = SummarisedLoginAttempt::getSummarised();

		return view('pages.listSummarisedLA', compact('loginAttempts'));
	}

}
