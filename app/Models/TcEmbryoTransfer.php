<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoTransfer extends Model
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
        $toEmbryo = $data->to_callus;
        $toGermin = $data->to_solid;
        $toLiquid = $data->to_suspen;
        $transId = $data->id;
        $allowEmbryo = $allowGermin = $allowLiquid = true;

        if($toEmbryo != 0){
            $bottleId = array_column(TcEmbryoBottle::select('id')->where('tc_embryo_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcEmbryoObDetail::whereIn('tc_embryo_bottle_id',$bottleId)->get()->count();
            $allowEmbryo = $q==0?true:false;
        }

        if($toGermin != 0){
            $bottleId = array_column(TcGerminBottle::select('id')->where('tc_embryo_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcGerminObDetail::whereIn('tc_germin_bottle_id',$bottleId)->get()->count();
            $allowGermin = $q==0?true:false;
        }

        if($toLiquid != 0){
            $bottleId = array_column(TcLiquidBottle::select('id')->where('tc_embryo_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcLiquidObDetail::whereIn('tc_liquid_bottle_id',$bottleId)->get()->count();
            $allowLiquid = $q==0?true:false;
        }
        return $allowEmbryo && $allowGermin && $allowLiquid?true:false;
    }
}
