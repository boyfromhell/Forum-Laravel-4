<?php namespace Parangi;

use App;
use Auth;
use Input;
use Redirect;
use Request;
use Session;
use View;

class AvatarController extends BaseController
{
    use \Earlybird\FoundryController;

	/**
	 * Choose and manage avatars
	 *
	 * @return Response
	 */
	public function manage()
	{
		global $me;

		if (Request::isMethod('post')) {
			$id = Input::get('id');

			// Make sure it's mine
			if ($id > 0) {
				$avatar = Avatar::findOrFail($id);

				if ($avatar->user_id != Auth::id()) {
					App::abort(403);
				}
			}

			// Select different avatar
			if (Input::has('select')) {
				$me->avatar_id = $avatar->id;
				$me->save();

				Session::push('messages', 'Avatar updated');
			} else if (Input::has('delete')) {
				// Delete avatar
				if ($me->avatar_id == $avatar->id) {
					$me->avatar_id = null;
					$me->save();
				}

				$avatar->delete();

				Session::push('messages', 'Avatar deleted');
			}

			return Redirect::to('avatar');
		}

		$_PAGE['title'] = 'Avatar';

		$avatars = $me->avatars;
	
		return View::make('users.avatar')
			->with('_PAGE', $_PAGE)
			->with('menu', UserController::fetchMenu('avatar'))
			->with('avatars', $avatars)
			->with('default', ($me->avatar_id ? $me->avatar_id : 0));
	}

	/**
	 * Upload a new avatar
	 *
	 * @return Response
	 */
	public function upload()
	{
		global $me;

		if (Input::hasFile('avatar')) {
			$file = Input::file('avatar');

			if ($file->isValid()) {
				$ext = strtolower($file->getClientOriginalExtension());
				$name = time().'_'.str_random().'.'.$ext;
				$file->move(storage_path().'/uploads', $name);

				/*
					max_width = 150
					max_height = 150
					max_size = 32768
				*/

				$image = new Image(storage_path().'/uploads/'.$name);
				$image->scaleCrop(150, 150)
					->save()
					->pushToS3('images/avatars');
				$image->unlink();

				$avatar = Avatar::create([
					'user_id' => $me->id,
					'file'    => $name,
				]);

				$me->avatar_id = $avatar->id;
				$me->save();

				Session::push('messages', 'Avatar updated');

				return Redirect::to('avatar');
			}
		}

	}

}

