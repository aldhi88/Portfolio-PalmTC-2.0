<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TcMedium extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'tc_mediums';

    public function tc_medium_opname(){
        return $this->hasManyThrough(TcMediumOpname::class, TcMediumStock::class);
    }
    public function tc_callus_transfer_stocks(){
        return $this->hasManyThrough(TcCallusTransferStock::class, TcMediumStock::class);
    }
    public function tc_medium_stocks(){
        return $this->hasMany('App\Models\TcMediumStock', 'tc_medium_id', 'id');
    }
    public function getCodeAttribute($value){
        return Str::upper($value);
    }
    public function getNameAttribute($value){
        return Str::upper($value);
    }
    public function totalStock($key){
        $TcMediumStock = new TcMediumStock();
        $masterStock = $TcMediumStock->totalStockPerId($key);

        $totalStock = $masterStock;
        return $totalStock;
    }

    public function selRelData(){
        return $this->with('tc_medium_stocks','tc_medium_opname','tc_callus_transfer_stocks');
    }
    public function selDataDt(){
        return $this->with('tc_medium_stocks','tc_medium_opname');
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
