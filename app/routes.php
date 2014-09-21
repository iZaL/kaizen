<?php

//Route::model('role', 'Role');

/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------ */
Route::pattern('id', '[0-9]+');

//Route::pattern('role', '[0-9]+');

Route::pattern('token', '[0-9a-z]+');

/*********************************************************************************************************
 * Event Routes
 ********************************************************************************************************/
Route::get('event/{id}/online', 'EventsController@streamEvent');

Route::get('event/{id}/offline', 'EventsController@streamEventOld');

Route::get('event/{id}/category', 'EventsController@getCategory');

Route::get('event/{id}/author', 'EventsController@getAuthor');

Route::get('event/{id}/follow', array('as' => 'event.follow', 'uses' => 'EventsController@follow'));

Route::get('event/{id}/unfollow', array('as' => 'event.unfollow', 'uses' => 'EventsController@unfollow'));

Route::get('event/{id}/favorite', array('as' => 'event.favorite', 'uses' => 'EventsController@favorite'));

Route::get('event/{id}/unfavorite', array('as' => 'event.unfavorite', 'uses' => 'EventsController@unfavorite'));

Route::get('events/featured', array('as' => 'event.featured', 'uses' => 'EventsController@getSliderEvents'));

Route::get('event/{id}/country', 'EventsController@getCountry');

Route::get('event/{id}/options', 'EventsController@showSubscriptionOptions');

Route::get('event/{id}/suggest', 'EventsController@getSuggestedEvents');

Route::post('event/{id}/organize', 'EventsController@reorganizeEvents');

Route::get('event/{id}/organize', 'EventsController@reorganizeEvents');

Route::resource('event.comments', 'CommentsController', array('only' => array('store')));

Route::resource('event', 'EventsController', array('only' => array('index', 'show')));

/*********************************************************************************************************
 * Contact Us Route
 ********************************************************************************************************/

Route::resource('contact', 'ContactsController', array('only' => array('index')));

Route::post('contact/contact', 'ContactsController@contact');

/*********************************************************************************************************
 * Posts
 ********************************************************************************************************/

Route::get('consultancy', array('as' => 'consultancy', 'uses' => 'BlogsController@consultancy'));

Route::resource('blog', 'BlogsController', array('only' => array('index', 'show', 'view')));

/*********************************************************************************************************
 * Tags
 ********************************************************************************************************/
Route::resource('tag', 'TagsController', array('only' => array('show')));

// Post Comment

/*********************************************************************************************************
 * Auth Routes
 ********************************************************************************************************/
Route::get('account/login', ['as' => 'user.login.get', 'uses' => 'AuthController@getLogin']);

Route::post('account/login', ['as' => 'user.login.post', 'uses' => 'AuthController@postLogin']);

Route::get('account/logout', ['as' => 'user.logout', 'uses' => 'AuthController@getLogout']);

Route::get('account/signup', ['as' => 'user.register.get', 'uses' => 'AuthController@getSignup']);

Route::post('account/signup', ['as' => 'user.register.post', 'uses' => 'AuthController@postSignup']);

Route::get('account/forgot', ['as' => 'user.forgot.get', 'uses' => 'AuthController@getForgot']);

Route::post('account/forgot', ['as' => 'user.forgot.post', 'uses' => 'AuthController@postForgot']);

Route::get('password/reset/{token}', ['as' => 'user.token.get', 'uses' => 'AuthController@getReset']);

Route::post('password/reset', ['as' => 'user.token.post', 'uses' => 'AuthController@postReset']);

Route::get('account/activate/{token}', ['as' => 'user.token.confirm', 'uses' => 'AuthController@activate']);


/*********************************************************************************************************
 * User Routes
 ********************************************************************************************************/

Route::get('user/{id}/profile', array('as' => 'profile', 'uses' => 'UserController@getProfile'));

Route::resource('user', 'UserController');

/*********************************************************************************************************
 * Category Routes
 ********************************************************************************************************/
Route::get('category/{id}/events', array('as' => 'CategoryEvents', 'uses' => 'CategoriesController@getEvents'));

Route::get('category/{id}/posts', array('as' => 'CategoryPosts', 'uses' => 'CategoriesController@getPosts'));

/*********************************************************************************************************
 * Country Routes
 ********************************************************************************************************/
Route::get('country/{id}/events', array('uses' => 'CountriesController@getEvents'));

/*********************************************************************************************************
 * Newsletter Routes
 ********************************************************************************************************/
Route::post('newsletter', 'NewslettersController@store');

Route::get('newsletter', 'NewslettersController@index');

/*********************************************************************************************************
 * MISC ROUTES
 ********************************************************************************************************/
Route::get('forbidden', function () {
    return View::make('error.forbidden');
});

//push queue worker
Route::post('queue/mails', function () {
    return Queue::marshal();
});


Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));

/*********************************************************************************************************
 * Admin Routes
 ********************************************************************************************************/
