<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TcBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function tc_inits(){
        return $this->hasMany("App\Models\TcInit","tc_bottle_id","id");
    }
    public function getCodeAttribute($value){
        return Str::upper($value);
    }
    public function getNameAttribute($value){
        return Str::upper($value);
    }
    public function selDataDt(){
        return $this->get();
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
