<?php

namespace App\Services\Authentication;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * These methods are typically the same across all guards.
 */
trait GuardHelpers {

	/**
	 * The currently authenticated user.
	 *
	 * @var \Illuminate\Contracts\Auth\Authenticatable
	 */
	protected $user;

	/**
	 * The user provider implementation.
	 *
	 * @var \Illuminate\Contracts\Auth\UserProvider
	 */
	protected $provider;

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable
	 * @throws \App\Services\Authentication\KeAuthenticationException
	 */
	public function authenticate()
	{
		if(!is_null($user = $this->user()))
		{
			return $user;
		}

		throw new KeAuthenticationException;
	}

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{
		return !is_null($this->user());
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return !$this->check();
	}

	/**
	 * Get the ID for the currently authenticated user.
	 *
	 * @return int|null
	 */
	public function id()
	{
		if($this->user())
		{
			return $this->user()->getAuthIdentifier();
		}
	}

	/**
	 * Set the current user.
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 *
	 * @return $this
	 */
	public function setUser(Authenticatable $user)
	{
		$this->user = $user;

		return $this;
	}
}
