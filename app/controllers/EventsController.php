<?php

use Acme\Category\CategoryRepository;
use Acme\Country\CountryRepository;
use Acme\EventModel\EventRepository;
use Acme\Favorite\FavoriteRepository;
use Acme\Follower\FollowerRepository;
use Acme\Subscription\SubscriptionRepository;
use Acme\User\UserRepository;
use Carbon\Carbon;

class EventsController extends BaseController {

    /**
     * @var Acme\EventModel\EventRepository
     */
    protected $eventRepository;
    /**
     * @var Status
     */
    private $status;
    /**
     * @var Acme\User\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var Acme\User\CountryRepository
     */
    private $countryRepository;
    /**
     * @var Acme\User\UserRepository
     */
    private $userRepository;
    /**
     * @var Acme\Subscription\SubscriptionRepository
     */
    private $subscriptionRepository;

    function __construct(EventRepository $eventRepository, CategoryRepository $categoryRepository, CountryRepository $countryRepository, UserRepository $userRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->eventRepository        = $eventRepository;
        $this->categoryRepository     = $categoryRepository;
        $this->countryRepository      = $countryRepository;
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        parent::__construct();
        $this->beforeFilter('auth', array('only' => 'showSubscriptionOptions'));
    }

    public function index()
    {
        $perPage     = 10;
        $this->title = 'Events';
        //find countries,authors,and categories to display in search form
        if ( App::getLocale() == 'en' ) {
            $countries = [0 => trans('site.event.choose_country')] + $this->countryRepository->getAll()->lists('name_en', 'id');
        } else {
            $countries = [0 => trans('site.event.choose_country')] + $this->countryRepository->getAll()->lists('name_ar', 'id');
        }
        $categories = [0 => trans('site.event.choose_category')] + $this->categoryRepository->getEventCategories()->lists('name_'.getLocale(), 'id');
        $authors    = [0 => trans('site.event.choose_author')] + $this->userRepository->getRoleByName('author')->lists('username', 'id');

        // find selected form values
        $search   = trim(Input::get('search'));
        $category = Request::get('category');
        $author   = Request::get('author');
        $country  = Request::get('country');

        // if the form is selected
        // perform search
        if ( !empty($search) || !empty($category) || !empty($author) || !empty($country) ) {
            $events = $this->eventRepository->getAll()
                ->where(function ($query) use ($search, $category, $author, $country) {
                    if ( !empty($search) ) {
                        $query->where('title_ar', 'LIKE', "%$search%")
                            ->orWhere('title_en', 'LIKE', "%$search%");
                        //  ->orWhere('description','LIKE',"%$search%")
                        //  ->orWhere('description_en','LIKE',"%$search%");
                    }
                    if ( !empty($category) ) {
                        $query->where('category_id', $category);
                    }
                    if ( !empty($author) ) {
                        $query->where('user_id', $author);
                    }
                    if ( !empty($country) ) {
                        $locations = $this->countryRepository->findById($country)->locations()->lists('id');
                        $query->whereIn('location_id', $locations);
                    }
                })->orderBy('date_start', 'ASC')->paginate($perPage);

        } else {
            $events = $this->eventRepository->getEvents($perPage);
        }
        $this->render('site.events.index', compact('events', 'authors', 'categories', 'countries', 'search', 'category', 'author', 'country'));
    }


    public function dashboard()
    {
        // $events = parent::all();
        // get only 4 images for slider
        $events = $this->eventRepository->getSliderEvents();
        $this->render('site.home', compact('events'));
    }

