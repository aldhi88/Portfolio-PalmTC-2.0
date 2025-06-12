<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_embryo_bottles(){
        return $this->belongsTo(TcEmbryoBottle::class,'tc_embryo_bottle_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_embryo_obs(){
        return $this->belongsTo(TcEmbryoOb::class,'tc_embryo_ob_id');
    }

    // process
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcEmbryoBottle::where('id',$bottleId)->first()->getAttribute('number_of_bottle');
        $q = TcEmbryoObDetail::where('tc_init_id',$initId)
            ->where('tc_embryo_bottle_id',$bottleId)
            ->where('tc_embryo_ob_id','<',$obsId)
            ->orderBy('tc_embryo_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;

        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }

        $q = TcEmbryoTransferBottle::select('id')->where('tc_embryo_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcEmbryoTransferBottleWork::whereIn('tc_embryo_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }

    public static function lastTotal($initId,$obsId,$bottleId){
        $stokAwal = TcEmbryoBottle::where('id',$bottleId)->first()->getAttribute('number_of_bottle');
        $q = TcEmbryoObDetail::where('tc_init_id',$initId)
            ->where('tc_embryo_bottle_id',$bottleId)
            ->where('tc_embryo_ob_id','<=',$obsId)
            ->orderBy('tc_embryo_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }
        $q = TcEmbryoTransferBottle::select('id')->where('tc_embryo_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcEmbryoTransferBottleWork::whereIn('tc_embryo_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
}
