<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    protected $table = 'setting';

    public static function getTimeLimit()
    {
        $res = self::where('name','time_limit')->first();
        if ($res){
            return $res->toArray();
        }
        return '';
    }
}
