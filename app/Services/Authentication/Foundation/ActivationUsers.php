<?php

namespace App\Services\Authentication\Foundation;

use App\User;
use App\Mail\Authentication\Activation;
use Illuminate\Support\Facades\Mail;

trait ActivationUsers {

	/**
	 * If activation is false we login the user.
	 */
	public function doLogin()
	{
		(property_exists($this, 'activate') and $this->activate) ?: $this->guard()->login($user);
	}

	/**
	 * Sent activation email, if active.
	 *
	 * @param \App\User $user
	 */
	public function sendActivationEmail(User $user)
	{
		(property_exists($this, 'activate') and $this->activate) ? Mail::to($user->email, $user->name)->send(new Activation($user)) : '';
	}
}
