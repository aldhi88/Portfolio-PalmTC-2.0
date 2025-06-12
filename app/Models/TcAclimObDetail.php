<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcAclimObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_aclim_trees(){
        return $this->belongsTo(TcAclimTree::class,'tc_aclim_tree_id');
    }
}
