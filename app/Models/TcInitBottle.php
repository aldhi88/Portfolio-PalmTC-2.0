<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcInitBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_medium_stocks_min(){
        return $this->belongsTo('App\Models\TcMediumStockMin','tc_medium_stock_id','id');
    }
    public function tc_medium_stocks(){
        return $this->belongsTo('App\Models\TcMediumStock','tc_medium_stock_id','id');
    }
    public function tc_medium_stock_fast(){
        return $this->belongsTo('App\Models\TcMediumStockFast','tc_medium_stock_id','id');
    }
    public function tc_inits(){
        return $this->belongsTo('App\Models\TcInit','tc_init_id','id');
    }
    public function tc_workers(){
        return $this->belongsTo('App\Models\TcWorker','tc_worker_id','id');
    }
    public function tc_laminars(){
        return $this->belongsTo('App\Models\TcLaminar','tc_laminar_id','id');
    }
    public function tc_callus_obs(){
        return $this->hasMany('App\Models\TcCallusOb','tc_laminar_id','id');
    }

    public static function getLaminar($initId,$workerId){
        $q = TcInitBottle::select('tc_laminar_id')
            ->where('tc_init_id',$initId)
            ->where('tc_worker_id', $workerId)
            ->get()
            ->first();
        return $q->tc_laminar_id;
    }
    public static function getLastBottleNumber($initId){
        $q = TcInitBottle::select('id')
            ->where('tc_init_id',$initId)
            ->orderBy('bottle_number','desc')
            ->get()
            ->count();
        return $q;
    }

}
