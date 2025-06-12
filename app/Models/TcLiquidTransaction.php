<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcLiquidTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
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
    public static function storeList($data,$q){
        if($q == 'in'){
            TcLiquidTransaction::create($data);
        }else{
            TcLiquidTransaction::where('tc_liquid_bottle_id',$data['tc_liquid_bottle_id'])
                ->where('tc_liquid_ob_id',$data['tc_liquid_ob_id'])
                ->update([
                    'last_total' => $data['last_total']
                ]);
        }
        if($data['last_total'] == 0){
            TcLiquidBottle::where('id',$data['tc_liquid_bottle_id'])->update(['status' => 0]);
        }
    }
}
