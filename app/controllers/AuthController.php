<?php

use Acme\Users\AuthService;

class AuthController extends BaseController {

    /**
     * @var AuthRepository
     */
    private $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    public function getLogin()
    {
        if ( Auth::check() ) {

            return Redirect::home()->with('info', 'You already logged in');
        }

        $this->title = 'Login to your Account';
        $this->render('site.auth.login');
    }

    public function postLogin()
    {
        $email    = Input::get('email');
        $password = Input::get('password');
        $remember = Input::has('remember') ? true : false;

        if ( Auth::attempt(array('email' => $email, 'password' => $password), $remember) ) {

            $this->service->updateLastLoggedAt();

            return Redirect::intended('/');
        } else {

            return Redirect::action('AuthController@getLogin')->with('errors', Lang::get('site.auth.alerts.wrong_credentials'));
        }
    }


    /**
     * User Registeration Page
     */
    public function getRegister()
    {
        $this->title = 'Create an Account';
        $this->render('site.auth.signup');
        // $this->postRegister();
    }


    public function postRegister()
    {
        // allow only these inputs
        $data = Input::only(
            'email', 'password', 'password_confirmation', 'name_ar', 'name_en', 'username', 'phone'
        );

        $val = $this->service->validators['create']->with($data);

        if ( $val->passes() ) {

            // If Validation Passes
            if ( $this->service->register($data) ) {

                return Redirect::action('AuthController@getLogin')->with('success', 'Email confirmation link has been sent to your email. PLease confirm your account');
            } else {

                return Redirect::route('cars.index')->with('errors', $this->service->errors());
            }

        } else {

            // If validation Fails
            return Redirect::back()->with('errors', $val->errors())->withInput($data);
        }
    }


    /**
     * Display The  Forgot Password Form
     * @return Response
     */
    public function getForgot()
    {
        $this->title = 'Reset Password';
        $this->render('site.auth.forgot');
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return Response
     */
    public function postForgot()
    {
        switch ( $response = Password::remind(Input::only('email')) ) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));

            case Password::REMINDER_SENT:
                return Redirect::back()->with('success', Lang::get($response));
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     * @return Response
     */
    public function getReset($token = null)
    {
        if ( is_null($token) ) App::abort(404);

        $this->render('site.auth.reset', array('token' => $token));
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset()
    {
        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->service->resetPassword($credentials);

        switch ( $response ) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));

            case Password::PASSWORD_RESET:
                return Redirect::action('AuthController@getLogin')->with('success', 'Your Password Has been Reset');
        }
    }

    /**
     * @param $token
     * Confirm the User and Activate
     * Lands on this page When User Clicks the Activation Link in Email
     */
    public function activate($token)
    {
        if ( $this->service->activateUser($token) ) {
            // redirec to home with active message
            dd('mail sent');
        } else {
            // errors
            dd($this->service->errors());
        }
    }

    /**
     * Logout a User
     */
    public function getLogout()
    {
        Auth::logout();
        return Redirect::home();
    }

    public function getProfile($id)
    {

        $this->title = 'Profile';
        $user        = User::find($id);
        $this->render('site.users.profile', compact('user'));
    }

}