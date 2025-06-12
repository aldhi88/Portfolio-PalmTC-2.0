<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcInitComment extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function tc_inits()
    {
        return $this->belongsTo(TcInit::class, 'tc_init_id', 'id');
    }
}
