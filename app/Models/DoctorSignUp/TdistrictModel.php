<?php

namespace App\Models\DoctorSignUp;

use Illuminate\Database\Eloquent\Model;

class TdistrictModel extends Model
{
    protected $table = 'tdistrict';
    protected $primaryKey = 'district_id';
    public $incrementing = false;
}
