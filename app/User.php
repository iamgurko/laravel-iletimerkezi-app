<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {

    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function tokens() {
    return $this->hasMany(SmsOnay::class);
    }

    public function getPhoneNumber() {
        return $this->phone;
    }

}
