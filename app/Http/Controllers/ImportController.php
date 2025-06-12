<?php

namespace App\Http\Controllers;

use App\Imports\AclimImport;
use App\Imports\CallusImport;
use App\Imports\EmbryoImport;
use App\Imports\FieldImport;
use App\Imports\GerminImport;
use App\Imports\HardenImport;
use App\Imports\InitsImport;
use App\Imports\LiquidImport;
use App\Imports\MaturImport;
use App\Imports\NurImport;
use App\Imports\RootingImport;
use App\Imports\SampleForImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    // public function indexSample()
    // {
    //     return view('modules.import.sample');
    // }
    public function sampleExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_sample.xlsx'));
    }
    public function sampleImport(Request $request)
    {
        Excel::import(new SampleForImport, $request->file);

        if(SampleForImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.SampleForImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }
    // ====================

    public function initsExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_init.xlsx'));
    }
    public function initsImport(Request $request)
    {
        Excel::import(new InitsImport, $request->file);
        if(InitsImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.InitsImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== CALLUS

    public function callusExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_callus.xlsx'));
    }
    public function callusImport(Request $request)
    {
        Excel::import(new CallusImport, $request->file);
        if(CallusImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.CallusImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Embryo

    public function embryoExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_embryo.xlsx'));
    }
    public function embryoImport(Request $request)
    {
        Excel::import(new EmbryoImport, $request->file);
        if(EmbryoImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.EmbryoImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Liquid

    public function liquidExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_liquid.xlsx'));
    }
    public function liquidImport(Request $request)
    {
        Excel::import(new LiquidImport, $request->file);
        if(LiquidImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.LiquidImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Maturation

    public function maturExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_matur.xlsx'));
    }
    public function maturImport(Request $request)
    {
        Excel::import(new MaturImport, $request->file);
        if(MaturImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.MaturImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Germin

    public function germinExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_germin.xlsx'));
    }
    public function germinImport(Request $request)
    {
        Excel::import(new GerminImport, $request->file);
        if(GerminImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.GerminImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Rooting
    public function rootingExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_rooting.xlsx'));
    }
    public function rootingImport(Request $request)
    {
        Excel::import(new RootingImport, $request->file);
        if(RootingImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.RootingImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Aclimatization
    public function aclimExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_aclim.xlsx'));
    }
    public function aclimImport(Request $request)
    {
        Excel::import(new AclimImport, $request->file);
        if(AclimImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.AclimImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Hardening
    public function hardenExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_harden.xlsx'));
    }
    public function hardenImport(Request $request)
    {
        Excel::import(new HardenImport, $request->file);
        if(HardenImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.HardenImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }
    // ==================== Nursery
    public function nurExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_nur.xlsx'));
    }
    public function nurImport(Request $request)
    {
        Excel::import(new NurImport, $request->file);
        if(NurImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.NurImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }

    // ==================== Field
    public function fieldExport()
    {
        return response()->download(storage_path('/app/public/form_import/form_import_field.xlsx'));
    }
    public function fieldImport(Request $request)
    {
        Excel::import(new FieldImport, $request->file);
        if(FieldImport::$error != false){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'type' => 'danger',
                    'icon' => 'times',
                    'el' => 'alert-area',
                    'msg' => 'Import Error, '.FieldImport::$error,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been imported.',
            ],
        ]);
    }
}
