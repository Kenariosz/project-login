<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Validator::extend('recaptchaRule', function($attribute, $value, $parameters, $validator)
		{
			$validator = new \Marwelln\Recaptcha\Model($value, '6LfbigsUAAAAAMRKj_0_vvcmXehqerlrm4eafenX');

			return $validator->validate();

		}, 'Wrong Captcha.');
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
