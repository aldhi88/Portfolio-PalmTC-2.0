<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcHardenTree extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_harden_ob_details(){
        return $this->hasOne(TcHardenObDetail::class,'tc_harden_tree_id');
    }
    public function tc_hardens(){
        return $this->belongsTo(TcHarden::class,'tc_harden_id');
    }
    
}
