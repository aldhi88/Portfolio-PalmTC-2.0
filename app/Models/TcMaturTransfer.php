<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMaturTransfer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_laminars(){
        return $this->belongsTo(TcLaminar::class,'tc_laminar_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function delAllow($data)
    {
        $toBack = $data->to_self;
        $toNext = $data->to_germin;
        $transId = $data->id;
        $allowBack = $allowNext = true;

        if($toBack != 0){
            $bottleId = array_column(TcMaturBottle::select('id')->where('tc_matur_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcMaturObDetail::whereIn('tc_matur_bottle_id',$bottleId)->get()->count();
            $allowBack = $q==0?true:false;
        }

        if($toNext != 0){
            $bottleId = array_column(TcGerminBottle::select('id')->where('tc_matur_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcGerminObDetail::whereIn('tc_germin_bottle_id',$bottleId)->get()->count();
            $allowNext = $q==0?true:false;
        }
        return $allowBack && $allowNext?true:false;
    }
}
