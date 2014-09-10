<?php

class ShoutboxController extends BaseController
{

	/**
	 * Show the embedded shoutbox
	 */
	public function embed()
	{
		global $me;

		define('DO_NOT_INCREMENT', true);

		// Delete shout
		// @todo allow admins to delete
		if( Input::has('del') ) {
			Shout::where('id', '=', Input::get('del'))
				->where('user_id', '=', $me->id)
				->delete();

			return Redirect::to('community/shoutbox');
		}

		// Fetch most recent shouts
		$shouts = Shout::orderBy('id', 'desc')
			->take(30);

		/*$count = 0;
		$prevdate = -1;
		while( $data = $exec->fetch_assoc() )
		{
			// This is sloppy, I don't like it
			// @todo clean this up, also get rid of Shout::get_date() function
			$shout->at_me = false;
			$shout->message = BBCode::parse($shout->message, true, true);
			if( stristr($shout->message, '@'.$me->name) ) {
				$shout->at_me = true;
			}
			$shout->message = preg_replace_callback('#@([\\d\\w]+)#', 'parse_at_reply', $shout->message);
			
			$shout->time += ($me->tz*3600);
			$thedate = $shout->get_date('F j');

			$shout->show_date = ( $thedate != $prevdate );

			if( $count == 0 ) {
				$last_id = $shout->id;
				$last_time = $shout->time;
			}
			$prevdate = $thedate;
			$count++;
		}*/

		//-----------------------------------------------------------------------------
		// Smarty templating

		$_PAGE = array(
			'title' => 'Shoutbox',
		);

		return View::make('shoutbox.embed')
			->with('_PAGE', $_PAGE)
			->with('shouts', $shouts);

		/*$Smarty->assign('last_id', $last_id);
		$Smarty->assign('last_time', $last_time);*/
	}

}
