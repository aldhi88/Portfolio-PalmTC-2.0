<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcAclim extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_aclim_ob_details(){
        return $this->hasManyThrough(TcAclimObDetail::class,TcAclimOb::class);
    }
    public function tc_aclim_obs(){
        return $this->hasMany(TcAclimOb::class,'tc_aclim_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_aclim_trees(){
        return $this->hasMany(TcAclimTree::class,'tc_aclim_id');
    }

    // process
    public static function statusAclim($id){
        $q = TcAclimTree::select('id')->where('tc_aclim_id',$id)->where('status',1)->get()->count();
        $q2 = TcAclimOb::select('id')->where('tc_aclim_id',$id)
            ->where('tc_aclim_id',$id)
            ->whereHas('tc_aclim_ob_details',function($q){
                $q->where('status',0);
            })->get()->count();
        if($q==0 && $q2==0){
            TcAclim::where('id',$id)->update(['status' => 0]);
        }else{
            TcAclim::where('id',$id)->update(['status' => 1]);
        }
    }
}
