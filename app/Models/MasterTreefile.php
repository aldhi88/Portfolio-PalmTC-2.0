<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTreefile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'master_treefile';

    public function selData(){
        return $this->all();
    }
    public function selDataDt(){
        return $this->whereNotNull('noseleksi');
    }
}
