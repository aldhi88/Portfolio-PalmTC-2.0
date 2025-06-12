<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcNurTree extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_nur_ob_details(){
        return $this->hasOne(TcNurObDetail::class,'tc_nur_tree_id');
    }
    public function tc_nurs(){
        return $this->belongsTo(TcNur::class,'tc_nur_id');
    }
    
}
