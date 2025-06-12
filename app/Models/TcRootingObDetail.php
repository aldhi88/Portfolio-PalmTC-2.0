<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_rooting_obs(){
        return $this->belongsTo(TcRootingOb::class,'tc_rooting_ob_id');
    }
    public function tc_rooting_bottles(){
        return $this->belongsTo(TcRootingBottle::class,'tc_rooting_bottle_id');
    }

    // process
    public static function firstTotalLeaf($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;

        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
        }

        $q = TcRootingTransferBottle::select('id')->where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcRootingTransferBottleWork::whereIn('tc_rooting_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('leaf_work') - $dt->sum('back_leaf');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;

        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }

        $q = TcRootingTransferBottle::select('id')->where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcRootingTransferBottleWork::whereIn('tc_rooting_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
    public static function lastTotal($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<=',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }
        $q = TcRootingTransferBottle::select('id')->where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcRootingTransferBottleWork::whereIn('tc_rooting_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
    public static function lastTotalLeaf($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<=',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
        }
        $q = TcRootingTransferBottle::select('id')->where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcRootingTransferBottleWork::whereIn('tc_rooting_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('leaf_work') - $dt->sum('back_leaf');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
    public static function lastTotalLeafObs($initId,$obsId,$bottleId){

        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<=',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
        }
        $q = TcRootingTransferBottle::select('id')->where('tc_rooting_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcRootingTransferBottleWork::whereIn('tc_rooting_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('leaf_work') - $dt->sum('back_leaf');

        $return = $stokAwal - $usedOb - $usedTransfer;
        // dump($obsId, $stokAwal, $usedOb, $usedTransfer);
        return $return;
    }
}
