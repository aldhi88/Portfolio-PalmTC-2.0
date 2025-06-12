<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingTransaction extends Model
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
    public function tc_rooting_obs(){
        return $this->belongsTo(TcRootingOb::class,'tc_rooting_ob_id');
    }
    public function tc_rooting_bottles(){
        return $this->belongsTo(TcRootingBottle::class,'tc_rooting_bottle_id');
    }

    // process
    public static function storeList($data,$q){
        if($q == 'in'){
            TcRootingTransaction::create($data);
        }else{
            TcRootingTransaction::where('tc_rooting_bottle_id',$data['tc_rooting_bottle_id'])
                ->where('tc_rooting_ob_id',$data['tc_rooting_ob_id'])
                ->update([
                    'last_total' => $data['last_total'],
                    'last_leaf' => $data['last_leaf'],
                ]);
        }
        if($data['last_total'] == 0){
            TcRootingBottle::where('id',$data['tc_rooting_bottle_id'])->update(['status' => 0]);
        }
    }
}
