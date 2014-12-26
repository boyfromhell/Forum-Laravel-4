<?php namespace Parangi;

use View;

class BaseController extends \Controller
{

	/*protected $me;

	public function __construct()
	{
		$this->me = Auth::user();
	}*/

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

}
