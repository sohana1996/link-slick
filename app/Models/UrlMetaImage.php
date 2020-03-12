<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlMetaImage extends Model
{
    protected $fillable = ['url_id', 'width', 'link'];

    public function urls(){
        return $this->belongsTo('App\Models\Urls', 'url_id', 'id');
    }
}
