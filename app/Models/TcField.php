<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcField extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // relation
    public function tc_field_ob_details(){
        return $this->hasManyThrough(TcFieldObDetail::class,TcFieldOb::class);
    }
    public function tc_field_obs(){
        return $this->hasMany(TcFieldOb::class,'tc_field_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_field_trees(){
        return $this->hasMany(TcFieldTree::class,'tc_field_id');
    }

    // process
    public static function statusField($id){
        $q = TcFieldTree::select('id')->where('tc_field_id',$id)->where('status',1)->get()->count();
        $q2 = TcFieldOb::select('id')
            ->whereHas('tc_field_ob_details',function($q){
                $q->where('status',0);
            })->get()->count();

        if($q==0 && $q2==0){
            TcField::where('id',$id)->update(['status' => 0]);
        }else{
            TcField::where('id',$id)->update(['status' => 1]);
        }
    }
    
}
