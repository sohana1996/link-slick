<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utm_contents extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;
}