    /**
     * Display the event by Id and the regardig comments.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $event = $this->eventRepository->findById($id, ['comments', 'author', 'photos']);

        $tags = $this->eventRepository->findById($id)->tags;

        // returns true false
        $eventStarted = $this->eventRepository->eventStarted($event->date_start);
        $eventExpired = $this->eventRepository->eventExpired($event->date_end);

        if ( Auth::check() ) {
            $user = Auth::user();
            // returns true false

            View::composer('site.events.view', function ($view) use ($id, $user, $event) {
                // return boolean true false
                $favorited      = $event->favorites->contains($user->id);
                $subscribed     = $event->subscribers->contains($user->id);
                $followed       = $event->followers->contains($user->id);
                $canWatchOnline = $this->eventRepository->ongoingEvent($event->date_start,$event->date_end);
                dd($canWatchOnline);

                $view->with(array('favorited' => $favorited, 'subscribed' => $subscribed, 'followed' => $followed, 'canWatchOnline' => $canWatchOnline));
            });
        } else {
            View::composer('site.events.view', function ($view) use ($tags) {
                $view->with(array('favorited' => false, 'subscribed' => false, 'followed' => false, 'canWatchOnline' => 'false'));
            });
        }
        $this->render('site.events.view', compact('event', 'tags', 'eventStarted', 'eventExpired'));

    }

    /**
     * @param $id eventId
     * @return boolean
     * User to Follow an Event
     */
    public function follow($id)
    {
        //check whether user logged in
        $user = Auth::user();
        if ( $user ) {
            //check whether seats are empty
            $event = $this->eventRepository->findById($id);

            if ( !$event->followers->contains($user->id) ) {

                $event->followers()->attach($user);

                return Response::json(array(
                    'success' => true,
                    'message' => trans('site.subscription.followed')
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => trans('site.subscription.not_authenticated')
        ), 403);

    }

    public function unfollow($id)
    {
        //check whether user logged in
        $user = Auth::user();
        if ( !empty($user->id) ) {
            //check whether seats are empty
            $event = $this->eventRepository->findById($id);

            if ( $event->followers->contains($user->id) ) {
                $event->followers()->detach($user);

                return Response::json(array(
                    'success' => true,
                    'message' => trans('site.subscription.unfollowed')
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => trans('site.subscription.not_authenticated')
        ), 403);

    }

    /**
     * @param $id eventId
     * @return boolean
     * User to Follow an Event
     */
    public function favorite($id)
    {
        //check whether user logged in
        $user = Auth::user();
        if ( !empty($user->id) ) {
            //check whether seats are empty
            $event = $this->eventRepository->findById($id);

            if ( !$event->favorites->contains($user->id) ) {

                $event->favorites()->attach($user);

                return Response::json(array(
                    'success' => true,
                    'message' => trans('site.subscription.favorited')
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => trans('site.subscription.not_authenticated')
        ), 403);

    }

    public function unfavorite($id)
    {
        //check whether user logged in
        $user = Auth::user();

        if ( !empty($user->id) ) {
            //check whether seats are empty
            $event = $this->eventRepository->findById($id);

            if ( $event->favorites->contains($user->id) ) {
                $event->favorites()->detach($user);

                return Response::json(array(
                    'success' => true,
                    'message' => trans('site.subscription.unfavorited')
                ), 200);
            }

        }

        return Response::json(array(
            'success' => false,
            'message' => trans('site.subscription.not_authenticated')
        ), 403);

    }

    public function getSliderEvents()
    {
        // fetch 3 latest post
        // fetches 2 featured post
        // order by event date, date created, featured
        // combines them into one query to return for slider
        $latestEvents   = $this->eventRepository->latestEvents();
        $featuredEvents = $this->eventRepository->feautredEvents();
        $events         = array_merge((array) $latestEvents, (array) $featuredEvents);
        if ( $events ) {
            foreach ( $events as $event ) {
                $array[] = $event->id;
            }
            $events_unique = array_unique($array);
            $sliderEvents  = $this->eventRepository->getSliderEvents(6, $events_unique);

            return $sliderEvents;
        } else {
            return null;
        }

    }


    public function getAuthor($id)
    {
        $event  = $this->eventRepository->findById($id);
        $author = $event->author;

        return $author;
    }

    /**
     * show the available registration options page before subscription ( VIP, ONLINE )
     * @param $id
     */
    public function showSubscriptionOptions($id)
    {
        $event = $this->eventRepository->findById($id);

        // initialize values with a false boolean
        $vip    = false;
        $online = false;
        $normal = false;

        // find available registration option types
        $setting = $event->setting;

        if ( is_null($setting) ) {

            // if not setting for the event found, just redirect
            return Redirect::action('EventsController@show', $id)->with('info', trans('site.general.system-error'));
        }

        $reg_types = explode(',', $setting->registration_types);

        // Pass the available options as a boolean
        if ( in_array('VIP', $reg_types) ) $vip = true;
        if ( in_array('ONLINE', $reg_types) ) $online = true;
        if ( in_array('NORMAL', $reg_types) ) $normal = true;

        $this->render('site.events.event-registration-types', compact('event', 'vip', 'online', 'setting', 'normal'));

    }

    public function getSuggestedEvents($id)
    {
        $event = $this->eventRepository->findById($id);

        // initialize arrays
        $suggestedCategoryEvent = [];
        $suggestedTagEvent      = [];
        $events                 = [];

        // find the category Model that is attached to this event
        $category = $this->categoryRepository->findById($event->category_id);

        if ( $category ) {

            // Get Random Events attached to the category
            $categoryEvents = $category->events()->notExpired()->where('events.id', '!=', $id)->take(10)->get(['events.id']);

            if ( count($categoryEvents) ) {
                // fetch one random event
                $categoryEvent = $categoryEvents->random(1);

                // get the Event Model and store it in an arryay
                $suggestedCategoryEvent = $this->eventRepository->findById($categoryEvent->id);
            }
        }

        // Get a Random Tag attached to this Event
        $tags = $event->tags->random(1);

        if ( $tags ) {

            if ( isset($categoryEvent) ) {
                // Get an Event Whose Date is Not Expired and Id not in $id or category event Id ( to avoid duplicate )
                $tagEvents = $tags->events()->notExpired()->where('events.id', '!=', $id)->where('events.id', '!=', $categoryEvent->id)->take(10)->get(['events.id']);

            } else {
                // Get an Event Whose Date is Not Expired and Id not in $id
                $tagEvents = $tags->events()->notExpired()->where('events.id', '!=', $id)->take(10)->get(['events.id']);
            }

            if ( count($tagEvents) ) {
                // fetch one random event
                $tagEvent = $tagEvents->random(1);

                // get the Event Model and store it in an arryay
                $suggestedTagEvent = $this->eventRepository->findById($tagEvent->id);
            }
        }

        // If event fetched from category_id is not empty, add it to the events array
        if ( !empty($suggestedCategoryEvent) )
            $events[] = $suggestedCategoryEvent;

        // If event fetched from tags is not empty, add it to the events array
        if ( !empty($suggestedTagEvent) )
            $events[] = $suggestedTagEvent;

        $this->render('site.events.suggest', compact('events'));


    }

    public function reorganizeEvents($id)
    {
        //check whether user logged in
        $user = Auth::user();

        if ( $user ) {
            //check whether seats are empty
            $event        = $this->eventRepository->findById($id);
            $eventExpired = $this->eventRepository->ifEventExpired($event->date_start);

            if ( !$eventExpired ) {
                if ( !$event->requests->contains($user->id) ) {
                    $event->requests()->attach($user, ['created_at' => Carbon::now()->toDateTimeString()]);
                }

                return Response::json(array(
                    'success' => true,
                    'message' => trans('site.subscription.requested')
                ), 200);
            }

        }

        return null;

    }

    /**
     * Stream event from electa service
     * @param $id
     */
    public function streamEvent($id)
    {
        $user              = Auth::user();
        $event             = $this->eventRepository->findById($id);
        $setting           = $event->setting;
        $registrationTypes = explode(',', $setting->registration_types);


        // if event is currently going on
        if ( ! $this->eventRepository->ongoingEvent($event->date_start, $event->date_end) ) {

            return Redirect::action('EventsController@show', $id)->with('warning', trans('site.general.cannot-watch'));
        }

        // check if this event has online streaming
        if ( !in_array('ONLINE', $registrationTypes) ) {

            return Redirect::action('EventsController@index')->with('error', trans('site.general.no-stream'));
        }

        // check whether this user subscribed for this and confirmed
        $subscription = $event->subscriptions()->where('user_id', $user->id)->first();

        // find if this user has a subscriptoin
        if ( $subscription ) {

            // If user has a subscription and subscription is not confirmed
            if ( $subscription->status != 'CONFIRMED' ) {

                return Redirect::action('EventsController@index')->with('error',trans('site.general.not-confirmed') );
            }

            // check whether the user has subscribed as online
            if ( $subscription->registration_type != 'ONLINE' ) {

                return Redirect::action('EventsController@index')->with('error', trans('site.general.not-online'));
            }

        } else {
            // If user does not have a subscriptoin
            return Redirect::action('EventsController@index')->with('error', trans('site.general.not_subscribed'));
        }



        // stream the event
        if ( ! $this->getStreamSettings() ) {

            return Redirect::action('EventsController@show', $id)->with('info', trans('site.general.system-error'));

        } else {

            list($token, $cid, $launchUrl) = $this->getStreamSettings();

            if ( is_null($token) ) {
                return Redirect::action('EventsController@show', $id)->with('error', trans('site.general.system-error'));
            }

            // Find the user id
            $userTypeId = $event->isAuthor($user->id) ? 1000 : 0;

            // user date to pass to streaming server
            $data = [
                'token'        => urlencode($token),
                'cid'          => $cid,
                'roomid'       => '22352', //todo : change with database room name $setting->online_room_no
                'usertypeid'   => $userTypeId,
                'gender'       => $user->gender,
                'firstname'    => $user->username,
                'lastname'     => $user->name,
                'email'        => $user->email,
                'externalname' => $user->username,
            ];

            // launch the live stream
            $this->launchStream($data, $launchUrl);
        }


    }

    /**
     * @return array token
     * Get Token From the Electa Site
     */
    public function getStreamSettings()
    {

        if ( function_exists('curl_init') ) {

            // get the settings for the live stream
            $cid       = '15829';
            $appKey    = 'WH73FJ63UT62WY76MQ50XX86MI50XQ82';
            $api       = 'http://kaizenlive.e-lectazone.com/apps/token.asp?cid=' . $cid . '&appkey=' . $appKey . '&result=xml';
            $launchUrl = 'http://kaizenlive.e-lectazone.com/apps/launch.asp?';

            $token = null;
            $ch    = curl_init();
            // Set URL to download and other parameters
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            // Download the given URL, and return output
            $output = curl_exec($ch);
            // Close the cURL resource, and free system resources
            curl_close($ch);

            if ( $output ) {

                $tokenRetrive = simplexml_load_string($output);

                foreach ( $tokenRetrive->ResponseData as $data ) {
                    $token = $data;
                }

            }

            return array($token, $cid, $launchUrl);
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @param $launchUrl
     */
    public function launchStream(array $data, $launchUrl)
    {
        try {
            foreach ( $data as $key => $value ) {
                $launchUrl .= $key . '=' . $value . '&';
            }
            rtrim($launchUrl, '&');
            header('Location: ' . $launchUrl);
            die();
        }
        catch (Exception $e) {
            return false;
        }

    }

    public function onlineTestEvent()
    {
        if ( !$this->getStreamSettings() ) {
            return Redirect::action('EventsController@index')->with('error', trans('site.general.system-error'));

        } else {
            list($token, $cid, $launchUrl) = $this->getStreamSettings();

            if ( is_null($token) ) {
                return Redirect::action('EventsController@index')->with('error', trans('site.general.system-error'));
            }

            // user date to pass to streaming server
            $data = [
                'token'        => urlencode($token),
                'cid'          => $cid,
                'roomid'       => '22352', //todo : change with database room name $setting->online_room_no
                'usertypeid'   => '0',
                'gender'       => 'M',
                'firstname'    => 'Test-User',
                'lastname'     => 'Test-User',
                'email'        => 'testuser@test.com',
                'externalname' => 'testuser',
            ];

            // launch the live stream
            $this->launchStream($data, $launchUrl);
        }
    }

}
