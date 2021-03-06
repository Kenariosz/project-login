<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public static function boot()
	{
		parent::boot();

		// TODO: create token generator
		static::creating(function($user)
		{
			$user->activation_token = str_random(100);
		});
	}

	/**
	 * Check the user activation.
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return (bool)$this->active;
	}
}
