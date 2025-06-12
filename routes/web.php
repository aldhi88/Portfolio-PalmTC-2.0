<?php

use App\Http\Controllers\AclimListController;
use App\Http\Controllers\AclimObController;
use App\Http\Controllers\AclimTransferController;
use App\Http\Controllers\AgarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BottleController;
use App\Http\Controllers\BottleInitController;
use App\Http\Controllers\CallusListController;
use App\Http\Controllers\CallusObController;
use App\Http\Controllers\CallusTransferController;
use App\Http\Controllers\ContaminationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeathController;
use App\Http\Controllers\EmbryoListController;
use App\Http\Controllers\EmbryoObController;
use App\Http\Controllers\EmbryoTransferController;
use App\Http\Controllers\ExportImportController;
use App\Http\Controllers\FieldListController;
use App\Http\Controllers\FieldObController;
use App\Http\Controllers\GerminListController;
use App\Http\Controllers\GerminObController;
use App\Http\Controllers\GerminTransferController;
use App\Http\Controllers\HardenListController;
use App\Http\Controllers\HardenObController;
use App\Http\Controllers\HardenTransferController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InitController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LaminarController;
use App\Http\Controllers\LiquidListController;
use App\Http\Controllers\LiquidObController;
use App\Http\Controllers\LiquidTransferController;
use App\Http\Controllers\MasterTreefileController;
use App\Http\Controllers\MaturListController;
use App\Http\Controllers\MaturObController;
use App\Http\Controllers\MaturTransferController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\MediumOpnameController;
use App\Http\Controllers\MediumStockController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\NurListController;
use App\Http\Controllers\NurObController;
use App\Http\Controllers\NurTransferController;
use App\Http\Controllers\PlantationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RootingListController;
use App\Http\Controllers\RootingObController;
use App\Http\Controllers\RootingTransferController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TcsWorkerController;
use App\Http\Controllers\TempController;
use App\Http\Controllers\TesController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

Route::get('tes', [TesController::class, 'index']);
Route::get('tes2', [TesController::class, 'tes']);
Route::get('query/{table_name}', [TesController::class, 'query']);

// new route

Route::prefix('bo')->group(function () {
    Route::name('bo.')->group(function () {

        Route::prefix('worker')->group(function () {
            Route::name('worker.')->group(function () {
                Route::controller(TcsWorkerController::class)->group(function () {
                    Route::get('data', 'data')->name('data');
                });
            });
        });

    });
});

// end new rout

