<?php namespace Acme\Users;

use Acme\Core\CrudableTrait;
use Acme\Users\Validators\UserCreateValidator;
use Acme\Users\Validators\UserResetValidator;
use Acme\Users\Validators\UserUpdateValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Acme\Core\Repositories\Paginable;
use Acme\Core\Repositories\Repository;
use Acme\Core\Repositories\AbstractRepository;
//use Subscription;

class EloquentUserRepository extends AbstractRepository implements Repository, Paginable, UserRepository {

    use CrudableTrait;
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Construct
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @internal param \Illuminate\Database\Eloquent\Model $user
     */
    public function __construct(Model $model)
    {
        parent::__construct(new MessageBag);

        $this->model = $model;
    }

//    public static function isSubscribed($id,$userId) {
//        $query = Subscription::where('user_id', '=', $userId)->where('event_id', '=', $id)->count();
//        return ($query >= 1 ) ? true : false;
//    }

    public function getRoleByName($roleName) {
        $query=  $this->model->with('roles')->whereHas('roles', function($q) use ($roleName)
        {
            $q->where('name', '=', $roleName);

        })->get();
        return $query;
    }

    public function getCreationForm()
    {
        return new UserCreateValidator();
    }

    public function getEditForm($id)
    {
        return new UserUpdateValidator($id);
    }

    public function getPasswordResetForm()
    {
        return new UserResetValidator();
    }

    public function findByToken($token){
        return $this->model->whereConfirmationCode($token)->first();
    }

    public function findByEmail($email) {
        return $this->model->whereEmail($email)->first();
    }

}