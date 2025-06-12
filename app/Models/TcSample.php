<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcSample extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $appends = [
        'sample_number_display',
        'resample_display',
        'resample_format',
        'created_at_short_format',
        'created_at_long_format',
        'created_at_num_format',
        'weekOfYear',
    ];

    // relationship
    public function tc_sample_comments(){
        return $this->hasMany(TcSampleComment::class,'tc_sample_id','id');
    }
    public function tc_inits(){
        return $this->hasOne('App\Models\TcInit','tc_sample_id','id');
    }

    public function getWeekOfYearAttribute(){
        return Carbon::parse($this->created_at)->weekOfYear;
    }
    public function getLastData(){
        return $this->select('id', 'sample_number')
                    ->latest('id')
                    ->first();

    }
    public function getResampleFormatAttribute(){
        if(is_null($this->resample)){
            return '';
        }else{
            return sprintf('S%03d', $this->resample);
        }
    }
    public function getCreatedAtShortFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d/m/y');
    }
    public function getCreatedAtLongFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d M Y');
    }
    public function getCreatedAtNumFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }
    public function getResampleDisplayAttribute(){
        if(is_null($this->resample)){
            return '<label class="badge badge-light-primary mb-0 d-block w-100 rounded-0">New</label>';
        }else{
            $resampleNumber = TcSample::where('id',$this->resample)->first()->getAttribute('sample_number');
            $idResemple = $this->resample;
            $stopCheck = null;
            $el = '<label class="badge badge-light-danger mb-0 d-block w-100 rounded-0">'.sprintf('S%03d', $resampleNumber).'</label>';
            while(is_null($stopCheck)){
                $q = $this->where('id', $idResemple)
                          ->select('id','resample')
                          ->get()
                          ->first();
                if(is_null($q->resample)){
                    $stopCheck = true;
                }else{
                    $idResemple = $q->resample;
                    $el .= '<br><label class="badge badge-light-danger mb-0 d-block w-100 rounded-0">'.sprintf('S%03d', $idResemple).'</label> ';
                }
            }
            // dd($stopCheck);
            return $el;
        }

        // if(is_null($this->resample)){
        //     return '<label class="badge badge-light-primary">New</label>';
        // }else{
        //     return '<label class="badge badge-light-danger">Resample ('.sprintf('S%03d', $this->resample).')</label>';
        // }
    }
    public function getSampleNumberDisplayAttribute(){
        return sprintf('S%03d', $this->sample_number);
    }
    public function master_treefile(){
        return $this->belongsTo('App\Models\MasterTreefile', "master_treefile_id", "id");
    }

    public function displayNumb(){
        $numb = $this->newSampleNumb();
        $formatNumb = sprintf("S%03d", $numb);
        return $formatNumb;
    }
    public function newSampleNumb(){
        $q = $this->select('sample_number')
                  ->orderBy('sample_number', 'desc')
                  ->get();
        if($q->count() == 0){
            $numb = 1;
        }else{
            $last = $q->first()->sample_number;
            $numb = $last + 1;
        }
        return $numb;
    }
    public function dtTreefile(){
        $MasterTreefile = new MasterTreefile();
        return $MasterTreefile->whereNotNull('noseleksi');
    }
    public function dtSample(){
        return $this->with('master_treefile');
    }
    public function selByCol($col,$val){
        return $this->where($col, $val)
                    ->with('master_treefile')
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
                    ->forceDelete();
    }
}