//============ Index
Route::get('/', function () {
    return redirect()->route('workers.index');
});
//============ Auth
Route::name('auth.')->group(function () {
    Route::prefix('auth')->group(function(){
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
//============ Dashboard
Route::get('dashboard/index', function () {
    return redirect()->route('workers.index');
});

// Route::name('dashboard.')->group(function () {
//     Route::prefix('dashboard')->group(function(){
//         Route::get('index', [DashboardController::class, 'index'])->name('index');
//     });
// });

// =========== migration
Route::name('migrations.')->group(function () {
    Route::prefix('migrations')->group(function(){
        Route::get('index', [MigrationController::class, 'index'])->name('index');
        Route::get('dtIndex', [MigrationController::class, 'dtIndex'])->name('dtIndex');
        Route::post('update', [MigrationController::class, 'update'])->name('update');
    });
});

//============ Import Data
Route::name('import.')->group(function () {
    Route::prefix('import')->group(function(){
        Route::get('sampleExport', [ImportController::class, 'sampleExport'])->name('sampleExport');
        Route::post('sampleImport', [ImportController::class, 'sampleImport'])->name('sampleImport');
        Route::get('initsExport', [ImportController::class, 'initsExport'])->name('initsExport');
        Route::post('initsImport', [ImportController::class, 'initsImport'])->name('initsImport');
        Route::get('callusExport', [ImportController::class, 'callusExport'])->name('callusExport');
        Route::post('callusImport', [ImportController::class, 'callusImport'])->name('callusImport');
        Route::get('embryoExport', [ImportController::class, 'embryoExport'])->name('embryoExport');
        Route::post('embryoImport', [ImportController::class, 'embryoImport'])->name('embryoImport');
        Route::get('liquidExport', [ImportController::class, 'liquidExport'])->name('liquidExport');
        Route::get('maturExport', [ImportController::class, 'maturExport'])->name('maturExport');
        Route::post('liquidImport', [ImportController::class, 'liquidImport'])->name('liquidImport');
        Route::post('maturImport', [ImportController::class, 'maturImport'])->name('maturImport');
        Route::get('germinExport', [ImportController::class, 'germinExport'])->name('germinExport');
        Route::post('germinImport', [ImportController::class, 'germinImport'])->name('germinImport');
        Route::get('rootingExport', [ImportController::class, 'rootingExport'])->name('rootingExport');
        Route::post('rootingImport', [ImportController::class, 'rootingImport'])->name('rootingImport');
        Route::get('aclimExport', [ImportController::class, 'aclimExport'])->name('aclimExport');
        Route::post('aclimImport', [ImportController::class, 'aclimImport'])->name('aclimImport');
        Route::get('hardenExport', [ImportController::class, 'hardenExport'])->name('hardenExport');
        Route::post('hardenImport', [ImportController::class, 'hardenImport'])->name('hardenImport');
        Route::get('hardenExport', [ImportController::class, 'hardenExport'])->name('hardenExport');
        Route::post('hardenImport', [ImportController::class, 'hardenImport'])->name('hardenImport');
        Route::get('nurExport', [ImportController::class, 'nurExport'])->name('nurExport');
        Route::post('nurImport', [ImportController::class, 'nurImport'])->name('nurImport');
        Route::get('fieldExport', [ImportController::class, 'fieldExport'])->name('fieldExport');
        Route::post('fieldImport', [ImportController::class, 'fieldImport'])->name('fieldImport');
    });
});

//============ Workers
Route::name('workers.')->group(function () {
    Route::prefix('workers')->group(function(){
        Route::get('dt', [WorkerController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [WorkerController::class, 'getData'])->name('getData');
    });
});
Route::resource('workers', WorkerController::class)->except(['create', 'show', 'edit']);
//============ Bottles
Route::name('bottles.')->group(function () {
    Route::prefix('bottles')->group(function(){
        Route::get('dt', [BottleController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [BottleController::class, 'getData'])->name('getData');
    });
});
Route::resource('bottles', BottleController::class)->except(['create', 'show', 'edit']);
// Bottle Init
Route::name('bottle-inits.')->group(function () {
    Route::prefix('bottle-inits')->group(function(){
        Route::get('dtIndex', [BottleInitController::class, 'dtIndex'])->name('dtIndex');
        Route::get('getDataBottle', [BottleInitController::class, 'getDataBottle'])->name('getDataBottle');
        Route::post('actionChecked', [BottleInitController::class, 'actionChecked'])->name('actionChecked');
    });
});
Route::resource('bottle-inits', BottleInitController::class)->except(['create', 'show', 'edit']);
//============ Agars
Route::name('agars.')->group(function () {
    Route::prefix('agars')->group(function(){
        Route::get('dt', [AgarController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [AgarController::class, 'getData'])->name('getData');
    });
});
Route::resource('agars', AgarController::class)->except(['create', 'show', 'edit']);
//============ Mediums
Route::name('mediums.')->group(function () {
    Route::prefix('mediums')->group(function(){
        Route::get('dt', [MediumController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [MediumController::class, 'getData'])->name('getData');
        Route::get('get-history/{id}', [MediumController::class, 'getHistory'])->name('getHistory');
    });
});
Route::resource('mediums', MediumController::class)->except(['create', 'show', 'edit']);
//============ Medium Stocks
Route::name('medium-stocks.')->group(function () {
    Route::prefix('medium-stocks')->group(function(){
        Route::get('dt', [MediumStockController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [MediumStockController::class, 'getData'])->name('getData');
        Route::get('filter/{id}', [MediumStockController::class, 'indexFilter'])->name('indexFilter');
        Route::get('get-history/{id}', [MediumStockController::class, 'getHistory'])->name('getHistory');
        Route::get('create-param/{id}', [MediumStockController::class, 'createParam'])->name('createParam');
    });
});
Route::resource('medium-stocks', MediumStockController::class);
// medium opname
Route::name('medium-validate.')->group(function () {
    Route::prefix('medium-validate')->group(function(){
        Route::get('dt', [MediumOpnameController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [MediumOpnameController::class, 'getData'])->name('getData');
    });
});
Route::resource('medium-validate', MediumOpnameController::class)->except(['create', 'show', 'edit']);
// rooms
Route::name('rooms.')->group(function () {
    Route::prefix('rooms')->group(function(){
        Route::get('dt', [RoomController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [RoomController::class, 'getData'])->name('getData');
    });
});
Route::resource('rooms', RoomController::class)->except(['create', 'show', 'edit']);
// contaminations
Route::name('contaminations.')->group(function () {
    Route::prefix('contaminations')->group(function(){
        Route::get('dt', [ContaminationController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [ContaminationController::class, 'getData'])->name('getData');
    });
});
Route::resource('contaminations', ContaminationController::class)->except(['create', 'show', 'edit']);
// deaths
Route::name('deaths.')->group(function () {
    Route::prefix('deaths')->group(function(){
        Route::get('dt', [DeathController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [DeathController::class, 'getData'])->name('getData');
    });
});
Route::resource('deaths', DeathController::class)->except(['create', 'show', 'edit']);
// treefilesTc
Route::name('treefiles.')->group(function () {
    Route::prefix('treefiles')->group(function(){
        Route::get('dt', [MasterTreefileController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [MasterTreefileController::class, 'getData'])->name('getData');
    });
});
Route::resource('treefiles', MasterTreefileController::class)->except(['create', 'show', 'edit']);
// Laminars
Route::name('laminars.')->group(function () {
    Route::prefix('laminars')->group(function(){
        Route::get('dt', [LaminarController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [LaminarController::class, 'getData'])->name('getData');
    });
});
Route::resource('laminars', LaminarController::class)->except(['create', 'show', 'edit']);
// plantations
Route::name('plantations.')->group(function () {
    Route::prefix('plantations')->group(function(){
        Route::get('dt', [PlantationController::class, 'dt'])->name('dt');
        Route::get('get-data/{id}', [PlantationController::class, 'getData'])->name('getData');
    });
});
Route::resource('plantations', PlantationController::class)->except(['create', 'show', 'edit']);
// Sample
Route::name('samples.')->group(function () {
    Route::prefix('samples')->group(function(){
        Route::get('dt', [SampleController::class, 'dt'])->name('dt');
        Route::get('dtComment', [SampleController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [SampleController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [SampleController::class, 'commentDestroy'])->name('commentDestroy');
        Route::get('get-data/{id}', [SampleController::class, 'getData'])->name('getData');
        Route::get('dt-treefile', [SampleController::class, 'dtTreefile'])->name('dtTreefile');
        Route::get('dt-sample', [SampleController::class, 'dtSample'])->name('dtSample');
        Route::get('get-treefile', [SampleController::class, 'getTreefile'])->name('getTreefile');
        Route::get('get-sample', [SampleController::class, 'getSample'])->name('getSample');
        Route::get('get-sample-number', [SampleController::class, 'getSampleNumb'])->name('getSampleNumb');

        Route::get('exportPDF', [SampleController::class, 'exportPDF'])->name('exportPDF');
        Route::get('exportExcel', [SampleController::class, 'exportExcel'])->name('exportExcel');
        Route::get('exportPrint', [SampleController::class, 'exportPrint'])->name('exportPrint');
    });
});
Route::resource('samples', SampleController::class)->except([]);

// Init
Route::name('inits.')->group(function () {
    Route::prefix('inits')->group(function(){
        Route::get('dt', [InitController::class, 'dt'])->name('dt');
        Route::get('getStep1', [InitController::class, 'getStep1'])->name('getStep1');
        Route::post('submitStep1', [InitController::class, 'submitStep1'])->name('submitStep1');
        Route::post('updateStep1', [InitController::class, 'updateStep1'])->name('updateStep1');
        Route::get('getStep2', [InitController::class, 'getStep2'])->name('getStep2');
        Route::post('addWorker', [InitController::class, 'addWorker'])->name('addWorker');
        Route::delete('delWorker', [InitController::class, 'delWorker'])->name('delWorker');
        Route::post('finishStep2', [InitController::class, 'finishStep2'])->name('finishStep2');
        Route::get('getStep3', [InitController::class, 'getStep3'])->name('getStep3');
        Route::post('addStock', [InitController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [InitController::class, 'delStock'])->name('delStock');
        Route::post('finishStep3', [InitController::class, 'finishStep3'])->name('finishStep3');

        Route::get('indexBottle/{id}', [InitController::class, 'indexBottle'])->name('indexBottle');
        Route::get('indexPrintBottle/{id}', [InitController::class, 'indexPrintBottle'])->name('indexPrintBottle');
        Route::get('dtBottleSummary', [InitController::class, 'dtBottleSummary'])->name('dtBottleSummary');
        Route::get('addBlockOption', [InitController::class, 'addBlockOption'])->name('addBlockOption');
        Route::post('formAddBottleWorker', [InitController::class, 'formAddBottleWorker'])->name('formAddBottleWorker');
        Route::get('dtBottle', [InitController::class, 'dtBottle'])->name('dtBottle');
        Route::post('changeBottleStatus', [InitController::class, 'changeBottleStatus'])->name('changeBottleStatus');

        Route::get('indexPrintBottle/{id}', [InitController::class, 'indexPrintBottle'])->name('indexPrintBottle');
        Route::get('printByBottleNumber', [InitController::class, 'printByBottleNumber'])->name('printByBottleNumber');
        Route::get('printByBlockNumber', [InitController::class, 'printByBlockNumber'])->name('printByBlockNumber');
        Route::get('printByWorker', [InitController::class, 'printByWorker'])->name('printByWorker');
        Route::get('dtPrintByCheck', [InitController::class, 'dtPrintByCheck'])->name('dtPrintByCheck');
        Route::get('checkBottlePrint', [InitController::class, 'checkBottlePrint'])->name('checkBottlePrint');
        Route::get('dataPrintCustom', [InitController::class, 'dataPrintCustom'])->name('dataPrintCustom');
        Route::get('dataPrintCustomUncheckAll', [InitController::class, 'dataPrintCustomUncheckAll'])->name('dataPrintCustomUncheckAll');
        Route::get('checkBeforePrintCheck', [InitController::class, 'checkBeforePrintCheck'])->name('checkBeforePrintCheck');
        Route::get('triggerPrintCheck', [InitController::class, 'triggerPrintCheck'])->name('triggerPrintCheck');

        // show
        Route::get('dtShow', [InitController::class, 'dtShow'])->name('dtShow');

        // delete
        Route::get('getDataDelete', [InitController::class, 'getDataDelete'])->name('getDataDelete');
        Route::post('nonActive', [InitController::class, 'nonActive'])->name('nonActive');
        Route::post('active', [InitController::class, 'active'])->name('active');

        // comment
        Route::get('comment/{id}', [InitController::class, 'comment'])->name('comment');
        Route::get('dtComment', [InitController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [InitController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [InitController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('inits', InitController::class);

// callus ob
Route::name('callus-obs.')->group(function () {
    Route::prefix('callus-obs')->group(function(){
        Route::get('dt', [CallusObController::class, 'dt'])->name('dt');
        Route::get('create/{id}', [CallusObController::class, 'create'])->name('create');
        Route::get('dtBottle', [CallusObController::class, 'dtBottle'])->name('dtBottle');

        Route::post('start-obs', [CallusObController::class, 'startObs'])->name('startObs');
        Route::get('dtDetailObs', [CallusObController::class, 'dtDetailObs'])->name('dtDetailObs');
        Route::get('printObsForm', [CallusObController::class, 'printObsForm'])->name('printObsForm');

        // comment
        Route::get('comment/{id}', [CallusObController::class, 'comment'])->name('comment');
        Route::get('dtComment', [CallusObController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [CallusObController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [CallusObController::class, 'commentDestroy'])->name('commentDestroy');

    });
});
Route::resource('callus-obs', CallusObController::class)->except(['edit','update','destroy','create']);

//============ Callus Transfer
Route::name('callus-transfers.')->group(function () {
    Route::prefix('callus-transfers')->group(function(){
        Route::get('dt', [CallusTransferController::class, 'dt'])->name('dt');

        Route::get('detail/{id}', [CallusTransferController::class, 'detail'])->name('detail');
        Route::get('dtDetailTransfer', [CallusTransferController::class, 'dtDetailTransfer'])->name('dtDetailTransfer');
        Route::get('dtListTransferPerInit', [CallusTransferController::class, 'dtListTransferPerInit'])->name('dtListTransferPerInit');
        Route::get('printBlankForm', [CallusTransferController::class, 'printBlankForm'])->name('printBlankForm');
        Route::get('create/{id}', [CallusTransferController::class, 'create'])->name('create');

        Route::get('dtCallusTransfer', [CallusTransferController::class, 'dtCallusTransfer'])->name('dtCallusTransfer');
        Route::get('dtPickMedStock', [CallusTransferController::class, 'dtPickMedStock'])->name('dtPickMedStock');
        Route::post('storeStock', [CallusTransferController::class, 'storeStock'])->name('storeStock');
        Route::get('dtPickedMedStock', [CallusTransferController::class, 'dtPickedMedStock'])->name('dtPickedMedStock');
        Route::post('deletePickedMedStock', [CallusTransferController::class, 'deletePickedMedStock'])->name('deletePickedMedStock');
        Route::get('getCountMedStock', [CallusTransferController::class, 'getCountMedStock'])->name('getCountMedStock');
        Route::get('setBottleLeft', [CallusTransferController::class, 'setBottleLeft'])->name('setBottleLeft');
        Route::post('delTransfer', [CallusTransferController::class, 'delTransfer'])->name('delTransfer');
        // print label
        Route::get('indexPrintLabel/{obsId}', [CallusTransferController::class, 'indexPrintLabel'])->name('indexPrintLabel');
        Route::get('printByBottleNumber', [CallusTransferController::class, 'printByBottleNumber'])->name('printByBottleNumber');
        Route::get('printByWorker', [CallusTransferController::class, 'printByWorker'])->name('printByWorker');
        Route::get('dtPrintByCheck', [CallusTransferController::class, 'dtPrintByCheck'])->name('dtPrintByCheck');
        Route::get('checkBottlePrint', [CallusTransferController::class, 'checkBottlePrint'])->name('checkBottlePrint');
        Route::get('dataPrintCustom', [CallusTransferController::class, 'dataPrintCustom'])->name('dataPrintCustom');
        Route::get('dataPrintCustomUncheckAll', [CallusTransferController::class, 'dataPrintCustomUncheckAll'])->name('dataPrintCustomUncheckAll');
        Route::get('triggerPrintCheck', [CallusTransferController::class, 'triggerPrintCheck'])->name('triggerPrintCheck');
        Route::get('dataPrintCustom', [CallusTransferController::class, 'dataPrintCustom'])->name('dataPrintCustom');
        Route::get('getDateList', [CallusTransferController::class, 'getDateList'])->name('getDateList');
        Route::get('printByGroup', [CallusTransferController::class, 'printByGroup'])->name('printByGroup');
        Route::get('printByTransfer', [CallusTransferController::class, 'printByTransfer'])->name('printByTransfer');

    });
});
Route::resource('callus-transfers', CallusTransferController::class)->except(['create']);
// callus list
Route::name('callus-lists.')->group(function () {
    Route::prefix('callus-lists')->group(function(){
        Route::get('dt', [CallusListController::class, 'dt'])->name('dt');
        Route::get('exportPrint', [CallusListController::class, 'exportPrint'])->name('exportPrint');
        Route::get('exportExcel', [CallusListController::class, 'exportExcel'])->name('exportExcel');
        Route::get('exportPDF', [CallusListController::class, 'exportPDF'])->name('exportPDF');
    });
});
Route::resource('callus-lists', CallusListController::class)->except(['create','store','show','edit','update','destroy']);

// Embriogenesis
Route::name('embryo-obs.')->group(function () {
    Route::prefix('embryo-obs')->group(function(){
        Route::get('dt', [EmbryoObController::class, 'dt'])->name('dt');
        Route::get('printObsForm', [EmbryoObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dtShow', [EmbryoObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [EmbryoObController::class, 'dtShow2'])->name('dtShow2');
        Route::get('create/{id}', [EmbryoObController::class, 'create'])->name('create');
        Route::get('dtCreate', [EmbryoObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [EmbryoObController::class, 'storeObDetail'])->name('storeObDetail');
    });
});
Route::resource('embryo-obs', EmbryoObController::class)->except(['create','edit','update']);

// Embriogenesis List
Route::name('embryo-lists.')->group(function () {
    Route::prefix('embryo-lists')->group(function(){
        Route::get('dt', [EmbryoListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [EmbryoListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [EmbryoListController::class, 'dtShow2'])->name('dtShow2');
        Route::post('storeSubtraction', [EmbryoListController::class, 'storeSubtraction'])->name('storeSubtraction');
        Route::get('showSubtraction', [EmbryoListController::class, 'showSubtraction'])->name('showSubtraction');
        Route::delete('destroySubtraction', [EmbryoListController::class, 'destroySubtraction'])->name('destroySubtraction');
        // Route::get('dtListBottle', [EmbryoListController::class, 'dtListBottle'])->name('dtListBottle');

        // comment
        Route::get('comment/{id}', [EmbryoListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [EmbryoListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [EmbryoListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [EmbryoListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('embryo-lists', EmbryoListController::class)->except(['create','store','edit','update','destroy']);

// Embryo Transfer
Route::name('embryo-transfers.')->group(function () {
    Route::prefix('embryo-transfers')->group(function(){
        Route::get('dtIndex', [EmbryoTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [EmbryoTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [EmbryoTransferController::class, 'dtShow2'])->name('dtShow2');

        Route::get('create/{id}', [EmbryoTransferController::class, 'create'])->name('create');
        Route::get('getStep1', [EmbryoTransferController::class, 'getStep1'])->name('getStep1');
        Route::post('finishStep1', [EmbryoTransferController::class, 'finishStep1'])->name('finishStep1');

        Route::get('getStep2', [EmbryoTransferController::class, 'getStep2'])->name('getStep2');
        Route::post('addItemStep2', [EmbryoTransferController::class, 'addItemStep2'])->name('addItemStep2');
        Route::delete('delItemStep2', [EmbryoTransferController::class, 'delItemStep2'])->name('delItemStep2');
        Route::post('finishStep2', [EmbryoTransferController::class, 'finishStep2'])->name('finishStep2');

        Route::get('getStep3', [EmbryoTransferController::class, 'getStep3'])->name('getStep3');
        Route::get('getMedStock', [EmbryoTransferController::class, 'getMedStock'])->name('getMedStock');
        Route::post('addStock', [EmbryoTransferController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [EmbryoTransferController::class, 'delStock'])->name('delStock');
        Route::get('closeModalStock', [EmbryoTransferController::class, 'closeModalStock'])->name('closeModalStock');
        Route::post('finishStep3', [EmbryoTransferController::class, 'finishStep3'])->name('finishStep3');

        Route::get('getStep4', [EmbryoTransferController::class, 'getStep4'])->name('getStep4');
        Route::post('finishStep4', [EmbryoTransferController::class, 'finishStep4'])->name('finishStep4');
        Route::post('finishTransfer', [EmbryoTransferController::class, 'finishTransfer'])->name('finishTransfer');

        Route::get('parsingDataDel/{id}', [EmbryoTransferController::class, 'parsingDataDel'])->name('parsingDataDel');

        Route::get('printByTransfer', [EmbryoTransferController::class, 'printByTransfer'])->name('printByTransfer');
        Route::get('printBlankForm', [EmbryoTransferController::class, 'printBlankForm'])->name('printBlankForm');
        Route::get('printBlankForm2', [EmbryoTransferController::class, 'printBlankForm2'])->name('printBlankForm2');

    });
});
Route::resource('embryo-transfers', EmbryoTransferController::class)->except(['create','store','edit','update']);

// Liquid List
Route::name('liquid-lists.')->group(function () {
    Route::prefix('liquid-lists')->group(function(){
        Route::get('dt', [LiquidListController::class, 'dt'])->name('dt');

        Route::get('dtShow', [LiquidListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [LiquidListController::class, 'dtShow2'])->name('dtShow2');

        // comment
        Route::get('comment/{id}', [LiquidListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [LiquidListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [LiquidListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [LiquidListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('liquid-lists', LiquidListController::class)->except(['create','store','edit','update','destroy']);
// Liquid Ob
Route::name('liquid-obs.')->group(function () {
    Route::prefix('liquid-obs')->group(function(){
        Route::get('printObsForm', [LiquidObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [LiquidObController::class, 'dt'])->name('dt');

        Route::get('create/{initId}', [LiquidObController::class, 'create'])->name('create');
        Route::get('dtCreate', [LiquidObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [LiquidObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [LiquidObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [LiquidObController::class, 'dtShow2'])->name('dtShow2');
    });
});
Route::resource('liquid-obs', LiquidObController::class)->except(['create','edit','update']);
// Liquid Transfer
Route::name('liquid-transfers.')->group(function () {
    Route::prefix('liquid-transfers')->group(function(){
        Route::get('dtIndex', [LiquidTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [LiquidTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [LiquidTransferController::class, 'dtShow2'])->name('dtShow2');

        Route::get('create/{id}', [LiquidTransferController::class, 'create'])->name('create');
        Route::get('getStep1', [LiquidTransferController::class, 'getStep1'])->name('getStep1');
        Route::post('finishStep1', [LiquidTransferController::class, 'finishStep1'])->name('finishStep1');

        Route::get('getStep2', [LiquidTransferController::class, 'getStep2'])->name('getStep2');
        Route::post('addItemStep2', [LiquidTransferController::class, 'addItemStep2'])->name('addItemStep2');
        Route::delete('delItemStep2', [LiquidTransferController::class, 'delItemStep2'])->name('delItemStep2');
        Route::post('finishStep2', [LiquidTransferController::class, 'finishStep2'])->name('finishStep2');

        Route::get('getStep3', [LiquidTransferController::class, 'getStep3'])->name('getStep3');
        Route::get('getMedStock', [LiquidTransferController::class, 'getMedStock'])->name('getMedStock');
        Route::post('addStock', [LiquidTransferController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [LiquidTransferController::class, 'delStock'])->name('delStock');
        Route::get('closeModalStock', [LiquidTransferController::class, 'closeModalStock'])->name('closeModalStock');
        Route::post('finishStep3', [LiquidTransferController::class, 'finishStep3'])->name('finishStep3');

        Route::get('getStep4', [LiquidTransferController::class, 'getStep4'])->name('getStep4');
        Route::post('finishStep4', [LiquidTransferController::class, 'finishStep4'])->name('finishStep4');
        Route::post('finishTransfer', [LiquidTransferController::class, 'finishTransfer'])->name('finishTransfer');

        Route::get('parsingDataDel/{id}', [LiquidTransferController::class, 'parsingDataDel'])->name('parsingDataDel');

        Route::get('printByTransfer', [LiquidTransferController::class, 'printByTransfer'])->name('printByTransfer');


    });
});
Route::resource('liquid-transfers', LiquidTransferController::class)->except(['create','store','edit','update']);

// Matur List
Route::name('matur-lists.')->group(function () {
    Route::prefix('matur-lists')->group(function(){
        Route::get('dt', [MaturListController::class, 'dt'])->name('dt');

        Route::get('dtShow', [MaturListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [MaturListController::class, 'dtShow2'])->name('dtShow2');

        // comment
        Route::get('comment/{id}', [MaturListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [MaturListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [MaturListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [MaturListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('matur-lists', MaturListController::class)->except(['create','store','edit','update','destroy']);
// Matur Ob
Route::name('matur-obs.')->group(function () {
    Route::prefix('matur-obs')->group(function(){
        Route::get('printObsForm', [MaturObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [MaturObController::class, 'dt'])->name('dt');

        Route::get('create/{initId}', [MaturObController::class, 'create'])->name('create');
        Route::get('dtCreate', [MaturObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [MaturObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [MaturObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [MaturObController::class, 'dtShow2'])->name('dtShow2');
    });
});
Route::resource('matur-obs', MaturObController::class)->except(['create','edit','update']);
// Matur Transfer
Route::name('matur-transfers.')->group(function () {
    Route::prefix('matur-transfers')->group(function(){
        Route::get('dtIndex', [MaturTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [MaturTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [MaturTransferController::class, 'dtShow2'])->name('dtShow2');

        Route::get('create/{id}', [MaturTransferController::class, 'create'])->name('create');
        Route::get('getStep1', [MaturTransferController::class, 'getStep1'])->name('getStep1');
        Route::post('finishStep1', [MaturTransferController::class, 'finishStep1'])->name('finishStep1');

        Route::get('getStep2', [MaturTransferController::class, 'getStep2'])->name('getStep2');
        Route::post('addItemStep2', [MaturTransferController::class, 'addItemStep2'])->name('addItemStep2');
        Route::delete('delItemStep2', [MaturTransferController::class, 'delItemStep2'])->name('delItemStep2');
        Route::post('finishStep2', [MaturTransferController::class, 'finishStep2'])->name('finishStep2');

        Route::get('getStep3', [MaturTransferController::class, 'getStep3'])->name('getStep3');
        Route::get('getMedStock', [MaturTransferController::class, 'getMedStock'])->name('getMedStock');
        Route::post('addStock', [MaturTransferController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [MaturTransferController::class, 'delStock'])->name('delStock');
        Route::get('closeModalStock', [MaturTransferController::class, 'closeModalStock'])->name('closeModalStock');
        Route::post('finishStep3', [MaturTransferController::class, 'finishStep3'])->name('finishStep3');

        Route::get('getStep4', [MaturTransferController::class, 'getStep4'])->name('getStep4');
        Route::post('finishStep4', [MaturTransferController::class, 'finishStep4'])->name('finishStep4');
        Route::post('finishTransfer', [MaturTransferController::class, 'finishTransfer'])->name('finishTransfer');

        Route::get('parsingDataDel/{id}', [MaturTransferController::class, 'parsingDataDel'])->name('parsingDataDel');

        Route::get('printByTransfer', [MaturTransferController::class, 'printByTransfer'])->name('printByTransfer');


    });
});
Route::resource('matur-transfers', MaturTransferController::class)->except(['create','store','edit','update']);

// Germin List
Route::name('germin-lists.')->group(function () {
    Route::prefix('germin-lists')->group(function(){
        Route::get('dt', [GerminListController::class, 'dt'])->name('dt');

        Route::get('dtShow', [GerminListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [GerminListController::class, 'dtShow2'])->name('dtShow2');

        // comment
        Route::get('comment/{id}', [GerminListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [GerminListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [GerminListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [GerminListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('germin-lists', GerminListController::class)->except(['create','store','edit','update','destroy']);
// Germin Ob
Route::name('germin-obs.')->group(function () {
    Route::prefix('germin-obs')->group(function(){
        Route::get('printObsForm', [GerminObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [GerminObController::class, 'dt'])->name('dt');

        Route::get('create/{initId}', [GerminObController::class, 'create'])->name('create');
        Route::get('dtCreate', [GerminObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [GerminObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [GerminObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [GerminObController::class, 'dtShow2'])->name('dtShow2');
    });
});
Route::resource('germin-obs', GerminObController::class)->except(['create','edit','update']);
// Germin Transfer
Route::name('germin-transfers.')->group(function () {
    Route::prefix('germin-transfers')->group(function(){
        Route::get('dtIndex', [GerminTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [GerminTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [GerminTransferController::class, 'dtShow2'])->name('dtShow2');

        Route::get('create/{id}', [GerminTransferController::class, 'create'])->name('create');
        Route::get('getStep1', [GerminTransferController::class, 'getStep1'])->name('getStep1');
        Route::post('finishStep1', [GerminTransferController::class, 'finishStep1'])->name('finishStep1');

        Route::get('getStep2', [GerminTransferController::class, 'getStep2'])->name('getStep2');
        Route::post('addItemStep2', [GerminTransferController::class, 'addItemStep2'])->name('addItemStep2');
        Route::delete('delItemStep2', [GerminTransferController::class, 'delItemStep2'])->name('delItemStep2');
        Route::post('finishStep2', [GerminTransferController::class, 'finishStep2'])->name('finishStep2');

        Route::get('getStep3', [GerminTransferController::class, 'getStep3'])->name('getStep3');
        Route::get('getMedStock', [GerminTransferController::class, 'getMedStock'])->name('getMedStock');
        Route::post('addStock', [GerminTransferController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [GerminTransferController::class, 'delStock'])->name('delStock');
        Route::get('closeModalStock', [GerminTransferController::class, 'closeModalStock'])->name('closeModalStock');
        Route::post('finishStep3', [GerminTransferController::class, 'finishStep3'])->name('finishStep3');

        Route::get('getStep4', [GerminTransferController::class, 'getStep4'])->name('getStep4');
        Route::post('finishStep4', [GerminTransferController::class, 'finishStep4'])->name('finishStep4');
        Route::post('finishTransfer', [GerminTransferController::class, 'finishTransfer'])->name('finishTransfer');

        Route::get('parsingDataDel/{id}', [GerminTransferController::class, 'parsingDataDel'])->name('parsingDataDel');

        Route::get('printByTransfer', [GerminTransferController::class, 'printByTransfer'])->name('printByTransfer');


    });
});
Route::resource('germin-transfers', GerminTransferController::class)->except(['create','store','edit','update']);

// Rooting List
Route::name('rooting-lists.')->group(function () {
    Route::prefix('rooting-lists')->group(function(){
        Route::get('dt', [RootingListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [RootingListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [RootingListController::class, 'dtShow2'])->name('dtShow2');

        // comment
        Route::get('comment/{id}', [RootingListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [RootingListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [RootingListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [RootingListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('rooting-lists', RootingListController::class)->except(['create','store','edit','update','destroy']);
// Rooting Ob
Route::name('rooting-obs.')->group(function () {
    Route::prefix('rooting-obs')->group(function(){
        Route::get('printObsForm', [RootingObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [RootingObController::class, 'dt'])->name('dt');

        Route::get('create/{initId}', [RootingObController::class, 'create'])->name('create');
        Route::get('dtCreate', [RootingObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [RootingObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [RootingObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [RootingObController::class, 'dtShow2'])->name('dtShow2');
    });
});
Route::resource('rooting-obs', RootingObController::class)->except(['create','edit','update']);
// Rooting Transfer
Route::name('rooting-transfers.')->group(function () {
    Route::prefix('rooting-transfers')->group(function(){
        Route::get('dtIndex', [RootingTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [RootingTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [RootingTransferController::class, 'dtShow2'])->name('dtShow2');

        Route::get('create/{id}', [RootingTransferController::class, 'create'])->name('create');
        Route::get('getStep1', [RootingTransferController::class, 'getStep1'])->name('getStep1');
        Route::post('finishStep1', [RootingTransferController::class, 'finishStep1'])->name('finishStep1');

        Route::get('getStep2', [RootingTransferController::class, 'getStep2'])->name('getStep2');
        Route::post('addItemStep2', [RootingTransferController::class, 'addItemStep2'])->name('addItemStep2');
        Route::delete('delItemStep2', [RootingTransferController::class, 'delItemStep2'])->name('delItemStep2');
        Route::post('finishStep2', [RootingTransferController::class, 'finishStep2'])->name('finishStep2');

        Route::get('getStep3', [RootingTransferController::class, 'getStep3'])->name('getStep3');
        Route::get('getMedStock', [RootingTransferController::class, 'getMedStock'])->name('getMedStock');
        Route::post('addStock', [RootingTransferController::class, 'addStock'])->name('addStock');
        Route::delete('delStock', [RootingTransferController::class, 'delStock'])->name('delStock');
        Route::get('closeModalStock', [RootingTransferController::class, 'closeModalStock'])->name('closeModalStock');
        Route::post('finishStep3', [RootingTransferController::class, 'finishStep3'])->name('finishStep3');

        Route::get('getStep4', [RootingTransferController::class, 'getStep4'])->name('getStep4');
        Route::post('finishStep4', [RootingTransferController::class, 'finishStep4'])->name('finishStep4');
        Route::post('finishTransfer', [RootingTransferController::class, 'finishTransfer'])->name('finishTransfer');

        Route::get('parsingDataDel/{id}', [RootingTransferController::class, 'parsingDataDel'])->name('parsingDataDel');

        Route::get('printByTransfer', [RootingTransferController::class, 'printByTransfer'])->name('printByTransfer');
        Route::get('printPlant', [RootingTransferController::class, 'printPlant'])->name('printPlant');


    });
});
Route::resource('rooting-transfers', RootingTransferController::class)->except(['create','store','edit','update']);

// Aclim List
Route::name('aclim-lists.')->group(function () {
    Route::prefix('aclim-lists')->group(function(){
        Route::get('dt', [AclimListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [AclimListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [AclimListController::class, 'dtShow2'])->name('dtShow2');
        Route::post('changeStatus', [AclimListController::class, 'changeStatus'])->name('changeStatus');
        Route::post('changeSkorAkar', [AclimListController::class, 'changeSkorAkar'])->name('changeSkorAkar');

        // comment
        Route::get('comment/{id}', [AclimListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [AclimListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [AclimListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [AclimListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('aclim-lists', AclimListController::class)->except(['create','store','edit','update','destroy']);
// Aclim Ob
Route::name('aclim-obs.')->group(function () {
    Route::prefix('aclim-obs')->group(function(){
        Route::get('printObsForm', [AclimObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [AclimObController::class, 'dt'])->name('dt');

        Route::get('create/{obsId}', [AclimObController::class, 'create'])->name('create');
        Route::get('dtCreate', [AclimObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [AclimObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [AclimObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [AclimObController::class, 'dtShow2'])->name('dtShow2');
        Route::get('dtObs', [AclimObController::class, 'dtObs'])->name('dtObs');
        Route::post('storeDetail', [AclimObController::class, 'storeDetail'])->name('storeDetail');
        Route::post('storeDetailAll', [AclimObController::class, 'storeDetailAll'])->name('storeDetailAll');
    });
});
Route::resource('aclim-obs', AclimObController::class)->except(['create','edit','update']);
// Aclim Transfer
Route::name('aclim-transfers.')->group(function () {
    Route::prefix('aclim-transfers')->group(function(){
        Route::get('dtIndex', [AclimTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [AclimTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [AclimTransferController::class, 'dtShow2'])->name('dtShow2');
        Route::get('create/{id}', [AclimTransferController::class, 'create'])->name('create');
        Route::get('printLabel', [AclimTransferController::class, 'printLabel'])->name('printLabel');

    });
});
Route::resource('aclim-transfers', AclimTransferController::class)->except(['edit','update','create']);

// Harden List
Route::name('harden-lists.')->group(function () {
    Route::prefix('harden-lists')->group(function(){
        Route::get('dt', [HardenListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [HardenListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [HardenListController::class, 'dtShow2'])->name('dtShow2');
        Route::post('changeStatus', [HardenListController::class, 'changeStatus'])->name('changeStatus');
        Route::post('changeSkorAkar', [HardenListController::class, 'changeSkorAkar'])->name('changeSkorAkar');

        // comment
        Route::get('comment/{id}', [HardenListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [HardenListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [HardenListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [HardenListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('harden-lists', HardenListController::class)->except(['create','store','edit','update','destroy']);
// Harden Ob
Route::name('harden-obs.')->group(function () {
    Route::prefix('harden-obs')->group(function(){
        Route::get('printObsForm', [HardenObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [HardenObController::class, 'dt'])->name('dt');

        Route::get('create/{obsId}', [HardenObController::class, 'create'])->name('create');
        Route::get('dtCreate', [HardenObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [HardenObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [HardenObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [HardenObController::class, 'dtShow2'])->name('dtShow2');
        Route::get('dtObs', [HardenObController::class, 'dtObs'])->name('dtObs');
        Route::post('storeDetail', [HardenObController::class, 'storeDetail'])->name('storeDetail');
        Route::post('storeDetailAll', [HardenObController::class, 'storeDetailAll'])->name('storeDetailAll');
    });
});
Route::resource('harden-obs', HardenObController::class)->except(['create','edit','update']);
// Harden Transfer
Route::name('harden-transfers.')->group(function () {
    Route::prefix('harden-transfers')->group(function(){
        Route::get('dtIndex', [HardenTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [HardenTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [HardenTransferController::class, 'dtShow2'])->name('dtShow2');
        Route::get('create/{id}', [HardenTransferController::class, 'create'])->name('create');
        Route::get('printLabel', [HardenTransferController::class, 'printLabel'])->name('printLabel');

    });
});
Route::resource('harden-transfers', HardenTransferController::class)->except(['edit','update','create']);

// Nur List
Route::name('nur-lists.')->group(function () {
    Route::prefix('nur-lists')->group(function(){
        Route::get('dt', [NurListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [NurListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [NurListController::class, 'dtShow2'])->name('dtShow2');
        Route::get('dtShow3', [NurListController::class, 'dtShow3'])->name('dtShow3');
        Route::post('changeStatus', [NurListController::class, 'changeStatus'])->name('changeStatus');
        Route::post('changeSkorAkar', [NurListController::class, 'changeSkorAkar'])->name('changeSkorAkar');

        // comment
        Route::get('comment/{id}', [NurListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [NurListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [NurListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [NurListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('nur-lists', NurListController::class)->except(['create','store','edit','update','destroy']);
// Nur Ob
Route::name('nur-obs.')->group(function () {
    Route::prefix('nur-obs')->group(function(){
        Route::get('printObsForm', [NurObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [NurObController::class, 'dt'])->name('dt');

        Route::get('create/{obsId}', [NurObController::class, 'create'])->name('create');
        Route::get('dtCreate', [NurObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [NurObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [NurObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [NurObController::class, 'dtShow2'])->name('dtShow2');
        Route::get('dtObs', [NurObController::class, 'dtObs'])->name('dtObs');
        Route::post('storeDetail', [NurObController::class, 'storeDetail'])->name('storeDetail');
        Route::post('storeDetailAll', [NurObController::class, 'storeDetailAll'])->name('storeDetailAll');
    });
});
Route::resource('nur-obs', NurObController::class)->except(['create','edit','update']);
// Nur Transfer
Route::name('nur-transfers.')->group(function () {
    Route::prefix('nur-transfers')->group(function(){
        Route::get('dtIndex', [NurTransferController::class, 'dtIndex'])->name('dtIndex');
        Route::get('dtShow', [NurTransferController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [NurTransferController::class, 'dtShow2'])->name('dtShow2');
        Route::get('create/{id}', [NurTransferController::class, 'create'])->name('create');
        Route::get('printLabel', [NurTransferController::class, 'printLabel'])->name('printLabel');

    });
});
Route::resource('nur-transfers', NurTransferController::class)->except(['edit','update','create']);

// Field List
Route::name('field-lists.')->group(function () {
    Route::prefix('field-lists')->group(function(){
        Route::get('dt', [FieldListController::class, 'dt'])->name('dt');
        Route::get('dtShow', [FieldListController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [FieldListController::class, 'dtShow2'])->name('dtShow2');
        Route::post('changeStatus', [FieldListController::class, 'changeStatus'])->name('changeStatus');
        Route::post('changeSkorAkar', [FieldListController::class, 'changeSkorAkar'])->name('changeSkorAkar');

        // comment
        Route::get('comment/{id}', [FieldListController::class, 'comment'])->name('comment');
        Route::get('dtComment', [FieldListController::class, 'dtComment'])->name('dtComment');
        Route::post('comment/store', [FieldListController::class, 'commentStore'])->name('commentStore');
        Route::delete('comment/destroy', [FieldListController::class, 'commentDestroy'])->name('commentDestroy');
    });
});
Route::resource('field-lists', FieldListController::class)->except(['create','store','edit','update','destroy']);
// Field Ob
Route::name('field-obs.')->group(function () {
    Route::prefix('field-obs')->group(function(){
        Route::get('printObsForm', [FieldObController::class, 'printObsForm'])->name('printObsForm');
        Route::get('dt', [FieldObController::class, 'dt'])->name('dt');

        Route::get('create/{obsId}', [FieldObController::class, 'create'])->name('create');
        Route::get('dtCreate', [FieldObController::class, 'dtCreate'])->name('dtCreate');
        Route::post('storeObDetail', [FieldObController::class, 'storeObDetail'])->name('storeObDetail');

        Route::get('dtShow', [FieldObController::class, 'dtShow'])->name('dtShow');
        Route::get('dtShow2', [FieldObController::class, 'dtShow2'])->name('dtShow2');
        Route::get('dtObs', [FieldObController::class, 'dtObs'])->name('dtObs');
        Route::post('storeDetail', [FieldObController::class, 'storeDetail'])->name('storeDetail');
        Route::post('storeDetailAll', [FieldObController::class, 'storeDetailAll'])->name('storeDetailAll');
    });
});
Route::resource('field-obs', FieldObController::class)->except(['create','edit','update']);

// Export Import
Route::name('export-imports.')->group(function () {
    Route::prefix('export-imports')->group(function(){
        // Route::get('printObsForm', [ExportImportController::class, 'printObsForm'])->name('printObsForm');
    });
});
Route::resource('export-imports', ExportImportController::class)->except(['create','edit','update']);

// Schedules
Route::name('schedules.')->group(function () {
    Route::prefix('schedules')->group(function(){
        // Route::get('printObsForm', [ExportImportController::class, 'printObsForm'])->name('printObsForm');
    });
});
Route::resource('schedules', ScheduleController::class)->except(['create','edit','update']);

// Reports
Route::name('reports.')->group(function () {
    Route::prefix('reports')->group(function(){
        // Route::get('printObsForm', [ExportImportController::class, 'printObsForm'])->name('printObsForm');
    });
});
Route::resource('reports', ReportController::class)->except(['create','edit','update']);

// temperaturs
Route::name('temps.')->group(function () {
    Route::prefix('temps')->group(function(){
        // Route::get('printObsForm', [ExportImportController::class, 'printObsForm'])->name('printObsForm');
    });
});
Route::resource('temps', TempController::class)->except(['create','edit','update']);

// labels
Route::name('labels.')->group(function () {
    Route::prefix('labels')->group(function(){
        // Route::get('printObsForm', [ExportImportController::class, 'printObsForm'])->name('printObsForm');
    });
});
Route::resource('labels', LabelController::class)->except(['create','edit','update']);

