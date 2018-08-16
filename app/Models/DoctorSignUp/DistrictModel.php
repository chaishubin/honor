<?php

namespace App\Models\DoctorSignUp;

use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
    protected $table = 'district';
    protected $primaryKey = 'id';
    public $incrementing = false;
}
