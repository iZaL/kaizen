<?php namespace Acme\Core\ServiceProviders;

use Acme\Blog\BlogEventSubscriber;
use Acme\Blog\EloquentBlogRepository;
use Acme\Contact\ContactEventSubscriber;
use Acme\Contact\EloquentContactRepository;
use Acme\Country\EloquentCountryRepository;
use Acme\Events\EloquentEventRepository;
use Acme\Category\EloquentCategoryRepository;
use Category;
use Contact;
use Country;
use EventModel;
use Illuminate\Support\MessageBag;
use Acme\Users\AuthService;
use Acme\Users\UserEventSubscriber;
use Illuminate\Support\ServiceProvider;
use Acme\Users\EloquentUserRepository;
use Post;
use User;

class RepositoryServiceProvider extends ServiceProvider {

    /**
     * Register
     */
    public function boot()
    {
        $this->app['events']->subscribe(new UserEventSubscriber($this->app['mailer']));
        $this->app['events']->subscribe(new ContactEventSubscriber($this->app['mailer']));
        $this->app['events']->subscribe(new BlogEventSubscriber($this->app['mailer']));
    }

    public function register()
    {
        $this->registerUserRepository();
        $this->registerAuthService();
        $this->registerEventRepository();
        $this->registerCountryRepository();
        $this->registerCategoryRepository();
        $this->registerContactRepository();
        $this->registerBlogRepository();
    }

    /**
     * Register User Repository
     */
    private function registerUserRepository()
    {
        $this->app->bind('Acme\Users\UserRepository', function () {
            $user = new EloquentUserRepository(new User);

            return $user;
        });

    }

    private function registerAuthService()
    {
        $this->app->bind('Acme\Users\AuthService', function ($app) {
            $user = new AuthService($app->make('Acme\Users\UserRepository'), new MessageBag);

            return $user;
        });
    }

    private function registerEventRepository()
    {
        $this->app->bind('Acme\Events\EventRepository', function () {
            $country = new EloquentEventRepository(new EventModel());

            return $country;
        });
    }

    private function registerCountryRepository()
    {
        $this->app->bind('Acme\Country\CountryRepository', function () {
            $country = new EloquentCountryRepository(new Country());

            return $country;
        });
    }

    private function registerCategoryRepository()
    {
        $this->app->bind('Acme\Category\CategoryRepository', function () {
            $category = new EloquentCategoryRepository(new Category());

            return $category;
        });
    }

    private function registerContactRepository()
    {
        $this->app->bind('Acme\Contact\ContactRepository', function () {
            $contact = new EloquentContactRepository(new Contact());

            return $contact;
        });
    }

    private function registerBlogRepository()
    {
        $this->app->bind('Acme\Blog\BlogRepository', function () {
            $contact = new EloquentBlogRepository(new Post());

            return $contact;
        });
    }

}
