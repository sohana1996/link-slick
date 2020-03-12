<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlMetaTag extends Model
{
    protected $fillable = ['url_id', 'type', 'name', 'content'];

    public function urls(){
        return $this->belongsTo('App\Models\Urls', 'url_id', 'id');
    }
   

}
