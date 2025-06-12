<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcLiquidObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_liquid_obs(){
        return $this->belongsTo(TcLiquidOb::class,'tc_liquid_ob_id');
    }
    public function tc_liquid_bottles(){
        return $this->belongsTo(TcLiquidBottle::class,'tc_liquid_bottle_id');
    }

    // process
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcLiquidBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcLiquidObDetail::where('tc_init_id',$initId)
            ->where('tc_liquid_bottle_id',$bottleId)
            ->where('tc_liquid_ob_id','<',$obsId)
            ->orderBy('tc_liquid_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;

        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }

        $q = TcLiquidTransferBottle::select('id')->where('tc_liquid_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcLiquidTransferBottleWork::whereIn('tc_liquid_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }

    public static function lastTotal($initId,$obsId,$bottleId){
        $stokAwal = TcLiquidBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcLiquidObDetail::where('tc_init_id',$initId)
            ->where('tc_liquid_bottle_id',$bottleId)
            ->where('tc_liquid_ob_id','<=',$obsId)
            ->orderBy('tc_liquid_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }
        $q = TcLiquidTransferBottle::select('id')->where('tc_liquid_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcLiquidTransferBottleWork::whereIn('tc_liquid_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
}
