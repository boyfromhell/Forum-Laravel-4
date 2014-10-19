<?php

class AvatarController extends Earlybird\FoundryController
{

	/**
	 * Choose and manage avatars
	 *
	 * @return Response
	 */
	public function manage()
	{
		$me = Auth::user();

		if( Request::isMethod('post') )
		{
			$id = Input::get('id');

			// Make sure it's mine
			if( $id > 0 ) {
				$avatar = Avatar::findOrFail($id);

				if( $avatar->user_id != Auth::id() ) {
					App::abort(403);
				}
			}

			// Select different avatar
			if( Input::has('select') ) {
				$me->avatar_id = $avatar->id;
				$me->save();

				Session::push('messages', 'Avatar updated');
			}
			// Delete avatar
			else if( Input::has('delete') ) {
				if( $me->avatar_id == $avatar->id ) {
					$me->avatar_id = NULL;
					$me->save();
				}

				$avatar->delete();

				Session::push('messages', 'Avatar deleted');
			}

			return Redirect::to('avatar');
		}

		$_PAGE = array(
			'category' => 'usercp',
			'section'  => 'avatar',
			'title'    => 'Avatar',
		);

		$avatars = $me->avatars;
	
		return View::make('users.avatar')
			->with('_PAGE', $_PAGE)
			->with('avatars', $avatars)
			->with('default', ( $me->avatar_id ? $me->avatar_id : 0 ));
	}

	/**
	 * Upload a new avatar
	 *
	 * @return Response
	 */
	public function upload()
	{
		/*
		$file = $_FILES['u_avatar'];
		if( $file['name'] ) {
			$data = array(
				'user_id' => $me->id,
				'date'    => $gmt
			);

			try {
				$avatar = new Avatar(null, $data);
				
				$options = array(
					'max_width'  => 150,
					'max_height' => 150,
					'max_size'   => 32768
				);			
				$avatar->image = new Image($_FILES['u_avatar'], $options);
			}
			catch( Exception $e ) {
				$errors[] = $e->getMessage();
			}
			
			if( !count($errors) ) {
				$file = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $me->name));
				$file = "{$file}_{$gmt}.{$avatar->image->extension}";
				$dest = 'web/images/avatars/' . $file;
				$avatar->file = $file;

				$success = $avatar->image->upload($dest);

				if( $success ) {
					$avatar->save();
					$avatar->push_to_s3();
					$me->avatar_id = $avatar->id;
					$me->avatar = $avatar;
					
					$me->last = 1;
					$me->save(array('fields' => array('avatar_id', 'last')));
					
					header("Location: /avatar");
					exit;
				}
				else {
					$errors[] = 'Problem uploading avatar';
				}
			}
		}
		*/

		Session::push('messages', 'Avatar updated');

		return Redirect::to('avatar');
	}

}

