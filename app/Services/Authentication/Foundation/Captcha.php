<?php

namespace App\Services\Authentication\Foundation;

use App\Services\Authentication\Models\LoginAttempts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class Captcha
 * @package App\Services\Authentication\Foundation
 */
trait Captcha {

	/**
	 * Check if show captcha.
	 *
	 * @return bool
	 */
	public function showCaptcha()
	{
		return session()->has('hasCaptcha') && session('hasCaptcha');
	}

}
