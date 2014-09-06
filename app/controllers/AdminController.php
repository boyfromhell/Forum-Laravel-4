<?php

class AdminController extends BaseController
{

	/**
	 * View message
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function viewMessage( $id )
	{
		$message = AdminMessage::findOrFail($id);

		return View::make('admin.view_message')
			->with('message', $message);
	}

}
