<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TcEmbryoBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_embryo_ob_details(){
        return $this->hasMany(TcEmbryoObDetail::class,'tc_embryo_bottle_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id','id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id','id');
    }
    public function tc_embryo_bottle_subtractions(){
        return $this->hasMany(TcEmbryoBottleSubtraction::class,'tc_embryo_bottle_id','id');
    }

    //process
    public static function firstStock($bottleId){
        $return = TcEmbryoBottle::where('id',$bottleId)->first()->getAttribute('number_of_bottle');
        $q = TcEmbryoList::where('tc_embryo_bottle_id',$bottleId)->orderBy('id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->first_total;
        }
        return $return;
    }
    public static function lastStock($bottleId){
        $return = TcEmbryoBottle::where('id',$bottleId)->first()->getAttribute('number_of_bottle');
        $q = TcEmbryoList::where('tc_embryo_bottle_id',$bottleId)->orderBy('id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->last_total;
        }
        return $return;
    }
    public static function usedBottle($bottleId){
        $dt = collect(TcEmbryoObDetail::where('tc_embryo_bottle_id',$bottleId)->get()->toArray());
        $minBottleOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        // transfer back
        $minBottleTransfer = 0;
        // $q = TcEmbryoTransferBottle::where('tc_embryo_bottle_id',$bottleId)->get();
        $q = TcEmbryoTransferBottle::select('id')->where('tc_embryo_bottle_id',$bottleId)
            ->withCount(['tc_embryo_transfer_bottle_works as total_transfer' => function($q){
                $q->select(DB::raw('sum(total_work) - sum(back_bottle)'));
            }])->get();
        if(count($q)!=0){
            foreach ($q as $key => $value) {
                $minBottleTransfer += $value->total_transfer;
            }
        }

        $return = $minBottleOb + $minBottleTransfer;
        // dd($minBottleOb);
        return $return;
    }
    public static function usedBottleByInit($initId){
        $dt = collect(TcEmbryoObDetail::where('tc_init_id',$initId)->get()->toArray());
        // transfer back
        $bottleMinus = 0;
        $q = TcEmbryoTransferBottle::select('id')
            ->where('bottle_left','>',0)
            ->where('tc_init_id',$initId)
            ->get()->toArray();
        if(count($q) != 0){
            foreach ($q as $key => $value) {
                $aryId[] = $value['id'];
            }
            $q = TcEmbryoTransferBottleWork::select('back_bottle','total_work')
                ->whereIn('tc_embryo_transfer_bottle_id',$aryId)
                ->get()->toArray();
            $totalWork = (collect($q)->sum('total_work'));
            $backBottle = (collect($q)->sum('back_bottle'));
            $bottleMinus = $totalWork - $backBottle;
        }

        $return = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other') + $bottleMinus;
        return $return;
    }

}
