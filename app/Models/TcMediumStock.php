<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMediumStock extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $appends = [
        'created_at_long_format',
        'created_at_short_format',
        'age',
        'stock_in',
        'stock_out',
        'stock_used',
        'callus_transfer_used',
        'embryo_transfer_used',
        'embryo_transfer_used',
        'liquid_transfer_used',
        'matur_transfer_used',
        'current_stock',
    ];

    public function getCurrentStockAttribute(){
        return $this->stock +
            $this->getStockInAttribute() -
            $this->getStockOutAttribute() -
            $this->getStockUsedAttribute() -
            $this->getCallusTransferUsedAttribute() -
            $this->getEmbryoTransferUsedAttribute() -
            $this->getLiquidTransferUsedAttribute() -
            $this->getMaturTransferUsedAttribute() -
            $this->getGerminTransferUsedAttribute() -
            $this->getRootingTransferUsedAttribute()
        ;
    }
    public function getRootingTransferUsedAttribute(){
        $id = $this->id;
        $data = TcRootingTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("used_stock");
        }
        return $return;
    }
    public function getGerminTransferUsedAttribute(){
        $id = $this->id;
        $data = TcGerminTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("used_stock");
        }
        return $return;
    }
    public function getMaturTransferUsedAttribute(){
        $id = $this->id;
        $data = TcMaturTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("used_stock");
        }
        return $return;
    }
    public function getLiquidTransferUsedAttribute(){
        $id = $this->id;
        $data = TcLiquidTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("used_stock");
        }
        return $return;
    }
    public function getEmbryoTransferUsedAttribute(){
        $id = $this->id;
        $data = TcEmbryoTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("used_stock");
        }
        return $return;
    }
    public function getCallusTransferUsedAttribute(){
        $id = $this->id;
        $data = TcCallusTransferStock::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("stock_used");
        }
        return $return;
    }
    public function getStockUsedAttribute(){
        $id = $this->id;
        $return = TcInitBottle::where('tc_medium_stock_id',$id)
            ->where('status',1)
            ->get()
            ->count();
        return $return;
    }
    public function getStockOutAttribute(){
        $id = $this->id;
        $data = TcMediumOpname::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("stock_out");
        }
        return $return;
    }
    public function getStockInAttribute(){
        $id = $this->id;
        $data = TcMediumOpname::where("tc_medium_stock_id", $id);
        if(count($data->get()) == 0){
            $return = 0;
        }else{
            $return = $data->sum("stock_in");
        }
        return $return;
    }
    public function getAgeAttribute(){
        return Carbon::parse($this->created_at)->diffInDays(now());
    }
    public function getCreatedAtShortFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d/m/y');
    }
    public function getCreatedAtLongFormatAttribute(){
        return Carbon::parse($this->created_at)->format('d M Y');
    }

    public function tc_mediums(){
        return $this->belongsTo('App\Models\TcMedium', 'tc_medium_id', 'id');
    }
    public function tc_bottles(){
        return $this->belongsTo('App\Models\TcBottle', 'tc_bottle_id', 'id');
    }
    public function tc_agars(){
        return $this->belongsTo('App\Models\TcAgar', 'tc_agar_id', 'id');
    }
    public function tc_workers(){
        return $this->belongsTo('App\Models\TcWorker', 'tc_worker_id', 'id');
    }
    public function tc_medium_opname(){
        return $this->hasMany('App\Models\TcMediumOpname', 'tc_medium_stock_id', 'id');
    }
    public function tc_callus_transfer_stocks(){
        return $this->hasMany('App\Models\TcCallusTransferStock','tc_medium_stock_id','id');
    }
    public function tc_embryo_transfer_stocks(){
        return $this->hasMany('App\Models\TcEmbryoTransferStock','tc_medium_stock_id','id');
    }
    public function tc_liquid_transfer_stocks(){
        return $this->hasMany(TcLiquidTransferStock::class,'tc_medium_stock_id','id');
    }
    public function tc_matur_transfer_stocks(){
        return $this->hasMany(TcMaturTransferStock::class,'tc_medium_stock_id','id');
    }
    public function tc_germin_transfer_stocks(){
        return $this->hasMany(TcGerminTransferStock::class,'tc_medium_stock_id','id');
    }
    public function tc_rooting_transfer_stocks(){
        return $this->hasMany(TcRootingTransferStock::class,'tc_medium_stock_id','id');
    }
    public function tc_init_bottles(){
        return $this->hasMany('App\Models\TcInitBottle','tc_medium_stock_id','id');
    }

    public function dataHistory($id){
        return $this->with('tc_medium_opname')
                    ->where('id', $id)
                    ->get();
    }
    public function selDataDt(){
        return $this->with('tc_mediums:id,name,code')
                    ->with('tc_workers:id,code,name')
                    ->where('id','!=',0);
    }
    public function selByCol($col,$val){
        return $this->where($col, $val)
                    ->with('tc_mediums', 'tc_bottles', 'tc_agars', 'tc_workers', 'tc_medium_opname')
                    ->get();
    }
    public function selByCol2(){
        return $this->with('tc_mediums', 'tc_bottles', 'tc_agars', 'tc_workers', 'tc_medium_opname')
                    ->get();
    }

    // CRUD
    public function inData($data){
        return $this->create($data);
    }
    public function selData(){
        return $this->with('tc_mediums', 'tc_bottles', 'tc_agars', 'tc_workers', 'tc_medium_opname')
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
