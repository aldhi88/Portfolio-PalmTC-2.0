<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Translation\Dumper\DumperInterface;

class TcObsDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // relation
    public function tc_observations(){
        return $this->belongsTo('App\Models\TcObservation','tc_observation_id','id');
    }
    public function tc_init_of_explants(){
        return $this->belongsTo('App\Models\TcInitOfExplant','tc_init_of_explant_id','id');
    }
    public function tc_contaminations(){
        return $this->belongsTo('App\Models\TcContamination','tc_contamination_id','id');
    }

    //================================================== master function
    public static function getAllOldBottlePerObsByResult($obsId,$result){
        $oldExplant = array_values(array_unique(TcObsDetail::getAllOldExplantPerObsByResult($obsId,$result)));
        $dtOldtBottle = TcInitOfExplant::select('tc_init_of_bottle_id')
            ->whereIn('id',$oldExplant)
            ->groupBy('tc_init_of_bottle_id')
            ->get();
        $aryOldBottle = [];
        foreach ($dtOldtBottle as $key => $value) {
            array_push($aryOldBottle,$value->tc_init_of_bottle_id);
        }
        return $aryOldBottle;
    }
    public static function getAllBottlePerObsByResult($obsId,$result){
        $dtExplant = TcObsDetail::select('tc_init_of_explant_id')
            ->where('result',$result)
            ->where('tc_observation_id',$obsId)
            ->get();
        $dtBottle = TcInitOfExplant::select('tc_init_of_bottle_id')
            ->whereIn('id',$dtExplant)
            ->groupBy('tc_init_of_bottle_id')
            ->get();
        $aryBottle = [];
        foreach ($dtBottle as $key => $value) {
            array_push($aryBottle,$value->tc_init_of_bottle_id);
        }
        return $aryBottle;
    }
    public static function getAllOldExplantPerObsByResult($obsId,$result){
        $initId = TcObservation::where('id',$obsId)->select('tc_init_id')->get()->first()->getAttribute('tc_init_id');
        $dateObs = TcObservation::where('id',$obsId)->select('date_obs')->get()->first()->getAttribute('date_obs');
        $dtOldObs = TcObservation::select('id')
            ->where('id','!=',$obsId)
            ->where('tc_init_id',$initId)
            ->where('date_obs','<',$dateObs)
            ->get();
        $aryOldObsId = [];
        foreach ($dtOldObs as $key => $value) {
            array_push($aryOldObsId,$value->id);
        }
        $dtOldExplant = TcObsDetail::select('tc_init_of_explant_id')
            ->whereIn('tc_observation_id',$aryOldObsId)
            ->where('result',$result)
            ->get();
        $aryOldExplantId = [];
        foreach ($dtOldExplant as $key => $value) {
            array_push($aryOldExplantId,$value->tc_init_of_explant_id);
        }
        return $aryOldExplantId;
    }
    public static function getAllExplantPerObsByResult($obsId,$result){
        $dtExplant = TcObsDetail::select('tc_init_of_explant_id')
            ->where('result',$result)
            ->where('tc_observation_id',$obsId)
            ->get();
        $aryExplant = [];
        foreach ($dtExplant as $key => $value) {
            array_push($aryExplant,$value->tc_init_of_explant_id);
        }
        return $aryExplant;
    }
    // ================================================end function for callus

    //========================================================= callus section
    public static function newCallusExplantPerObs($obsId){
        $dtCallusExplant = TcObsDetail::getAllExplantPerObsByResult($obsId,1);
        $dtOldCallusExplant = TcObsDetail::getAllOldExplantPerObsByResult($obsId,1);
        $aryNewCallus = [];
        foreach ($dtCallusExplant as $key => $value) {
            if(!in_array($value,$dtOldCallusExplant)){
                array_push($aryNewCallus,$value);
            }
        }
        return $aryNewCallus;
    }
    public static function doubleCallusExplantPerObs($obsId){
        $dtCallusExplant = TcObsDetail::getAllExplantPerObsByResult($obsId,1);
        $dtOldCallusExplant = TcObsDetail::getAllOldExplantPerObsByResult($obsId,1);
        $aryOldCallus = [];
        foreach ($dtCallusExplant as $key => $value) {
            if(in_array($value,$dtOldCallusExplant)){
                array_push($aryOldCallus,$value);
            }
        }
        return $aryOldCallus;
    }
    public static function newBottleCallusPerObs($obsId){
        $dtBottleCallus = TcObsDetail::getAllBottlePerObsByResult($obsId,1);
        $dtOldBottleCallus = TcObsDetail::getAllOldBottlePerObsByResult($obsId,1);
        $aryNewBottleCallus = [];
        foreach ($dtBottleCallus as $key => $value) {
            if(!in_array($value,$dtOldBottleCallus)){
                array_push($aryNewBottleCallus,$value);
            }
        }
        return $aryNewBottleCallus;
    }
    public static function doubleBottleCallusPerObs($obsId){
        $dtBottleCallus = TcObsDetail::getAllBottlePerObsByResult($obsId,1);
        $dtOldBottleCallus = TcObsDetail::getAllOldBottlePerObsByResult($obsId,1);
        $aryOldBottlecallus = [];
        foreach ($dtBottleCallus as $key => $value) {
            if(in_array($value,$dtOldBottleCallus)){
                array_push($aryOldBottlecallus,$value);
            }
        }
        return $aryOldBottlecallus;
    }
    public static function totalBottleCallusPerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtBottleCallus'] = TcObsDetail::getAllOldBottlePerObsByResult($data['obsId'],1);
        return count($data['dtBottleCallus']);
    }
    public static function totalExplantCallusPerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtExplantCallus'] = array_values(array_unique(TcObsDetail::getAllOldExplantPerObsByResult($data['obsId'],1)));
        return count($data['dtExplantCallus']);
    }

    // ======================================================oxidation section
    public static function newOxidateExplantPerObs($obsId){
        $dtOxidateExplant = TcObsDetail::getAllExplantPerObsByResult($obsId,2);
        return $dtOxidateExplant;
    }
    public static function newOxidateBottlePerObs($obsId){
        $dtCallusBottle = TcObsDetail::getAllOldBottlePerObsByResult($obsId,1);
        $dtBottle = TcObsDetail::getAllBottlePerObsByResult($obsId,2);
        $dtReal = [];
        foreach ($dtBottle as $key => $value) {
            if(!in_array($value,$dtCallusBottle)){
                array_push($dtReal,$value);
            }
        }
        return $dtReal;
    }
    public static function totalBottleOxidatePerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtBottleOxidate'] = TcObsDetail::getAllOldBottlePerObsByResult($data['obsId'],2);
        return count($data['dtBottleOxidate']);
    }
    public static function totalExplantOxidatePerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtExplantOxidate'] = array_values(array_unique(TcObsDetail::getAllOldExplantPerObsByResult($data['obsId'],2)));
        return count($data['dtExplantOxidate']);
    }

    //==================================================== contaminate section
    public static function newContaminateExplantPerObs($obsId){
        $dtExplant = TcObsDetail::getAllExplantPerObsByResult($obsId,3);
        return $dtExplant;
    }
    public static function newContaminateBottlePerObs($obsId){
        $dtBottle = TcObsDetail::getAllBottlePerObsByResult($obsId,3);
        $dtBottleCallus = TcObsDetail::getAllOldBottlePerObsByResult($obsId,1);
        $dtReal = [];
        foreach ($dtBottle as $key => $value) {
            if(!in_array($value,$dtBottleCallus)){
                array_push($dtReal,$value);
            }
        }
        return $dtReal;
    }
    public static function totalBottleContaminatePerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtBottleContaminate'] = TcObsDetail::getAllOldBottlePerObsByResult($data['obsId'],3);
        return count($data['dtBottleContaminate']);
    }
    public static function totalExplantContaminatePerInit($initId){
        $q = TcObservation::where('tc_init_id', $initId)
            ->where('status', 0)
            ->where('type', 1)
            ->orderBy('date_obs','desc')
            ->get();
        $data['obsId'] = $q->first()->id;
        $data['dtExplantContaminate'] = array_values(array_unique(TcObsDetail::getAllOldExplantPerObsByResult($data['obsId'],3)));
        return count($data['dtExplantContaminate']);
    }
    

    public static function getBottleIndexByExplant(array $dtExplant){
        $brokenExplant = [];
        foreach ($dtExplant as $key => $value) {
            array_push($brokenExplant,$value['explant_id']);
        }
        $q = TcInitOfExplant::select('tc_init_of_bottle_id')
            ->whereIn('id',$brokenExplant)
            ->groupBy('tc_init_of_bottle_id')
            ->get();
        $brokenBottle = [];
        foreach ($q as $key => $value) {
            array_push($brokenBottle,$value->tc_init_of_bottle_id);
        }
        $return = $brokenBottle;
        return $return;
    }

    // =============================================================form observation
    public static function countOfCallus($initId,$explantId,$obsId){
        $dateObs = TcObservation::select('date_obs')
            ->where('id',$obsId)
            ->get()
            ->first()
            ->getAttribute('date_obs');
        $return = TcObsDetail::select('tc_init_of_explant_id')
            ->where('tc_init_of_explant_id', $explantId)
            ->where('result',1)
            ->with('tc_observations')
            ->whereHas('tc_observations',function($q) use($initId,$dateObs){
                $q->where('tc_init_id',$initId)
                    ->where('status',1)
                    ->where('date_obs','<',$dateObs);
            })
            ->get()
            ->count();
        return $return;
    }
    public static function checkExplantResult($obsId,$explantId){
        $q = TcObsDetail::where('tc_observation_id',$obsId)
            ->where('tc_init_of_explant_id',$explantId)
            ->get();
        $return['result'] = null;
        $return['contaminate_id'] = null;
        if(count($q) != 0){
            $return['result'] = $q->first()->result;
            $return['contaminate_id'] = null;
            if($return['result']==3){
                $return['contaminate_id'] = $q->first()->tc_contamination_id;
            }
        }

        return $return;
    }
    public static function getBrokenBottle($obsId,$initId){
        $qExplant = TcObsDetail::select('tc_init_of_explant_id','result')
            ->where('tc_observation_id','<',$obsId)
            ->where(function($q){
                $q->where('result',3)
                    ->orWhere('result',2);
            })
            ->whereHas('tc_observations',function($q) use($initId,$obsId){
                $q->where('tc_init_id',$initId)
                    ->where('status',1)
                    ->where('id','!=',$obsId);
            })
            ->get();
        $aryDieExplant = [];
        foreach ($qExplant as $key => $value) {
            $push['explant_id'] = $value->tc_init_of_explant_id;
            $push['result'] = $value->result;
            array_push($aryDieExplant,$push);
        }

        $return = TcObsDetail::getBottleIndexByExplant($aryDieExplant);
        return $return;
    }
    public static function resultOfBrokenExplant($obsId,$initId,$explantId){
        $qExplant = TcObsDetail::select('tc_init_of_explant_id','result','tc_contamination_id')
            ->where('tc_init_of_explant_id',$explantId)
            ->where(function($q){
                $q->where('result',3)
                    ->orWhere('result',2);
            })
            ->whereHas('tc_observations',function($q) use($initId,$obsId){
                $q->where('tc_init_id',$initId)
                    ->where('status',1)
                    ->where('id','!=',$obsId);
            })
            ->get();
        return $qExplant;
    }

    // callus transfer
    public static function getAllCallusBottle($initId){
        
    }
}

