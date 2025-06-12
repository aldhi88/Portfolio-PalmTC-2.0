<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcFieldTree extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_field_ob_details(){
        return $this->hasOne(TcFieldObDetail::class,'tc_field_tree_id');
    }
    public function tc_fields(){
        return $this->belongsTo(TcField::class,'tc_field_id');
    }
    
}
