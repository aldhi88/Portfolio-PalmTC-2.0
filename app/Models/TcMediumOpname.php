<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMediumOpname extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'tc_medium_opname';
    protected $appends = [
        'created_at_long_format',
        'created_at_short_format',
    ];

    public function getCreatedAtShortFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d/m/y');
    }
    public function getCreatedAtLongFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d M Y');
    }
    public function tc_medium_stocks(){
        return $this->belongsTo('App\Models\TcMediumStock', 'tc_medium_stock_id', 'id');
    }
    public function selDataDt(){
        return $this->with('tc_medium_stocks');
    }
    public function selByCol($col,$val){
        return $this->where($col, $val)
                    ->with(['tc_medium_stocks' => function($q){
                        $q->with('tc_mediums');
                    }])
                    ->get();
    }

    // CRUD
    public function inData($data){
        return $this->create($data);
    }
    public function selData(){
        return $this->with(['tc_medium_stocks' => function($q){
                        $q->with('tc_mediums');
                    }])
                    ->get();
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
