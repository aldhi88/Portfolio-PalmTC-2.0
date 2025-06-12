<?php

use App\Models\TcObservation;
use App\Models\TcSample;
use Carbon\Carbon;

// function isReminder(){
//     $reminder = false;
//     $q = TcSample::select('id','sample_number')
//         ->with([
//             'tc_inits:id,tc_sample_id'
//         ])
//         ->whereHas('tc_inits')
//         ->orderBy('sample_number','desc')
//         ->get();
//     ;
    
//     if(count($q) != 0){
//         $initActive = $q->first()->tc_inits->id;
//         $q = TcObservation::where('tc_init_id',$initActive)
//             ->where('status',0)
//             ->get();
//         if(count($q)!=0){
//             $dateSchedule = Carbon::parse($q->first()->date_schedule);
//             $dateRemaider = Carbon::parse($dateSchedule)->subDay(7);
//             $diffDays = $dateSchedule->diff($dateRemaider)->days;
//             $now = Carbon::now();
//             $diff = $dateRemaider->diff($now)->days;
//             if($diff <= $diffDays){
//                 $reminder = true;
//             }
//         }
//     }
//     return $reminder;
// }