<?php

namespace App\Services\Authentication\Models;

use App\Services\Authentication\Utilities\IPAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class LoginAttempts
 * @package App\Services\Authentication\Models
 */
class LoginAttempts extends Model {

	/**
	 * Login attempt's table name
	 *
	 * @var string
	 */
	protected $table = "login_attempts";

	/**
	 * Max login attempts for username and ip address
	 *
	 * @var int
	 */
	protected $max_login_attempts = 3;

	/**
	 * Max login attempts for netmask 24 bit
	 *
	 * @var int
	 */
	protected $max_login_attempts_ip_24 = 500;

	/**
	 * Max login attempts for netmask 16 bit
	 *
	 * @var int
	 */
	protected $max_login_attempts_ip_16 = 1000;

	/**
	 * The number of SECONDS you want to delete login attempts from DB.
	 *
	 * @var int
	 */
	protected $login_attempts_expiration = 3600;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'ip_address', 'ip_16', 'ip_24', 'username', 'type',
	];

	/**
	 * Count username login attempts
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 */
	public static function hasTooManyUsernameLoginAttempts(Request $request)
	{
		$loginAttempt = new static;

		return LoginAttempts::where('type', '=', 'user')->where('username', '=', $request->email)->where('created_at', '>', date('Y-m-d H:i:s', (time() - $loginAttempt->login_attempts_expiration)))->count() >= $loginAttempt->max_login_attempts;
	}

	/**
	 * Count username login attempts
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 */
	public static function hasTooManyIPAddressLoginAttempts(Request $request)
	{
		$loginAttempt = new static;

		return LoginAttempts::where('type', '=', 'ip_address')->where('ip_address', '=', $request->ip())->where('created_at', '>', date('Y-m-d H:i:s', (time() - $loginAttempt->login_attempts_expiration)))->count() >= $loginAttempt->max_login_attempts;
	}

	/**
	 * Count username login attempts
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 */
	public static function hasTooManyNetMask16LoginAttempts(Request $request)
	{
		$loginAttempt = new static;

		return LoginAttempts::where('type', '=', 'ip_16')->where('ip_16', '=', IPAddress::getCIDR($request->ip() . '/16'))->where('created_at', '>', date('Y-m-d H:i:s', (time() - $loginAttempt->login_attempts_expiration)))->count() >= $loginAttempt->max_login_attempts_ip_16;
	}

	/**
	 * Count username login attempts
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 */
	public static function hasTooManyNetMask24LoginAttempts(Request $request)
	{
		$loginAttempt = new static;

		return LoginAttempts::where('type', '=', 'ip_24')->where('ip_24', '=', IPAddress::getCIDR($request->ip() . '/24'))->where('created_at', '>', date('Y-m-d H:i:s', (time() - $loginAttempt->login_attempts_expiration)))->count() >= $loginAttempt->max_login_attempts_ip_24;
	}

	/**
	 * Increment login attempt
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param                          $type
	 */
	public static function incrementLoginAttempt(Request $request, $type)
	{
		$data = [
			'ip_address' => $request->ip(),
			'ip_16'      => IPAddress::getCIDR($request->ip() . '/16'),
			'ip_24'      => IPAddress::getCIDR($request->ip() . '/24'),
			'username'   => $request->email,
			'type'       => $type,
		];

		LoginAttempts::create($data);
	}

	/**
	 * Remove old Login attempts
	 *
	 * @return void
	 */
	public static function removeOldLoginAttempt()
	{
		$loginAttempt = new static;

		LoginAttempts::where('created_at', '<=', date('Y-m-d H:i:s', (time() - $loginAttempt->login_attempts_expiration)))->delete();
	}
}
