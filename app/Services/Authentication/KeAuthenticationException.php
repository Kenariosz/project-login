<?php

namespace App\Services\Authentication;

use Exception;

/**
 * Class KeAuthenticationException
 * @package App\Services\Authentication
 */
class KeAuthenticationException extends Exception {

	/**
	 * All of the guards that were checked.
	 *
	 * @var array
	 */
	protected $guards;

	/**
	 * Create a new authentication exception.
	 *
	 * @param string $message
	 * @param array  $guards
	 */
	public function __construct($message = 'Unauthenticated.', array $guards = [])
	{
		parent::__construct($message);

		$this->guards = $guards;
	}

	/**
	 * Get the guards that were checked.
	 *
	 * @return array
	 */
	public function guards()
	{
		return $this->guards;
	}
}
