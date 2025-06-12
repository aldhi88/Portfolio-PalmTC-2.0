<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TcFieldObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_field_trees(){
        return $this->belongsTo(TcFieldTree::class,'tc_field_tree_id');
    }
    
}
