<?php

namespace App\Http\Controllers\Authentication;

use App\User;
use App\Http\Controllers\Controller;
use App\Services\Authentication\Foundation\RegistersUsers;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after login / registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/login';

	/**
	 * Use user activation feature.
	 *
	 * @var bool
	 */
	protected $activate = true;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array $data
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name'     => 'required|min:3|max:255',
			'email'    => 'required|email|max:255|unique:users',
			'password' => 'required|max:32|strongPassword|confirmed',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array $data
	 *
	 * @return User
	 */
	protected function create(array $data)
	{
		$user = User::create([
			'name'     => $data['name'],
			'email'    => $data['email'],
			'password' => bcrypt($data['password']),
		]);

		$this->sendActivationEmail($user);

		flash()->success('Success!', 'Your registration was successful. We sent an activation email.');

		return $user;
	}

	/**
	 * Activate the user.
	 *
	 * @param $activationToken
	 *
	 * @return mixed
	 */
	protected function activate($activationToken)
	{
		$user = User::whereActivationToken($activationToken)->firstOrFail();
		$user->active = 1;
		$user->activation_token = null;
		$user->save();

		flash()->success('Success!', 'Your activation was successful.');

		return $user;
	}
}
