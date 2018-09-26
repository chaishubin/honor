<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Model;

class SignUpInfoReview extends Model
{
    protected $table = 'signup_info_review';

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager\ManagerModel','user_id');
    }
}
