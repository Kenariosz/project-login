<?php

namespace App\Services\Authentication\Foundation;

use App\Services\Authentication\Models\LoginAttempts;
use Illuminate\Http\Request;

/**
 * Class KeSecurity
 * @package App\Services\Authentication\Foundation
 */
trait KeSecurity {

	/**
	 * Increase login attempts.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return void
	 */
	public function addLoginAttempts(Request $request)
	{
		if(!$this->hasTooManyLoginAttempts($request))
		{
			LoginAttempts::incrementLoginAttempt($request, 'ip_16');
			LoginAttempts::incrementLoginAttempt($request, 'ip_24');
		}
		LoginAttempts::incrementLoginAttempt($request, 'user');
		LoginAttempts::incrementLoginAttempt($request, 'ip_address');
	}

	/**
	 * Check login attempts limit.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	public function hasTooManyLoginAttempts(Request $request)
	{
		return LoginAttempts::hasTooManyUsernameLoginAttempts($request) || LoginAttempts::hasTooManyIPAddressLoginAttempts($request) || LoginAttempts::hasTooManyNetMask16LoginAttempts($request) || LoginAttempts::hasTooManyNetMask24LoginAttempts($request);
	}

	/**
	 * Clear login attempts
	 *
	 * @return void
	 */
	public function clearLoginAttempt()
	{
		LoginAttempts::removeOldLoginAttempt();
	}

}
