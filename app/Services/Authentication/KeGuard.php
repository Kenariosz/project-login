<?php

namespace App\Services\Authentication;

use App\Services\Authentication\Contracts\Guard;
use App\Services\Authentication\Foundation\Captcha;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Cookie\QueueingFactory as CookieJar;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class KeGuard
 * @package App\Services\Authentication
 */
class KeGuard implements Guard {

	use GuardHelpers, Captcha;

	/**
	 * The name of the Guard. Typically "session".
	 *
	 * Corresponds to driver name in authentication configuration.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Indicates if the logout method has been called.
	 *
	 * @var bool
	 */
	protected $loggedOut = false;

	/**
	 * The request instance.
	 *
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * Create a new authentication guard.
	 *
	 * @param \Illuminate\Contracts\Auth\UserProvider $provider
	 *
	 * @return void
	 */
	public function __construct(UserProvider $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function user()
	{
		if($this->loggedOut)
		{
			return null;
		}
		// If we've already retrieved the user for the current request
		if(!is_null($this->user))
		{
			return $this->user;
		}

		$id = session($this->getName());

		// First we will try to load the user using the identifier in the session if one exists.
		$user = null;

		if(!is_null($id))
		{
			$user = $this->provider->retrieveById($id);
		}

		// If the user is null, but we decrypt a "recaller" cookie we can attempt to
		// pull the user data on that cookie which serves as a remember cookie on
		// the application. Once we have a user we can return it to the caller.
		$recaller = $this->getRecaller();

		if(is_null($user) && !is_null($recaller))
		{
			$user = $this->getUserByRecaller($recaller);

			if($user)
			{
				$this->updateSession($user->getAuthIdentifier());
			}
		}

		return $this->user = $user;
	}

	/**
	 * Attempt to authenticate a user using the given credentials.
	 *
	 * @param  array $credentials
	 * @param  bool  $remember
	 * @param  bool  $login
	 *
	 * @return bool
	 */
	public function attempt(array $credentials = [], $remember = false, $login = true)
	{
		$user = $this->provider->retrieveByCredentials($credentials);

		if($this->validate($user, $credentials))
		{
			if($login)
			{
				$this->login($user, $remember);
			}

			return true;
		}

		return false;
	}

	/**
	 * Log a user into the application.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  bool                                       $remember
	 *
	 * @return void
	 */
	public function login(Authenticatable $user, $remember = false)
	{
		$this->updateSession($user->getAuthIdentifier());

		if($remember)
		{
			$this->createRememberTokenIfDoesntExist($user);
			$this->queueRecallerCookie($user);
		}

		$this->setUser($user);
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		$user = $this->user();
		$this->clearUserDataFromStorage();
		if(!is_null($this->user))
		{
			$this->refreshRememberToken($user);
		}

		$this->user = null;
		$this->loggedOut = true;
	}

	/**
	 * Remove the user data from the session and cookies.
	 *
	 * @return void
	 */
	protected function clearUserDataFromStorage()
	{
		session()->forget($this->getName());
		if(!is_null($this->getRecaller()))
		{
			$recaller = $this->getRecallerName();

			$this->getCookieJar()->queue($this->getCookieJar()->forget($recaller));
		}
	}

	/**
	 * Get the cookie creator instance used by the guard.
	 *
	 * @return \Illuminate\Contracts\Cookie\QueueingFactory
	 *
	 * @throws \RuntimeException
	 */
	public function getCookieJar()
	{
		if(!isset($this->cookie))
		{
			throw new RuntimeException('Cookie jar has not been set.');
		}

		return $this->cookie;
	}

	/**
	 * Validate a user's credentials.
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param array                                      $credentials
	 *
	 * @return bool
	 */
	public function validate($user, array $credentials = [])
	{
		return !is_null($user) && $user->isActive() && $this->provider->validateCredentials($user, $credentials);
	}

	/**
	 * Update the session with the given ID.
	 *
	 * @param  string $id
	 *
	 * @return void
	 */
	protected function updateSession($id)
	{
		session([$this->getName() => $id]);
	}

	/**
	 * Create a new "remember me" token for the user if one doesn't already exist.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 *
	 * @return void
	 */
	protected function createRememberTokenIfDoesntExist(Authenticatable $user)
	{
		if(empty($user->getRememberToken()))
		{
			$this->refreshRememberToken($user);
		}
	}

	/**
	 * Refresh the "remember me" token for the user.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 *
	 * @return void
	 */
	protected function refreshRememberToken(Authenticatable $user)
	{
		$user->setRememberToken($token = Str::random(60));

		$this->provider->updateRememberToken($user, $token);
	}

	/**
	 * Queue the recaller cookie into the cookie jar.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 *
	 * @return void
	 */
	protected function queueRecallerCookie(Authenticatable $user)
	{
		$value = $user->getAuthIdentifier() . '|' . $user->getRememberToken();
		$this->getCookieJar()->queue($this->createRecaller($value));
	}

	/**
	 * Create a "remember me" cookie for a given ID.
	 *
	 * @param  string $value
	 *
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	protected function createRecaller($value)
	{
		return $this->getCookieJar()->forever($this->getRecallerName(), $value);
	}

	/**
	 * Get the decrypted recaller cookie for the request.
	 *
	 * @return string|null
	 */
	protected function getRecaller()
	{
		return $this->request->cookies->get($this->getRecallerName());
	}

	/**
	 * Get the user ID from the recaller cookie.
	 *
	 * @return string|null
	 */
	protected function getRecallerId()
	{
		if($this->validRecaller($recaller = $this->getRecaller()))
		{
			return head(explode('|', $recaller));
		}
	}

	/**
	 * Determine if the recaller cookie is in a valid format.
	 *
	 * @param  mixed $recaller
	 *
	 * @return bool
	 */
	protected function validRecaller($recaller)
	{
		if(!is_string($recaller) || !Str::contains($recaller, '|'))
		{
			return false;
		}

		$segments = explode('|', $recaller);

		return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
	}

	/**
	 * Get a unique identifier for the auth session value.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'login_' . $this->name . '_' . sha1(static::class);
	}

	/**
	 * Get the name of the cookie used to store the "recaller".
	 *
	 * @return string
	 */
	public function getRecallerName()
	{
		return 'remember_' . $this->name . '_' . sha1(static::class);
	}

	/**
	 * Set the current request instance.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return $this
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;

		return $this;
	}

	/**
	 * Set the cookie creator instance used by the guard.
	 *
	 * @param  \Illuminate\Contracts\Cookie\QueueingFactory $cookie
	 *
	 * @return void
	 */
	public function setCookieJar(CookieJar $cookie)
	{
		$this->cookie = $cookie;
	}

	/**
	 * Get the user provider used by the guard.
	 *
	 * @return \Illuminate\Contracts\Auth\UserProvider
	 */
	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * Set the user provider used by the guard.
	 *
	 * @param  \Illuminate\Contracts\Auth\UserProvider $provider
	 *
	 * @return void
	 */
	public function setProvider(UserProvider $provider)
	{
		$this->provider = $provider;
	}

}
