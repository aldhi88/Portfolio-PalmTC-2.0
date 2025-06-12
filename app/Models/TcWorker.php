<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TcWorker extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $appends = ['date_of_birth_format'];

    public function getDateOfBirthFormatAttribute(){
        return Carbon::parse($this->date_of_birth)->format('d M Y');
    }
    public function tc_init_of_workers(){
        return $this->hasMany('App\Models\TcInitOfWorker', 'tc_worker_id', 'id');
    }
    public function getCodeAttribute($value){
        return Str::upper($value);
    }

    public function getNameAttribute($value){
        return Str::upper($value);
    }

    public function selDataDt(){
        return $this->orderBy('created_at', 'DESC')
                    ->where('id','!=',0)
                    ->get();
    }
    public function selDtWorkerInitiation($id){
        return $this->whereDoesntHave('tc_init_of_workers', function($query) use($id){
                        $query->where('tc_init_id', $id);
                    })
                    ->where('status', 1)
                    ->get();
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
        $q = $this->where('id', $id)
                    ->delete();
    }
}
