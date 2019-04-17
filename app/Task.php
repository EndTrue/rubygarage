<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Task extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    // protected $dates = [
    //     'deadline'
    // ];

    public function getDeadlineAttribute($data)
    {
        if ($data != null){
            return Carbon::create($data);
        }        
    }
}
