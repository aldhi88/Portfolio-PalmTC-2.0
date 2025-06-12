<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcCallusOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    
    // relationship
    public function tc_inits(){
        return $this->belongsTo('App\Models\TcInit','tc_init_id','id');
    }
    public function tc_callus_ob_details(){
        return $this->hasMany(TcCallusObDetail::class,'tc_callus_ob_id','id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id','id');
    }


}
