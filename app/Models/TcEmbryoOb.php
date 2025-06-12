<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id','id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_embryo_ob_details(){
        return $this->hasMany(TcEmbryoObDetail::class,'tc_embryo_ob_id');
    }
    public function tc_embryo_transfer_bottles(){
        return $this->hasMany(TcEmbryoTransferBottle::class,'tc_embryo_ob_id');
    }

    // process
    public static function nextOb($initId){
        $q = TcEmbryoOb::select('id')->where('tc_init_id',$initId)->where('status',0)->get();
        if(count($q)==0){
            $q = TcEmbryoOb::create(['tc_init_id' => $initId]);
        }
        return $q->first()->id;
    }
}
