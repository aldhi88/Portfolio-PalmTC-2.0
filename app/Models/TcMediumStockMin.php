<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMediumStockMin extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "tc_medium_stocks";

    public function tc_mediums(){
        return $this->belongsTo('App\Models\TcMedium', 'tc_medium_id', 'id');
    }
    public function tc_bottles(){
        return $this->belongsTo('App\Models\TcBottle', 'tc_bottle_id', 'id');
    }
    public function tc_agars(){
        return $this->belongsTo('App\Models\TcAgar', 'tc_agar_id', 'id');
    }
    public function tc_workers(){
        return $this->belongsTo('App\Models\TcWorker', 'tc_worker_id', 'id');
    }
    public function tc_medium_opname(){
        return $this->hasMany('App\Models\TcMediumOpname', 'tc_medium_stock_id', 'id');
    }
    public function tc_callus_transfer_stocks(){
        return $this->hasMany('App\Models\TcCallusTransferStock','tc_medium_stock_id','id');
    }
    public function tc_init_bottles(){
        return $this->hasMany('App\Models\TcInitBottle','tc_medium_stock_id','id');
    }
}
