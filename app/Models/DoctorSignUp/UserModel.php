<?php

namespace App\Models\DoctorSignUp;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'user';

    public function signUpInfo()
    {
        return $this->hasMany('App\Models\DoctorSignUp\DoctorModel','phone_number','phone_number');
    }
}
