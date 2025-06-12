<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcCallusObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relationship
    public function tc_callus_obs(){
        return $this->belongsTo('App\Models\TcCallusOb','tc_callus_ob_id','id');
    }
    public function tc_contaminations(){
        return $this->belongsTo(TcContamination::class,'tc_contamination_id','id');
    }
    public function tc_init_bottles(){
        return $this->belongsTo(TcInitBottle::class,'tc_init_bottle_id','id');
    }
    public function tc_inits()
    {
        return $this->belongsTo(TcInit::class, 'tc_init_id','id');
    }

    public static function getTotalBottleCallusByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->groupBy('tc_init_bottle_id')
            ->where('result',1)
            ->where('is_count_bottle',1)
            ->get()
            ->count();

        $return = $q;
        return $return;
    }
    public static function getTotalExplantCallusByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',1)
            ->where('is_count_explant',1)
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalBottleOxiByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',2)
            ->where('is_count_bottle',1)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalExplantOxiByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',2)
            ->where('is_count_explant',1)
            // ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalBottleContamByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',3)
            ->where('is_count_bottle',1)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalExplantContamByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',3)
            ->where('is_count_explant',1)
            // ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }

    // =======================
    public static function getTotalNewBottleCallusByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',1)
            ->where('is_count_bottle',1)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalOldBottleCallusByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',1)
            ->where('is_count_bottle',0)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalNewExplantCallusByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',1)
            ->where('is_count_explant',1)
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalOldExplantCallusByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',1)
            ->where('is_count_explant',0)
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalBottleOxiByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',2)
            ->where('is_count_bottle',1)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalExplantOxiByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',2)
            ->where('is_count_explant',1)
            // ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        // dd($q);
        $return = $q;
        return $return;
    }
    public static function getTotalBottleContamByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',3)
            ->where('is_count_bottle',1)
            ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        $return = $q;
        return $return;
    }
    public static function getTotalExplantContamByOb($obsId){
        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->where('tc_callus_ob_id',$obsId)
            ->where('result',3)
            ->where('is_count_explant',1)
            // ->groupBy('tc_init_bottle_id')
            ->get()
            ->count();
        // dd($q);
        $return = $q;
        return $return;
    }
    // ==================================
    public static function getListBottleCallusByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->groupBy('tc_init_bottle_id')
            ->where('result',1)
            ->where('is_count_bottle',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }
    public static function getListExplantCallusByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id','explant_number')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',1)
            ->where('is_count_explant',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }
    public static function getListBottleOxiByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->groupBy('tc_init_bottle_id')
            ->where('result',2)
            ->where('is_count_bottle',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }
    public static function getListExplantOxiByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id','explant_number')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',2)
            ->where('is_count_explant',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }
    public static function getListBottleContamByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->groupBy('tc_init_bottle_id')
            ->where('result',3)
            ->where('is_count_bottle',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }
    public static function getListExplantContamByInit($initId){
        $q = TcCallusOb::select('id')
            ->where('tc_init_id',$initId)
            ->where('status',1)
            ->has('tc_callus_ob_details')
            ->get()->toArray();
        $aryObId = [];
        foreach ($q as $key => $value) {
            $aryObId[] = $value['id'];
        }

        $q = TcCallusObDetail::select('tc_init_bottle_id','explant_number')
            ->whereIn('tc_callus_ob_id',$aryObId)
            ->where('result',3)
            ->where('is_count_explant',1)
            ->with([
                'tc_init_bottles' => function($q){
                    $q->select('id','block_number','bottle_number','tc_worker_id');
                },
                'tc_init_bottles.tc_workers' => function($q){
                    $q->select('id','code');
                }
            ])
            ->get();
        $return = $q;
        return $return;
    }


}
