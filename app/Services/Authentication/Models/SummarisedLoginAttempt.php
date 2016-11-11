<?php

namespace App\Services\Authentication\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SummarisedLoginAttempt
 * @package App\Services\Authentication\Models
 */
class SummarisedLoginAttempt extends Model {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'all', 'ip_address', 'ip_16', 'ip_24', 'username',
	];

	/**
	 * The number of SECONDS you want to summarise login attempts.
	 *
	 * @var int
	 */
	protected $refreshCycle = 300;

	/**
	 * Return last row
	 *
	 * @return Builder
	 */
	public static function getSummarised()
	{
		return static::orderBy('id', 'desc')->first();
	}

	/**
	 * Summarise login attempts
	 *
	 * @return void
	 */
	public static function summariseLoginAttempts()
	{
		$summarisedLoginAttempt = new static;

		if(0 === $summarisedLoginAttempt::where('created_at', '>', date('Y-m-d H:i:s', (time() - $summarisedLoginAttempt->refreshCycle)))->count())
		{
			$summarisedLoginAttempt->all = LoginAttempts::count();
			$summarisedLoginAttempt->ip_address = LoginAttempts::where('type', 'ip_address')->count();
			$summarisedLoginAttempt->username = LoginAttempts::where('type', 'user')->count();
			$summarisedLoginAttempt->ip_16 = LoginAttempts::where('type', 'ip_16')->count();
			$summarisedLoginAttempt->ip_24 = LoginAttempts::where('type', 'ip_24')->count();

			$summarisedLoginAttempt->save();
		}
	}
}
