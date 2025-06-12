<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TcNur extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // relation
    public function tc_plantations(){
        return $this->belongsTo(TcPlantation::class,'tc_plantation_id');
    }
    public function tc_nur_ob_details(){
        return $this->hasManyThrough(TcNurObDetail::class,TcNurOb::class);
    }
    public function tc_nur_obs(){
        return $this->hasMany(TcNurOb::class,'tc_nur_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_nur_trees(){
        return $this->hasMany(TcNurTree::class,'tc_nur_id');
    }

    // process
    public static function statusNur($id){
        $q = TcNurTree::select('id')->where('tc_nur_id',$id)->where('status',1)->get()->count();
        $q2 = TcNurOb::select('id')->where('tc_nur_id',$id)
            ->whereHas('tc_nur_ob_details',function($q){
                $q->where('status',0);
            })->get()->count();

        if($q==0 && $q2==0){
            TcNur::where('id',$id)->update(['status' => 0]);
        }else{
            TcNur::where('id',$id)->update(['status' => 1]);
        }
    }
    
}
