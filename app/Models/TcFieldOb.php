<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcFieldOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_fields(){
        return $this->belongsTo(TcField::class,'tc_field_id');
    }
    public function tc_field_ob_details(){
        return $this->hasMany(TcFieldObDetail::class,'tc_field_ob_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function genObsResult($obsId){
        $q = collect(TcFieldObDetail::where('tc_field_ob_id',$obsId)->get()->toArray());
        $totalDeath = $q->sum('is_death');
        $normal = $q->where('is_normal',1)->count();
        $abnormal = $q->where('is_normal',2)->count();
        $load = $q->sum('load');
        TcFieldOb::where('id',$obsId)->update([
            'total_death' => $totalDeath,
            'normal' => $normal,
            'abnormal' => $abnormal,
            'load' => $load,
        ]);
    }
}
