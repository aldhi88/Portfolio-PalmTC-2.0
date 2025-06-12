<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoList extends Model
{
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
    // ==========================

    public static function storeList($data,$q){
        if($q == 'in'){
            TcEmbryoList::create($data);
        }else{
            TcEmbryoList::where('tc_embryo_bottle_id',$data['tc_embryo_bottle_id'])
                ->where('tc_embryo_ob_id',$data['tc_embryo_ob_id'])
                ->update([
                    'last_total' => $data['last_total'],
                    'tc_worker_id' => $data['tc_worker_id'],
                ]);
        }
        if($data['last_total'] == 0){
            TcEmbryoBottle::where('id',$data['tc_embryo_bottle_id'])->update(['status' => 0]);
        }
    }
}
