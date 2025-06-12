<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcHarden extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // relation
    public function tc_harden_ob_details(){
        return $this->hasManyThrough(TcHardenObDetail::class,TcHardenOb::class);
    }
    public function tc_harden_obs(){
        return $this->hasMany(TcHardenOb::class,'tc_harden_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_harden_trees(){
        return $this->hasMany(TcHardenTree::class,'tc_harden_id');
    }

    // process
    public static function statusHarden($id){
        $q = TcHardenTree::select('id')->where('tc_harden_id',$id)->where('status',1)->get()->count();
        $q2 = TcHardenOb::select('id')->where('tc_harden_id',$id)
            ->whereHas('tc_harden_ob_details',function($q){
                $q->where('status',0);
            })->get()->count();

        if($q==0 && $q2==0){
            TcHarden::where('id',$id)->update(['status' => 0]);
        }else{
            TcHarden::where('id',$id)->update(['status' => 1]);
        }
    }
    
}
