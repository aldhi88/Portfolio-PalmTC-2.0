<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoTransferStock extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    // relation
    public function tc_medium_stocks(){
        return $this->belongsTo(TcMediumStock::class,'tc_medium_stock_id');
    }

}
