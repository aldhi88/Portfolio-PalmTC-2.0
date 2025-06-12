<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcCallusTransfer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function tc_callus_obs(){
        return $this->belongsTo(TcCallusOb::class,'tc_callus_ob_id','id');
    }
    public function tc_callus_transfer_bottles(){
        return $this->hasMany('App\Models\TcCallusTransferBottle','tc_callus_transfer_id','id');
    }
    public function tc_workers(){
        return $this->belongsTo('App\Models\TcWorker','tc_worker_id','id');
    }
    public function tc_laminars(){
        return $this->belongsTo('App\Models\TcLaminar','tc_laminar_id','id');
    }
    public function tc_medium_stocks(){
        return $this->belongsTo(TcMediumStock::class,'tc_medium_stock_id','id');
    }

    public static function getBottleNo($obsId){
        $qStartNo = TcCallusTransfer::select('id')
            ->where('tc_observation_id','<',$obsId)
            ->get()
            ->count();
        $data['start_no'] = $qStartNo + 1;

        $qEndNo = TcCallusTransfer::select('id')
            ->where('tc_observation_id','=',$obsId)
            ->get()
            ->count();
        $data['end_no'] = $qStartNo + $qEndNo;
        
    }
}
