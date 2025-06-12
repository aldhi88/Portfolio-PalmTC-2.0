<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcGerminTransaction extends Model
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
    public function tc_germin_obs(){
        return $this->belongsTo(TcGerminOb::class,'tc_germin_ob_id');
    }
    public function tc_germin_bottles(){
        return $this->belongsTo(TcGerminBottle::class,'tc_germin_bottle_id');
    }

    // process
    public static function storeList($data,$q){
        if($q == 'in'){
            TcGerminTransaction::create($data);
        }else{
            TcGerminTransaction::where('tc_germin_bottle_id',$data['tc_germin_bottle_id'])
                ->where('tc_germin_ob_id',$data['tc_germin_ob_id'])
                ->update(['last_total' => $data['last_total']]);
        }
        if($data['last_total'] == 0){
            TcGerminBottle::where('id',$data['tc_germin_bottle_id'])->update(['status' => 0]);
        }
    }
}
