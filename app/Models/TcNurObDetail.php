<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TcNurObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_nur_obs(){
        return $this->belongsTo(TcNurOb::class,'tc_nur_ob_id');
    }
    public function tc_nur_trees(){
        return $this->belongsTo(TcNurTree::class,'tc_nur_tree_id');
    }
    
}
