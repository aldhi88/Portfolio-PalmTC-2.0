<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcGerminTransferBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_germin_bottles(){
        return $this->belongsTo(TcGerminBottle::class,'tc_germin_bottle_id');
    }
    public function tc_germin_obs(){
        return $this->belongsTo(TcGerminOb::class,'tc_germin_ob_id');
    }
    public function tc_germin_transfer_bottle_works(){
        return $this->hasMany(TcGerminTransferBottleWork::class,'tc_germin_transfer_bottle_id');
    }
}
