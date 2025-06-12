<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMaturTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_matur_bottles(){
        return $this->belongsTo(TcMaturBottle::class,'tc_matur_bottle_id');
    }
    public function tc_matur_obs(){
        return $this->belongsTo(TcMaturOb::class,'tc_matur_ob_id');
    }
    public function tc_matur_transfer_bottle_works(){
        return $this->hasMany(TcMaturTransferBottleWork::class,'tc_matur_transfer_bottle_id');
    }
}
