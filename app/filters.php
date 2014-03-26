<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    //
});


App::after(function($request, $response)
{
    //
});
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
//    if (Auth::guest()) {
//        Session::put('loginRedirect', Request::url());
//        return Redirect::to('/');
//    }
    if (Auth::guest()) return Redirect::guest('/');
});

Route::filter('auth.basic', function()
{
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('user/login/');
});

/*
|--------------------------------------------------------------------------
| Role Permissions
|--------------------------------------------------------------------------
|
| Access filters based on roles.
|
*/

// Check for role on all admin routes
Entrust::routeNeedsRole( 'admin*', array('admin'));

// Check for permissions on admin actions
//Entrust::routeNeedsPermission( 'admin/blogs*', 'manage_blogs', Redirect::to('/admin') );
//Entrust::routeNeedsPermission( 'admin/comments*', 'manage_comments', Redirect::to('/admin') );
//Entrust::routeNeedsPermission( 'admin/users*', 'manage_users', Redirect::to('/admin') );
//Entrust::routeNeedsPermission( 'admin/roles*', 'manage_roles', Redirect::to('/admin') );
//Entrust::routeNeedsPermission( 'admin/events*', 'manage_roles', Redirect::to('/admin') );

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('Moderator', function()
{
    if (!(Entrust::hasRole('admin') || (Entrust::hasRole('moderator')))) // Checks the current user
    {
        return Redirect::to(LaravelLocalization::localizeURL('/'));
    }
});

Route::filter('Admin', function()
{
    if (!(Entrust::hasRole('admin') )) // Checks the current user
    {
        dd('you do not have permission');
        return Redirect::to(LaravelLocalization::localizeURL('/'));
    }
});


Route::filter('csrf', function()
{
    if (Session::getToken() != Input::get('csrf_token') &&  Session::getToken() != Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

Route::filter('owner', function($route, $request)
{
    if(Auth::check())
        if( $request->segment(3) != Auth::user()->id)
        {
            return Redirect::action('EventsController@dashboard')->with('error','You are not supposed to do that');
        } else {
            return ;
        }
    return Redirect::action('UserController@getLogin')->with('error','Please login');
});

