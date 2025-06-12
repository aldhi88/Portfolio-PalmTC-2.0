<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcGerminObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_germin_obs(){
        return $this->belongsTo(TcGerminOb::class,'tc_germin_ob_id');
    }
    public function tc_germin_bottles(){
        return $this->belongsTo(TcGerminBottle::class,'tc_germin_bottle_id');
    }

    // process
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcGerminBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcGerminObDetail::where('tc_init_id',$initId)
            ->where('tc_germin_bottle_id',$bottleId)
            ->where('tc_germin_ob_id','<',$obsId)
            ->orderBy('tc_germin_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;

        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }

        $q = TcGerminTransferBottle::select('id')->where('tc_germin_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcGerminTransferBottleWork::whereIn('tc_germin_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }

    public static function lastTotal($initId,$obsId,$bottleId){
        $stokAwal = TcGerminBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcGerminObDetail::where('tc_init_id',$initId)
            ->where('tc_germin_bottle_id',$bottleId)
            ->where('tc_germin_ob_id','<=',$obsId)
            ->orderBy('tc_germin_ob_id','desc')
            ->get();
        $usedOb = $usedTransfer = 0;
        if(count($q)!=0){
            $dt = collect($q->toArray());
            $usedOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        }
        $q = TcGerminTransferBottle::select('id')->where('tc_germin_bottle_id',$bottleId)->get()->toArray();
        $idTransBottle = array_column($q,'id');
        $q = TcGerminTransferBottleWork::whereIn('tc_germin_transfer_bottle_id',$idTransBottle)->get()->toArray();
        $dt = collect($q);
        $usedTransfer = $dt->sum('total_work') - $dt->sum('back_bottle');

        $return = $stokAwal - $usedOb - $usedTransfer;
        return $return;
    }
}
