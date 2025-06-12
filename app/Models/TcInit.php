<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcInit extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_field_comments(){
        return $this->hasMany(TcFieldComment::class,'tc_init_id', 'id');
    }
    public function tc_nur_comments(){
        return $this->hasMany(TcNurComment::class,'tc_init_id', 'id');
    }
    public function tc_harden_comments(){
        return $this->hasMany(TcHardenComment::class,'tc_init_id', 'id');
    }
    public function tc_aclim_comments(){
        return $this->hasMany(TcAclimComment::class,'tc_init_id', 'id');
    }
    public function tc_rooting_comments(){
        return $this->hasMany(TcRootingComment::class,'tc_init_id', 'id');
    }
    public function tc_germin_comments(){
        return $this->hasMany(TcGerminComment::class,'tc_init_id', 'id');
    }
    public function tc_matur_comments(){
        return $this->hasMany(TcMaturComment::class,'tc_init_id', 'id');
    }
    public function tc_liquid_comments(){
        return $this->hasMany(TcLiquidComment::class,'tc_init_id', 'id');
    }
    public function tc_embryo_comments(){
        return $this->hasMany(TcEmbryoComment::class,'tc_init_id', 'id');
    }
    public function tc_init_comments(){
        return $this->hasMany(TcInitComment::class,'tc_init_id', 'id');
    }
    public function tc_callus_comments(){
        return $this->hasMany(TcCallusComment::class,'tc_init_id', 'id');
    }
    public function tc_field_obs(){
        return $this->hasMany(TcFieldOb::class,'tc_init_id');
    }
    public function tc_field_ob_details(){
        return $this->hasMany(TcFieldObDetail::class,'tc_init_id');
    }
    public function tc_field_trees(){
        return $this->hasOneThrough(TcFieldTree::class,TcField::class);
    }
    public function tc_fields(){
        return $this->hasMany(TcField::class,'tc_init_id');
    }

    public function tc_nur_transfers(){
        return $this->hasMany(TcNurTransfer::class,'tc_init_id');
    }
    public function tc_nur_obs(){
        return $this->hasMany(TcNurOb::class,'tc_init_id');
    }
    public function tc_nur_ob_details(){
        return $this->hasMany(TcNurObDetail::class,'tc_init_id');
    }
    public function tc_nur_trees(){
        return $this->hasOneThrough(TcNurTree::class,TcNur::class);
    }
    public function tc_nurs(){
        return $this->hasMany(TcNur::class,'tc_init_id');
    }

    public function tc_harden_transfers(){
        return $this->hasMany(TcHardenTransfer::class,'tc_init_id');
    }
    public function tc_harden_obs(){
        return $this->hasMany(TcHardenOb::class,'tc_init_id');
    }
    public function tc_harden_ob_details(){
        return $this->hasMany(TcHardenObDetail::class,'tc_init_id');
    }
    public function tc_harden_trees(){
        return $this->hasOneThrough(TcHardenTree::class,TcHarden::class);
    }
    public function tc_hardens(){
        return $this->hasMany(TcHarden::class,'tc_init_id');
    }

    public function tc_aclim_transfers(){
        return $this->hasMany(TcAclimTransfer::class,'tc_init_id');
    }
    public function tc_aclim_obs(){
        return $this->hasMany(TcAclimOb::class,'tc_init_id');
    }
    public function tc_aclim_ob_details(){
        return $this->hasMany(TcAclimObDetail::class,'tc_init_id');
    }
    public function tc_aclim_trees(){
        return $this->hasOneThrough(TcAclimTree::class,TcAclim::class);
    }
    public function tc_aclims(){
        return $this->hasMany(TcAclim::class,'tc_init_id');
    }
    public function tc_rooting_transfer_bottles(){
        return $this->hasMany(TcRootingTransferBottle::class,'tc_init_id');
    }
    public function tc_rooting_transfers(){
        return $this->hasMany(TcRootingTransfer::class,'tc_init_id');
    }
    public function tc_rooting_obs(){
        return $this->hasMany(TcRootingOb::class,'tc_init_id');
    }
    public function tc_rooting_bottles(){
        return $this->hasMany(TcRootingBottle::class,'tc_init_id');
    }
    public function tc_germin_transfer_bottles(){
        return $this->hasMany(TcGerminTransferBottle::class,'tc_init_id');
    }
    public function tc_germin_transfers(){
        return $this->hasMany(TcGerminTransfer::class,'tc_init_id');
    }
    public function tc_germin_obs(){
        return $this->hasMany(TcGerminOb::class,'tc_init_id');
    }
    public function tc_germin_bottles(){
        return $this->hasMany(TcGerminBottle::class,'tc_init_id');
    }
    public function tc_matur_transfer_bottles(){
        return $this->hasMany(TcMaturTransferBottle::class,'tc_init_id');
    }
    public function tc_matur_transfers(){
        return $this->hasMany(TcMaturTransfer::class,'tc_init_id');
    }
    public function tc_matur_obs(){
        return $this->hasMany(TcMaturOb::class,'tc_init_id');
    }
    public function tc_matur_bottles(){
        return $this->hasMany(TcMaturBottle::class,'tc_init_id');
    }
    public function tc_liquid_transfer_bottles(){
        return $this->hasMany(TcLiquidTransferBottle::class,'tc_init_id');
    }
    public function tc_liquid_transfers(){
        return $this->hasMany(TcLiquidTransfer::class,'tc_init_id');
    }
    public function tc_liquid_obs(){
        return $this->hasMany(TcLiquidOb::class,'tc_init_id');
    }
    public function tc_liquid_bottles(){
        return $this->hasMany(TcLiquidBottle::class,'tc_init_id');
    }
    public function tc_embryo_transfer_bottles(){
        return $this->hasMany(TcEmbryoTransferBottle::class,'tc_init_id');
    }
    public function tc_embryo_transfers(){
        return $this->hasMany(TcEmbryoTransfer::class,'tc_init_id');
    }
    public function tc_embryo_obs(){
        return $this->hasMany(TcEmbryoOb::class,'tc_init_id');
    }
    public function tc_embryo_bottles(){
        return $this->hasMany(TcEmbryoBottle::class,'tc_init_id');
    }
    public function tc_callus_transfer_bottles(){
        return $this->hasMany(TcCallusTransferBottle::class,'tc_init_id','id');
    }
    public function tc_callus_ob_details(){
        return $this->hasMany(TcCallusObDetail::class,'tc_init_id','id');
    }
    public function tc_callus_obs(){
        return $this->hasMany('App\Models\TcCallusOb','tc_init_id','id');
    }
    public function tc_samples(){
        return $this->belongsTo('App\Models\TcSample','tc_sample_id','id');
    }
    public function tc_rooms(){
        return $this->belongsTo('App\Models\TcRoom','tc_room_id','id');
    }
    public function tc_init_bottles(){
        return $this->hasMany('App\Models\TcInitBottle','tc_init_id','id');
    }


    public static function getMediumStock($initId){
        $q = TcInitBottle::select('tc_medium_stock_id')
            ->where('tc_init_id',$initId)
            ->with([
                'tc_medium_stocks:id,created_at,tc_medium_id',
                'tc_medium_stocks.tc_mediums:id,code'
            ])
            ->groupBy('tc_medium_stock_id')
            ->get();
        return $q;
    }
}
