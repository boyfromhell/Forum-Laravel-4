<?php

class ScoreController extends Earlybird\FoundryController
{

	/**
	 * Show honor rolls
	 *
	 * @return Response
	 */
	public function index()	
	{
		$_PAGE = array(
			'category' => 'community',
			'section'  => 'honorrolls',
			'title'    => 'Honor Rolls',
		);

		$categories = array();
		$types = array('Victories', 'Defeats');

		foreach( $types as $i => $name )
		{
			$scores = Score::where('victory', '=', (1-$i))
				->orderBy('score', 'desc')
				->orderBy('id', 'asc')
				->take(20)
				->get();

			if( count($scores) > 0 ) {
				$scores->load(['user']);
			}

			$categories[$name] = $scores;
		}

		return View::make('scores.index')
			->with('_PAGE', $_PAGE)
			->with('categories', $categories);
	}

	/**
	 * Submit a new score
	 *
	 * @return Response
	 */
	public function submit()
	{
		global $me;

		$_PAGE = array(
			'category' => 'community',
			'section'  => 'honorrolls',
			'title'    => 'Submit Score',
		);

		if( Request::isMethod('post') )
		{
			$rules = [
				'character' => 'required',
				'score' => 'required|integer',
				'variant' => 'required',
				'ending' => 'required',
			];

			$validator = Validator::make(Input::all(), $rules);

			if( $validator->fails() )
			{
				foreach( $validator->messages()->all() as $error ) {
					Session::push('errors', $error);
				}

				return Redirect::to('community/submit_score')
					->withInput();
			}
			else
			{
				// Insert new score
				$score = Score::create([
					'character' => Input::get('character'),
					'user_id'   => $me->id,
					'score'     => Input::get('score'),
					'variant'   => Input::get('variant'),
					'victory'   => Input::get('victory', 0),
					'ending'    => Input::get('ending'),
					'url'       => Input::get('url'),
				]);

				Session::push('messages', 'Score added');

				return Redirect::to('community/honor_rolls');
			}
		}

		return View::make('scores.submit')
			->with('_PAGE', $_PAGE);
	}

}
