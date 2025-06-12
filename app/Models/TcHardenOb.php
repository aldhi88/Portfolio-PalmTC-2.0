<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcHardenOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_hardens(){
        return $this->belongsTo(TcHarden::class,'tc_harden_id');
    }
    public function tc_harden_ob_details(){
        return $this->hasMany(TcHardenObDetail::class,'tc_harden_ob_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function genObsResult($obsId){
        $q = collect(TcHardenObDetail::where('tc_harden_ob_id',$obsId)->get()->toArray());
        $totalTransfer = $q->sum('is_transfer');
        $totalDeath = $q->sum('is_death');
        TcHardenOb::where('id',$obsId)->update([
            'total_transfer' => $totalTransfer,
            'total_death' => $totalDeath,
        ]);
    }
}
