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
    }

    public function index()
    {
        $perPage     = 10;
        $this->title = 'Events';
        //find countries,authors,and categories to display in search form
        if ( App::getLocale() == 'en' ) {
            $countries = [0 => Lang::get('site.event.choose_country')] + $this->countryRepository->getAll()->lists('name_en', 'id');
        } else {
            $countries = [0 => Lang::get('site.event.choose_country')] + $this->countryRepository->getAll()->lists('name_ar', 'id');
        }
        $categories = [0 => Lang::get('site.event.choose_category')] + $this->categoryRepository->getEventCategories()->lists('name_en', 'id');
        $authors    = [0 => Lang::get('site.event.choose_author')] + $this->userRepository->getRoleByName('author')->lists('username', 'id');

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
                        $locations = $this->countryRepository->find($country)->locations()->lists('id');
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
        $event = $this->eventRepository->findById($id, ['comments', 'author', 'photos', 'subscribers', 'followers', 'favorites']);
        // Afdal :: the photoRepository is not implemented within EventsController !!!
        $tags = $this->eventRepository->findById($id)->tags;


        $eventExpired = $this->eventRepository->checkIfEventExpired($event->date_start);

        if ( Auth::check() ) {
            $user = Auth::user();
            View::composer('site.events.view', function ($view) use ($id, $user, $event) {

                // return boolean true false
                $favorited  = $event->favorites->contains($user->id);
                $subscribed = $event->subscriptions->contains($user->id);
                $followed   = $event->followers->contains($user->id);

                $view->with(array('favorited' => $favorited, 'subscribed' => $subscribed, 'followed' => $followed));
            });
        } else {
            View::composer('site.events.view', function ($view) use ($tags) {
                $view->with(array('favorited' => false, 'subscribed' => false, 'followed' => false));
            });
        }

        $this->render('site.events.view', compact('event', 'tags', 'eventExpired'));

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
                    'message' => Lang::get('site.subscription.subscribed', array('attribute' => 'following'))
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => Lang::get('site.subscription.not_authenticated')
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
                    'message' => Lang::get('site.subscription.unsubscribed', array('attribute' => 'unfollowed'))
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => Lang::get('site.subscription.not_authenticated')
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
                    'message' => Lang::get('site.subscription.subscribed', array('attribute' => 'favorited'))
                ), 200);
            }

        }

        // notify user not authenticated
        return Response::json(array(
            'success' => false,
            'message' => Lang::get('site.subscription.not_authenticated')
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
                    'message' => Lang::get('site.subscription.unsubscribed', array('attribute' => 'unfavorited'))
                ), 200);
            }

        }

        return Response::json(array(
            'success' => false,
            'message' => Lang::get('site.subscription.not_authenticated')
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

    public function isTheAuthor($user)
    {
        return $this->author_id === $user->id ? true : false;
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
            return Redirect::action('EventsController@show', $id)->with('warning', 'System Error');
        }

        $reg_types = explode(',', $setting->registration_types);

        // Pass the available options as a boolean
        if ( in_array('VIP', $reg_types) ) $vip = true;
        if ( in_array('ONLINE', $reg_types) ) $online = true;
        if ( in_array('NORMAL', $reg_types) ) $normal = true;

        $this->render('site.events.event-registration-types', compact('event', 'vip', 'online', 'setting', 'normal'));

    }

    /**
     * @param $id
     * Get Suggested Events
     */
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
            $eventExpired = $this->eventRepository->checkIfEventExpired($event->date_start);

            if ( !$eventExpired ) {
                if ( !$event->requests->contains($user->id) ) {
                    $event->requests()->attach($user, ['created_at' => Carbon::now()->toDateTimeString()]);
                }

                return Response::json(array(
                    'success' => true,
                    'message' => Lang::get('site.subscription.requested')
                ), 200);
            }

        }

        return null;
    }

    /**
     * Stream event from electa service
     * @param $id
     */
    public function getEventStream($id)
    {
        $event             = $this->eventRepository->findById($id);
        $setting           = $event->setting;
        $user              = Auth::user();
        $registrationTypes = explode(',', $setting->registration_types);

        // check if this event has online streaming
        if ( !in_array('ONLINE', $registrationTypes) ) {

            return Redirect::action('EventsController@index')->with('error', 'There is no online stream for this event');
        }

        // check whether this user subscribed for this and confirmed
        $subscription = $event->subscriptions()->where('user_id', $user->id)->where('status', 'CONFIRMED')->first();

        if ( !count($subscription) ) {

            return Redirect::action('EventsController@index')->with('error', 'You are not subscribed to this event, Sorry');
        }

        // check whether the user has subscribed as online
        if ( $subscription->registration_type != 'ONLINE' ) {

            return Redirect::action('EventsController@index')->with('error', 'You are not subscribed to this event as ONLINE, Sorry');
        }


        $url = 'http://kaizenlive.e-lectazone.com/apps/token.asp?cid=15829&appkey=WH73FJ63UT62WY76MQ50XX86MI50XQ82&result=xml';

        if ( !function_exists('curl_init') ) {
            // If curl is not installed
            return Redirect::home('303')->with('error', 'Sorry System Error. Please Contact Admin');
        }

        $ch = curl_init();
        // Set URL to download and other parameters
        curl_setopt($ch, CURLOPT_URL, $url);
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
//        $data = [
//            'token' => $token,
//            'cid' =>  '15829' ,
//            'roomid' =>  '22352' ,
//            'usertypeid' =>  '0',
//            'gender' =>  'M' ,
//            'firstname' =>  'Ahmed' ,
//            'lastname' =>  'shalaby' ,
//            'email' =>  'test@gmail.com',
//            'externalname' =>  'unique_id_for_GerorgeEdwrds',
//        ];
//        header('Location: ' . 'http://kaizenlive.e-lectazone.com/apps/launch.asp?'.http_build_query($data));
//        die();

//        return Redirect::to('http://kaizenlive.e-lectazone.com/apps/launch.asp')->with(['roomid' => $setting->online_room_no]);
//        echo '

//';

        $this->render('site.events.online', array('sessionToken' => $token,'event'=>$event));
    }


    public function postEventStream(){

        $posts = Input::all();
        header('Location: ' . 'http://kaizenlive.e-lectazone.com/apps/launch.asp?'.http_build_query($posts));
        exit();
        // works;

//        $cid = '1';
//        $roomId = '1';
//        $usertypeid = '1';
//        $gender = 'M';
//        $firstName = 'zal';
//        $lastName = 'as';
//        $email = 'z4ls@live.com';
//        $externameName = 'asdasd';
//
//        $url = 'http://kaizenlive.e-lectazone.com/apps/launch.asp';
//        $fields = array(
//            'cid' => urlencode($cid),
//            'firstname' => urlencode($firstName),
//            'lastname' => urlencode($lastName),
//            'email' => urlencode($email),
//            'gender' => urlencode($gender),
//            'email' => urlencode($email),
//            'externamname' => urlencode($externameName),
//            'usertypeid' => urlencode($usertypeid),
//            'roomid' => $roomId,
//        );
//
//        //url-ify the data for the POST
//        $fields_string = '';
//        foreach($fields as $key=>$value)
//        {
//            $fields_string .= $key.'='.$value.'&';
//        }
//        rtrim($fields_string, '&');
//
//        //open connection
//        $ch = curl_init();
//
//        //set the url, number of POST vars, POST data
//        curl_setopt($ch,CURLOPT_URL, $url);
//        curl_setopt($ch,CURLOPT_POST, count($fields));
//        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//
//        //execute post
//        $result = curl_exec($ch);
//
//        //close connection
//        curl_close($ch);
//        exit();

    }
}
