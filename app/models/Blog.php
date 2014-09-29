<?php

use Acme\Blog\BlogPresenter;
use Acme\Core\LocaleTrait;
use McCool\LaravelAutoPresenter\PresenterInterface;

class Blog extends BaseModel implements PresenterInterface {

    use LocaleTrait;
    protected $guarded = array();

    protected $table = "posts";

    protected $localeStrings = ['title', 'description'];

    protected static $name = 'post';
	/**
	 * Deletes a blog post and all
	 * the associated comments.
	 *
	 * @return bool
	 */
	public function delete()
	{
		// Delete the comments
		$this->comments()->delete();

		// Delete the blog post
		return parent::delete();
	}

	/**
	 * Get the post's author.
	 *
	 * @return User
	 */
    public function author() {
        return $this->belongsTo('User','user_id')->select('id','username','email');
    }

	/**
	 * Get the post's comments.
	 *
	 * @return array
	 */
	public function comments()
	{
		//return $this->hasMany('Comment');
        return $this->morphMany('Comment','commentable');
	}


    public function getPresenter()
    {
        return 'Acme\Blog\Presenter';
    }

    public function latest($count) {
        return $this->orderBy('created_at', 'DESC')->remember(10)->limit($count)->get();
    }

    public function category() {
//        return $this->morphMany('Category','categorizable','categorizable_type');
        return $this->belongsTo('Category','category_id');
    }
    public function photos() {
        return $this->morphMany('Photo','imageable');
    }

    public function tags()
    {
        return $this->morphToMany('Tag', 'taggable');
    }

    public function categories()
    {
        return $this->hasMany('Category', 'category_id')->where('');
    }
}
