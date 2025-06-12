<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcSampleComment extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function tc_samples()
    {
        return $this->belongsTo(TcSample::class, 'tc_sample_id', 'id');
    }

}
