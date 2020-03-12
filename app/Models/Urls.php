<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Urls extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;

    public function metaTags(){
        return $this->hasMany('App\Models\UrlMetaTag', 'url_id', 'id');
    }

    public function metaPhotos(){
        return $this->hasMany('App\Models\UrlMetaImage', 'url_id', 'id');
    }
}