Route::group(array('prefix' => 'admin', 'before' => array('Auth', 'Moderator')), function () {

    /*********************************************************************************************************
     * Admin Comments Routes
     ********************************************************************************************************/
    Route::resource('comments', 'AdminCommentsController');

    /*********************************************************************************************************
     * Admin Blog Management Routes
     ********************************************************************************************************/
    Route::get('blogs/{id}/delete', 'AdminBlogsController@getDelete');

    Route::get('blogs/data', 'AdminBlogsController@getData');

    Route::resource('blogs', 'AdminBlogsController');

    /*********************************************************************************************************
     * User Management Routes
     ********************************************************************************************************/
    Route::get('users/{user}/show', array('uses' => 'AdminUsersController@getShow'));

//    Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
//
//    Route::post('users/{user}/edit', 'AdminUsersController@postEdit');

    Route::get('users/{user}/delete', 'AdminUsersController@getDelete');

    Route::post('users/{user}/delete', 'AdminUsersController@postDelete');

    Route::get('users/{id}/report', 'AdminUsersController@getReport');

    Route::post('users/{id}/report', 'AdminUsersController@postReport');

    Route::resource('users', 'AdminUsersController');

    /*********************************************************************************************************
     * Admin User Role Management Routes
     ********************************************************************************************************/

    Route::resource('roles', 'AdminRolesController');

    /*********************************************************************************************************
     * Admin Events Routes
     ********************************************************************************************************/
    Route::get('event/{id}/followers', 'AdminEventsController@getFollowers');

    Route::get('event/{id}/favorites', 'AdminEventsController@getFavorites');

    Route::get('event/{id}/subscriptions', 'AdminEventsController@getSubscriptions');

    Route::get('event/{id}/country', 'AdminEventsController@getCountry');

    Route::get('event/{id}/location', 'AdminEventsController@getLocation');

    Route::post('event/{id}/mailFollowers', 'AdminEventsController@mailFollowers');

    Route::post('event/{id}/mailSubscribers', 'AdminEventsController@mailSubscribers');

    Route::post('event/{id}/mailFavorites', 'AdminEventsController@mailFavorites');

    Route::get('event/{id}/location', 'AdminEventsController@getLocation');

    Route::get('event/{id}/settings', 'AdminEventsController@getSettings');

    Route::get('event/{id}/details', 'AdminEventsController@getDetails');


    Route::get('event/type/create', 'AdminEventsController@selectType');

    Route::post('photo/create', 'AdminEventsController@storeImage');

    Route::resource('event', 'AdminEventsController');

    /*********************************************************************************************************
     * Package routes
     ********************************************************************************************************/
    Route::get('package/{id}/settings', 'AdminPackagesController@settings');

    Route::resource('package', 'AdminPackagesController');

    /*********************************************************************************************************
     * Event Settings Routes
     ********************************************************************************************************/

    Route::get('setting/{id}/add-online-room', 'AdminSettingsController@getAddRoom');

    Route::post('setting/{id}/add-online-room', 'AdminSettingsController@postAddRoom');

    Route::resource('settings', 'AdminSettingsController');

    /*********************************************************************************************************
     * Category Routes
     ********************************************************************************************************/
    Route::resource('category', 'AdminCategoriesController');

    /*********************************************************************************************************
     * Country Routes
     ********************************************************************************************************/
    Route::resource('country', 'AdminCountriesController');

    /*********************************************************************************************************
     * Location Routes
     ********************************************************************************************************/
    Route::get('location/{id}/events', array('as' => 'LocationEvents', 'uses' => 'AdminLocationsController@getEvents'));

    Route::resource('locations', 'AdminLocationsController');

    /*********************************************************************************************************
     * Ads Route
     ********************************************************************************************************/
    Route::resource('ads', 'AdminAdsController', array('only' => array('index', 'store')));

    /*********************************************************************************************************
     * Contact US Routes
     ********************************************************************************************************/
    Route::resource('contact-us', 'AdminContactsController', array('only' => array('index', 'store')));

    /*********************************************************************************************************
     * Photo Routes
     ********************************************************************************************************/
    Route::resource('photo', 'AdminPhotosController');

    /*********************************************************************************************************
     * Event Requests Route
     ********************************************************************************************************/
    Route::resource('subscription', 'AdminSubscriptionsController');

    Route::get('event/{id}/requests', array('uses' => 'AdminEventsController@getRequests'));

    /*********************************************************************************************************
     * Event Type Routes
     ********************************************************************************************************/
    Route::resource('type', 'AdminTypesController');

    /*********************************************************************************************************
     * Admin Dashboard
     ********************************************************************************************************/
    Route::get('/', 'AdminEventsController@index');

});

Route::get('package', 'SubscriptionsController@subscribePackage');
Route::get('types', 'SubscriptionsController@subscribeTypes');
Route::post('subscribe', 'SubscriptionsController@subscribe');
Route::get('subscribe', 'SubscriptionsController@subscribe');
Route::get('event/{id}/unsubscribe', 'SubscriptionsController@unsubscribe');

Route::get('test', function () {
    $event = EventModel::find(1);
    $date_start = $event->date_start;

    $data_end = $event->date_end;
//    dd($date_start->toDateString());
    echo 'Event Start Date: '.$date_start;
    echo '<br>';
    echo 'Event End Date: '.$data_end;
    echo '<br>';
    $now = \Carbon\Carbon::now();
    echo 'Now :' . $now;
    echo '<br>';
//    dd($now->toDateString());
    dd($date_start->diffInHours());

    dd($date_start->diffInHours());
    if($now->toDateString() == $date_start->toDateString()) {
        dd('rqual');
    }

    echo 'Now: '.$now;
    echo '<br>';

    // Find if Event Expired
    // Check if current time is greater than start_date [Do not allow subscription, unsubscription]

    // make a user to watch event online
    // Check if current time is greater than start_date and less than end_date

    // make a user subscribe to event
    // check if current time is less than start_date

    if ($now > $date_start) {
        // do not alow subscriptions, unsubscriptions
        echo 'Event Expired<br>';
    } elseif($now > $date_start && $now < $data_end) {
        echo 'Event Not Expired';
    } else {

    }
});