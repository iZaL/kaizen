<?php

use Acme\Core\LocaleTrait;

class Category extends BaseModel {

    use LocaleTrait;

    protected $guarded = array();

    protected $table = "categories";

    protected static $name = "category";

    protected $localeStrings = ['name'];

}
