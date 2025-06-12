<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingTransfer extends Model
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

    public static function delAllow($data)
    {
        $toRoot1 = $data->to_root1_bottle;
        $toRoot2 = $data->to_root2;
        $toAclim = $data->to_aclim;
        $transId = $data->id;
        $allowRoot1 = $allowRoot2 = true;

        if($toRoot1 != 0){
            $bottleId = array_column(TcRootingBottle::select('id')->where('tc_rooting_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcRootingObDetail::whereIn('tc_rooting_bottle_id',$bottleId)->get()->count();
            $allowRoot1 = $q==0?true:false;
        }

        if($toRoot2 != 0){
            $bottleId = array_column(TcRootingBottle::select('id')->where('tc_rooting_transfer_id',$transId)->get()->toArray(),'id');
            $q = TcRootingObDetail::whereIn('tc_rooting_bottle_id',$bottleId)->get()->count();
            $allowRoot2 = $q==0?true:false;
        }

        // if($toAclim != 0){
        //     $bottleId = array_column(TcAclimBottle::select('id')->where('tc_rooting_transfer_id',$transId)->get()->toArray(),'id');
        //     $q = TcAclimObDetail::whereIn('tc_rooting_bottle_id',$bottleId)->get()->count();
        //     $allowBack = $q==0?true:false;
        // } pending!!
        return $allowRoot1 && $allowRoot2?true:false;
    }

}
