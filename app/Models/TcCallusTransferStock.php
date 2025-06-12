<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcCallusTransferStock extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function tc_medium_stocks(){
        return $this->belongsTo('App\Models\TcMediumStock','tc_medium_stock_id','id');
    }

}