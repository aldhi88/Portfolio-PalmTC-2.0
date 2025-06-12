<?php

if (!function_exists('getAlpha')) {
    function getAlpha($alpha){
        foreach( range('A', 'Z') as $elements) {
            $aryAlpha[] = $elements;
        }
        foreach( range('A', 'Z') as $elements) {
            foreach( range('A', 'Z') as $elements2) {
                $aryAlpha[] = $elements.$elements2;
            }
        }
        foreach( range('A', 'Z') as $elements) {
            foreach( range('A', 'Z') as $elements2) {
                foreach( range('A', 'Z') as $elements3) {
                    $aryAlpha[] = $elements.$elements2.$elements3;
                }
            }
        }

        $indexNow = array_search($alpha,$aryAlpha);
        return $aryAlpha[$indexNow+1];
    }
}

if (!function_exists('alert')) {
    function alert($type,$msg,$area){
        if($type == 0){
            $msg = is_null($msg)?'Failed, the process has not been done.':$msg;
            $area = is_null($area)?'alert-area':$area;
            $status = 'error';
            $type = 'danger';
            $icon = 'times';
        }else{
            $msg = is_null($msg)?'Success, the process has been done.':$msg;
            $area = is_null($area)?'alert-area':$area;
            $status = 'success';
            $type = 'success';
            $icon = 'check';
        }
        return response()->json([
            'status' => $status,
            'data' => ['type' => $type,'icon' => $icon,'el' => $area,'msg' => $msg]
        ]);
    }
}



