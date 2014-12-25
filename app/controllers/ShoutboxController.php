<?php namespace Parangi;

use Auth;
use Input;
use Response;
use View;

class ShoutboxController extends BaseController
{

    /**
     * Fetch new shouts
     *
     * @return Response
     */
    public function fetch()
    {
        $last_id = Input::get('last_id');
        $last_time = Input::get('last_time');

        $prevdate = Helpers::local_date('F j', $last_time);

        // Look for newer shouts
        $shouts = Shout::where('id', '>', $last_id)
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();

        $shouts = ShoutboxController::format($shouts, $prevdate);

        $html = '';
        foreach ($shouts as $i => $shout) {
            $html .= View::make('shoutbox.row')
                ->with('shout', $shout)
                ->render();

            if ($i == 0) {
                $group = 'shouts' . Helpers::local_date('md', $shout->created_at);
                $last_id = $shout->id;
                $last_time = (string)$shout->created_at;
            }
        }

        return Response::json([
            'html' => $html,
            'group' => $group,
            'last_id' => $last_id,
            'last_time' => $last_time
        ]);
    }

    /**
     * Post a new shout
     *
     * @return Response
     */
    public function post()
    {
        $message = trim(Input::get('message'));

        if (strlen($message) >= 2 && strlen($message) <= 500) {
            // Check for duplicate
            $last = Shout::where('user_id', '=', Auth::id())
                ->orderBy('id', 'desc')
                ->first();

            if ($message != $last->message) {
                $shout = Shout::create([
                    'user_id' => Auth::id(),
                    'message' => $message
                ]);

                return Response::json(['success' => true]);
            }
        }

        return Response::json(['success' => false]);
    }

    /**
     * Bulk format shouts
     *
     * @param  Collection $shouts
     * @return Collection
     */
    public static function format($shouts, $prevdate = null)
    {
        if (count($shouts) > 0) {
            $shouts->load(['user']);

            foreach ($shouts as $i => $shout) {
                $thedate = Helpers::local_date('F j', $shout->created_at);
                $shout->show_date = ($thedate != $prevdate);

                $shout->at_me = false;
                if (stristr($shout->message, '@' . $me->name)) {
                    $shout->at_me = true;
                }

                $shout->message = BBCode::parse($shout->message, true, true);
                //$shout->message = preg_replace_callback('#@([\\d\\w]+)#', 'parse_at_reply', $shout->message);

                $prevdate = $thedate;
            }
        }

        return $shouts;
    }

    /**
     * Show a full history of shoutbox posts
     *
     * @return Response
     */
    public function history()
    {
        $_PAGE['category'] = 'community';
        $_PAGE['title'] = 'Shoutbox History';

        $search = Input::get('search');

        // Delete shout
        // @todo allow admins to delete
        /*if (Input::has('del')) {
            Shout::where('id', '=', Input::get('del'))
                ->where('user_id', '=', $me->id)
                ->delete();

            return Redirect::to('community/shoutbox');
        }*/

        if ($search) {
            $shouts = Shout::where('message', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'desc')
                ->paginate(30);
        } else {
            $shouts = Shout::orderBy('id', 'desc')
                ->paginate(30);
        }

        $shouts = ShoutboxController::format($shouts);

        return View::make('shoutbox.history')
            ->with('_PAGE', $_PAGE)
            ->with('menu', GroupController::fetchMenu())
            ->with('shouts', $shouts)
            ->with('search', $search);
    }

}

