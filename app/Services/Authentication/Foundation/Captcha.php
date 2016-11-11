<?php

namespace App\Services\Authentication\Foundation;

use App\Services\Authentication\Models\LoginAttempts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

/**
 * Class Captcha
 * @package App\Services\Authentication\Foundation
 */
trait Captcha {

	private $message = '';

	/**
	 * Check if show captcha.
	 *
	 * @return bool
	 */
	public function showCaptcha()
	{
		return session()->has('hasCaptcha') && session('hasCaptcha');
	}

	/**
	 * Validate google captcha response.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	public function validateCaptcha(Request $request)
	{
		Validator::make($request->all(), [
			$this->username()      => 'required',
			'password'             => 'required',
			'g-recaptcha-response' => 'required|recaptchaRule',
		])->validate();
	}

	/**
	 * Return message.
	 *
	 * @return string
	 */
	public function getCaptchaMessage()
	{
		return $this->message;
	}

}
