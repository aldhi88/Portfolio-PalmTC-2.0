<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_rooting_bottles(){
        return $this->belongsTo(TcRootingBottle::class,'tc_rooting_bottle_id');
    }
    public function tc_rooting_obs(){
        return $this->belongsTo(TcRootingOb::class,'tc_rooting_ob_id');
    }
    public function tc_rooting_transfer_bottle_works(){
        return $this->hasMany(TcRootingTransferBottleWork::class,'tc_rooting_transfer_bottle_id');
    }
}
