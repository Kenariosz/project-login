<?php

namespace APP\Services\Authentication\Foundation;

use App\Services\Authentication\Foundation\RedirectsUsers;
use App\Services\Authentication\Foundation\KeSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

/**
 * Class AuthenticatesUsers
 * @package APP\Services\Authentication\Foundation
 */
trait AuthenticatesUsers {

	use RedirectsUsers, KeSecurity;

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		return view('authentication.login');
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		$this->validateLogin($request);
		// Count login attempts
		if($this->hasTooManyLoginAttempts($request))
		{
			session(['hasCaptcha' => true]);
		}
		else
		{
			session(['hasCaptcha' => false]);
		}
		// Try to login
		$credentials = $this->credentials($request);
		if($this->guard()->attempt($credentials, $request->has('remember')))
		{
			return $this->sendLoginResponse($request);
		}
		// Remove old login attempts
		$this->clearLoginAttempt();
		// Increase login attempts
		$this->addLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	/**
	 * Validate the user login request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return void
	 */
	protected function validateLogin(Request $request)
	{
		$this->validate($request, [
			$this->username() => 'required', 'password' => 'required',
		]);
	}

	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	protected function credentials(Request $request)
	{
		return $request->only($this->username(), 'password');
	}

	/**
	 * Send the response after the user was authenticated.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function sendLoginResponse(Request $request)
	{
		$request->session()->regenerate();

		return $this->authenticated($request, $this->guard()->user())
			?: redirect()->intended($this->redirectPath());
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  mixed                    $user
	 *
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user)
	{
		//
	}

	/**
	 * Get the failed login response instance.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function sendFailedLoginResponse(Request $request)
	{
		return redirect()->back()
			->withInput($request->only($this->username(), 'remember'))
			->withErrors([
				$this->username() => Lang::get('auth.failed'),
			]);
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		return 'email';
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		$this->guard()->logout();

		$request->session()->flush();

		$request->session()->regenerate();

		return redirect('/');
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @return \Illuminate\Contracts\Auth\StatefulGuard
	 */
	protected function guard()
	{
		return Auth::guard();
	}
}
