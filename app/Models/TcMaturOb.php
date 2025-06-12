<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMaturOb extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }

    // mutator
    public function setAlphaAttribute($value){
        $this->attributes['alpha'] = str_replace(' ','',$value);
    }
    public function getAlphaAttribute($value){
        return str_replace(' ','',$value);
    }


}
