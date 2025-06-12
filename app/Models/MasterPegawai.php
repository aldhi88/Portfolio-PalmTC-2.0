<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MasterPegawai extends Authenticatable
{
    use HasFactory;
    protected $table = 'master_pegawai';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function selData(){
        return $this->all()->toArray();
    }
}
