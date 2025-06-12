<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcObservation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function getTypeDesc($type){
        $data = [
            0 => "Callus Observation",
        ];

        return $type;
    }

    public function tc_inits(){
        return $this->belongsTo('App\Models\TcInit', 'tc_init_id','id');
    }

    public function tc_obs_details(){
        return $this->hasMany('App\Models\TcObsDetail','tc_observation_id','id');
    }

    public static function checkRemider($initId){
        $reminder = false;
        $q = TcObservation::where('tc_init_id',$initId)
            ->where('status',0)
            ->get();
        $dateSchedule = Carbon::parse($q->first()->date_schedule);
        $dateRemaider = Carbon::parse($dateSchedule)->subDay(7);
        $diffDays = $dateSchedule->diff($dateRemaider)->days;
        $now = Carbon::now();
        $diff = $dateRemaider->diff($now)->days;
        if($diff <= $diffDays){
            $reminder = true;
        }
        return $reminder;
    }


}
