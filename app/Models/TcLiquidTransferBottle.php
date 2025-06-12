<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcLiquidTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_liquid_bottles(){
        return $this->belongsTo(TcLiquidBottle::class,'tc_liquid_bottle_id');
    }
    public function tc_liquid_obs(){
        return $this->belongsTo(TcLiquidOb::class,'tc_liquid_ob_id');
    }
    public function tc_liquid_transfer_bottle_works(){
        return $this->hasMany(TcLiquidTransferBottleWork::class,'tc_liquid_transfer_bottle_id');
    }
    public function tc_liquid_transfer_bottle_work(){
        return $this->hasMany(TcLiquidTransferBottleWork::class,'tc_liquid_transfer_bottle_id');
    }
}
