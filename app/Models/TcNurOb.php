<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcNurOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_nurs(){
        return $this->belongsTo(TcNur::class,'tc_nur_id');
    }
    public function tc_nur_ob_details(){
        return $this->hasMany(TcNurObDetail::class,'tc_nur_ob_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function genObsResult($obsId){
        $q = collect(TcNurObDetail::where('tc_nur_ob_id',$obsId)->get()->toArray());
        $totalTransfer = $q->sum('is_transfer');
        $totalDeath = $q->sum('is_death');
        TcNurOb::where('id',$obsId)->update([
            'total_transfer' => $totalTransfer,
            'total_death' => $totalDeath,
        ]);
    }
}
