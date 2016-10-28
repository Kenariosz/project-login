<?php

namespace App\Services\Authentication\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface Guard {

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check();

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest();

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function user();

	/**
	 * Get the ID for the currently authenticated user.
	 *
	 * @return int|null
	 */
	public function id();

	/**
	 * Validate a user's credentials.
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  array                                     $credentials
	 *
	 * @return bool
	 */
	public function validate(Authenticatable $user, array $credentials = []);

	/**
	 * Set the current user.
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 *
	 * @return
	 */
	public function setUser(Authenticatable $user);
}
