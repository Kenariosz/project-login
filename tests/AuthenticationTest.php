<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticationTest extends TestCase {

	use DatabaseTransactions;

	/** @test */
	public function testNewUserRegistration()
	{
		$this->visit('login')
			->click('Registration')
			->seePageIs('register')
			->type('bob', 'name')
			->type('hello1@in.com', 'email')
			->type('hello1', 'password')
			->type('hello1', 'password_confirmation')
			->press('Register')
			->see('Your registration was successful. We sent an activation email.')
			->seePageIs('login');
	}
}