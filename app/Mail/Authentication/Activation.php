<?php

namespace App\Mail\Authentication;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Activation extends Mailable {

	use Queueable, SerializesModels;

	public $user = '';

	/**
	 * Create a new message instance.
	 *
	 * @param \App\User $user
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->view('emails.authentication.activation');
	}
}
