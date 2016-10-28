<?php

namespace App\Providers;

use App\Services\Authentication\KeGuard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\CreatesUserProviders;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthServiceProvider extends ServiceProvider {

	use CreatesUserProviders;

	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		Auth::extend('ke', function($app, $name, array $config)
		{
			$provider = $this->createUserProvider($config['provider']);

			$guard = new KeGuard($provider);
			// When using the remember me functionality of the authentication services we
			// will need to be set the encryption instance of the guard, which allows
			// secure, encrypted cookie values to get generated for those cookies.
			if(method_exists($guard, 'setCookieJar'))
			{
				$guard->setCookieJar($this->app['cookie']);
			}
			if(method_exists($guard, 'setRequest'))
			{
				$guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
			}

			return $guard;
		});

		// Custom Validation
		Validator::extend('strongPassword', function($attribute, $value, $parameters, $validator)
		{
			return (bool)preg_match("#.*^(?=.{8,32})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $value);
		}, 'The given password is weak. Min length: 8 character. Contains: number, letter and capital letter.');
	}
}
