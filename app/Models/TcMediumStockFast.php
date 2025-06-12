<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMediumStockFast extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'tc_medium_stocks';

    public function tc_mediums(){
        return $this->belongsTo('App\Models\TcMedium', 'tc_medium_id', 'id');
    }
    public function tc_bottles(){
        return $this->belongsTo('App\Models\TcBottle', 'tc_bottle_id', 'id');
    }
    public function tc_agars(){
        return $this->belongsTo('App\Models\TcAgar', 'tc_agar_id', 'id');
    }
}
