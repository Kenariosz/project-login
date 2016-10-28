<?php

namespace App\Exceptions;

use Exception;
use App\Services\Authentication\KeAuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		\App\Services\Authentication\KeAuthenticationException::class,
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Symfony\Component\HttpKernel\Exception\HttpException::class,
		\Illuminate\Database\Eloquent\ModelNotFoundException::class,
		\Illuminate\Session\TokenMismatchException::class,
		\Illuminate\Validation\ValidationException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception $exception
	 *
	 * @return void
	 */
	public function report(Exception $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Exception               $exception
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception)
	{

		$exception = $this->prepareException($exception);

		if($exception instanceof KeAuthenticationException)
		{
			return $this->unauthenticated($request, $exception);
		}
		else
		{
			return parent::render($request, $exception);
		}
	}

	/**
	 * Convert an authentication exception into an unauthenticated response.
	 *
	 * @param  \Illuminate\Http\Request                               $request
	 * @param  \App\Services\Authentication\KeAuthenticationException $exception
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, KeAuthenticationException $exception)
	{
		if($request->expectsJson())
		{
			return response()->json(['error' => 'Unauthenticated.'], 401);
		}

		return redirect()->guest('login');
	}
}
