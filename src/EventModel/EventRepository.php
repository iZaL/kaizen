<?php namespace Acme\EventModel;

use Acme\Core\CrudableTrait;
use Carbon\Carbon;
use DB;
use EventModel;
use Acme\Core\Repositories\Illuminate;
use Acme\Core\Repositories\AbstractRepository;
use Redirect;
use User;

class EventRepository extends AbstractRepository {

    use CrudableTrait;

    public $model;

    public function __construct(EventModel $model)
    {
        $this->model = $model;
    }

    public function getAll($with = [])
    {
        $currentTime = Carbon::now()->toDateTimeString();

        return $this->model->with($with)//->where('date_start', '>', $currentTime)
            ;

    }

    /**
     * Return Events For Event Index Page
     * @param $perPage
     * @return mixed
     *
     */
    public function getEvents($perPage = 10)
    {
        return $this->getAll()
            ->orderBy('date_start', 'DESC')
            ->paginate($perPage);
    }

    /**
     * @return array|null|static[]
     * Fetch Posts For Sliders
     */

    // Afdal that's so weird .. this function is repeated in EventRepository and EventsController !!!!!!!!!!!!!!!!!!!!!!!!!

    public function getSliderEvents()
    {
        // fetch 3 latest post
        // fetches 2 featured post
        // order by event date, date created, featured
        // combines them into one query to return for slider

        $latestEvents   = $this->latestEvents();
        $featuredEvents = $this->feautredEvents();
        $events         = array_merge((array) $latestEvents, (array) $featuredEvents);
        if ( $events ) {
            foreach ( $events as $event ) {
                $array[] = $event->id;
            }
            $events_unique = array_unique($array);
            $sliderEvents  = $this->mergeSliderEvents(6, $events_unique);

            return $sliderEvents;
        } else {
            return null;
        }

    }

    /**
     * Fetches posts for latest Event
     * @return array
     *
     */
    public function latestEvents()
    {
        return DB::table('events as e')
            ->join('photos as p', 'e.id', '=', 'p.imageable_id', 'LEFT')
            ->where('p.imageable_type', '=', 'EventModel')
            ->where('e.date_start', '>', Carbon::now()->toDateTimeString())
            ->orderBy('e.date_start', 'DESC')
            ->orderBy('e.created_at', 'DESC')
            ->take('5')
            ->get(array('e.id'));
    }

    /**
     * Fetches posts for latest Event
     * @return array
     *
     */
    public function feautredEvents()
    {
        return DB::table('events AS e')
            ->join('photos AS p', 'e.id', '=', 'p.imageable_id', 'LEFT')
            ->where('p.imageable_type', '=', 'EventModel')
            ->where('e.date_start', '>', Carbon::now()->toDateTimeString())
            ->where('e.featured', '1')
            ->orderBy('e.date_start', 'DESC')
            ->orderBy('e.created_at', 'DESC')
            ->take('5')
            ->get(array('e.id'));
    }

    /**
     * @param $limit
     * @param $array
     * @return array|static[]
     * Merge Slider Events
     */
    public function mergeSliderEvents($limit, $array)
    {
        $events = DB::table('events AS e')
            ->join('photos AS p', 'e.id', '=', 'p.imageable_id', 'LEFT')
            ->whereIn('e.id', $array)
            ->take($limit)
            ->groupBy('e.id')
            ->get(array('e.id', 'e.title_ar', 'e.title_en', 'e.description_ar', 'e.description_en', 'p.name', 'e.button_ar', 'e.button_en'));

        return $events;
    }

    public function isEventExpired($id)
    {
        $now   = Carbon::now()->toDateTimeString();
        $query = $this->model->where('start_date', '<', $now)->where('id', '=', $id)->count();
        dd($query);

        return ($query >= 1) ? true : false;
    }

    function suggestedEvents($eventId)
    {
        $current_event            = $this->findById($eventId);
        $current_event_tags       = $this->model->tags->get();
        $current_event_categories = $this->model->categories()->get();
        echo '<pre>';
        print_r($current_event_tags);
        echo '<pre>';
        print_r($current_event_categories);
        exit;
//        $event = $this->get()->where('tag_id' , '=', )
    }


    /**
     * @param $dateStart DateTimeString
     * @return bool
     */
    public function ifEventExpired($dateStart)
    {
        $eventExpired = false;
        $now          = Carbon::now();
        if ( $now->toDateTimeString() > $dateStart ) {
            $eventExpired = true;
        }

        return $eventExpired;
    }

    /**
     * @param $dateStart DateTimeString
     * @param $dateEnd DateTimeString
     * @return bool
     */
    public function ifCanWatchOnline(EventModel $event, User $user)
    {
        $canWatchOnline = false;
        $now            = Carbon::now();
//        $setting           = $event->setting;
//        $registrationTypes = explode(',', $setting->registration_types);
//        $subscription = $event->subscriptions()->where('user_id', $user->id)->where('status', 'CONFIRMED')->first();
//        // check if this event has online streaming
//        if ( !in_array('ONLINE', $registrationTypes) ) {
//            $canWatchOnline = false;
//            return Redirect::action('EventsController@index')->with('error', 'There is no online stream for this event');
//        } elseif( !count($subscription) ) {
//            $canWatchOnline = false;
//            return Redirect::action('EventsController@index')->with('error', 'You are not subscribed to this event, Sorry');
//        }
//        // check whether the user has subscribed as online
//        elseif ( $subscription->registration_type != 'ONLINE' ) {
//            $canWatchOnline = false;
//            return Redirect::action('EventsController@index')->with('error', 'You are not subscribed to this event as ONLINE, Sorry');
//        }

        // If the Current Time is Greater than Event End Date, Do not allow to watch online
        if ( $now > $event->date_end ) {
            $canWatchOnline = false;
        } elseif ( $event->date_start->diffInHours() <= 4 ) {
            // If Date Start is around 4 hours
            $canWatchOnline = true;
        } else {
            $canWatchOnline = false;
        }

        return $canWatchOnline;
    }

    /**
     * @param $id Event Id
     */
    public function getSuggestedEvents($id)
    {
        // get 1 random post for tags
        // get 1 random post for categories
//        $events =
    }
}