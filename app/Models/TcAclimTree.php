<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcAclimTree extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_aclim_ob_details(){
        return $this->hasOne(TcAclimObDetail::class,'tc_aclim_tree_id');
    }
    public function tc_aclims(){
        return $this->belongsTo(TcAclim::class,'tc_aclim_id');
    }

}
