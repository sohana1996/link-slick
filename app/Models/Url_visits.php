<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Url_visits extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;
}
