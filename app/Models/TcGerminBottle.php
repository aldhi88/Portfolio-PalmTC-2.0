<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TcGerminBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // relation
    public function tc_bottles(){
        return $this->belongsTo(TcBottle::class,'tc_bottle_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_laminars(){
        return $this->belongsTo(TcLaminar::class,'tc_laminar_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function firstStock($bottleId){
        $return = TcGerminBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcGerminTransaction::where('tc_germin_bottle_id',$bottleId)
            ->orderBy('id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->first_total;
        }
        return $return;
    }
    public static function lastStock($bottleId){
        $return = TcGerminBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcGerminTransaction::where('tc_germin_bottle_id',$bottleId)
            ->orderBy('id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->last_total;
        }
        return $return;
    }
    public static function usedBottle($bottleId){
        $dt = collect(TcGerminObDetail::where('tc_germin_bottle_id',$bottleId)->get()->toArray());
        $minBottleOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        // transfer back
        $minBottleTransfer = 0;
        $q = TcGerminTransferBottle::select('id')->where('tc_germin_bottle_id',$bottleId)
            ->withCount(['tc_germin_transfer_bottle_works as total_transfer' => function($q){
                $q->select(DB::raw('sum(total_work) - sum(back_bottle)'));
            }])->get();
        if(count($q)!=0){
            foreach ($q as $key => $value) {
                $minBottleTransfer += $value->total_transfer;
            }
        }
        $return = $minBottleOb + $minBottleTransfer;
        return $return;
    }

    // mutator
    public function setAlphaAttribute($value){
        $this->attributes['alpha'] = str_replace(' ','',$value);
    }
    public function getAlphaAttribute($value){
        return str_replace(' ','',$value);
    }

}
