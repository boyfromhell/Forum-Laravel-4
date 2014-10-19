<?php

class RemindersController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'signin',
			'title'    => 'Sign in',
		);

		return View::make('password.remind')
			->with('_PAGE', $_PAGE);
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		$response = Password::remind(Input::only('email'), function($message)
		{
			$message->subject('Reset your password');
		});

		switch( $response )
		{
			case Password::INVALID_USER:
				Session::push('errors', Lang::get($response));
				return Redirect::back();
				break;

			case Password::REMINDER_SENT:
				Session::push('messages', Lang::get($response));
				return Redirect::back();
				break;
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'signin',
			'title'    => 'Sign in',
		);

		if (is_null($token)) App::abort(404);

		return View::make('password.reset')
			->with('_PAGE', $_PAGE)
			->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				Session::push('errors', Lang::get($response));
				return Redirect::back();
				break;

			case Password::PASSWORD_RESET:
				Session::push('messages', 'Your password has been updated');
				return Redirect::to('/');
				break;
		}
	}

}

