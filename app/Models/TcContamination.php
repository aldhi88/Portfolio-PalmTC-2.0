<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TcContamination extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function getCodeAttribute($value){
        return Str::upper($value);
    }
    public function selDataDt(){
        return $this->query();
    }
    public function selByCol($col,$val){
        return $this->where($col, $val)
                    ->get();
    }

    // CRUD
    public function inData($data){
        return $this->create($data);
    }
    public function selData(){
        return $this->all();
    }
    public function upData($id, $data){
        return $this->where('id', $id)
                    ->update($data);
    }
    public function delData($id){
        return $this->where('id', $id)
                    ->delete();
    }
}
