<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcEmbryoTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_embryo_bottles(){
        return $this->belongsTo(TcEmbryoBottle::class,'tc_embryo_bottle_id');
    }
    public function tc_embryo_obs(){
        return $this->belongsTo(TcEmbryoOb::class,'tc_embryo_ob_id');
    }
    public function tc_embryo_transfer_bottle_works(){
        return $this->hasMany(TcEmbryoTransferBottleWork::class,'tc_embryo_transfer_bottle_id');
    }
}
