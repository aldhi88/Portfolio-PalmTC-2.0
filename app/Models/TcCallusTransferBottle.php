<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcCallusTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_inits(){
        return $this->belongsTo('App\Models\TcInit','tc_init_id','id');
    }
    public function tc_medium_stocks(){
        return $this->belongsTo('App\Models\TcMediumStock','tc_medium_stock_id','id');
    }
    public function tc_callus_transfers(){
        return $this->belongsTo('App\Models\TcCallusTransfer','tc_callus_transfer_id','id');
    }
}
