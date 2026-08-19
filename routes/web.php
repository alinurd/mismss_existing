<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\WareController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BackupNewController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WebFormController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DokuController;
use App\Http\Controllers\PackerListController;
use App\Http\Controllers\TroubleshootController;
use App\Http\Controllers\JobhistoryController;
use App\Http\Controllers\ShiptripController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\VoidController;
use App\Http\Controllers\ApiJneController;
use App\Http\Controllers\ApiSentralKargoController;
use App\Http\Controllers\AnnouncerController;
use App\Http\Middleware\Verify;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/auth', [LoginController::class, 'auth']);
Route::get('/login', [LoginController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () {

    Route::controller(AppController::class)->group(function () {
        Route::get('/', 'index')->name('app.index');
        Route::get('/check/getcustlist', 'getCustList')->name('app.getcustlist');
        Route::get('/check/getcustlistdisc', 'getCustListDisc')->name('app.getcustlistdisc');
        Route::get('/check/getservlist', 'getServList')->name('app.getservlist');
        Route::get('/check/getservdata', 'getServData')->name('app.getservdata');
        Route::get('/check/getcustdata', 'getCustData')->name('app.getcustdata');
        Route::get('/check/getuomlist', 'getUomList')->name('app.getuomlist');
        Route::get('/check/getshipnum', 'getShipNum')->name('app.getshipnum');
        Route::get('/check/getshipid', 'getShipId')->name('app.getshipid');
        Route::get('/check/getdriverlist', 'getDriverList')->name('app.getdriverlist');
        Route::get('/check/checkphoneandemail', 'checkPhoneAndEmail')->name('app.checkphoneandemail');
        Route::get('/check/invoicelinkdoku', 'invoiceLinkDoku')->name('app.checkinvoicelinkdoku');
        Route::get('/check/getshipnumfrominvoice', 'getShipNumFromInvoice')->name('app.getshipnumfrominvoice');
        Route::post('/dashboard', 'dashboard')->name('dashboard.index');
        Route::get('/load/dashboard', 'loadDashboard')->name('app.loadDashboard');
        Route::get('/import/example/invoice', 'importExampleInvoice')->name('app.importExampleInvoice');
        Route::get('/import/example/shipping/{id}', 'importExampleShipping')->name('app.importExampleShipping');
    });

    Route::controller(ShiptripController::class)->group(function () {
        Route::post('/newship', 'newShipPage')->name('newship.newshippage');
        Route::post('/newship/buat', 'createShipment')->name('newship.createshipment');
        Route::get('/newship/createtrackid', 'createTrackId')->name('newship.createtrackid');
        Route::post('/shiptrip', 'shipTripPage')->name('shiptrip.shiptrippage');
        Route::get('/shiptrip/check/primarytrackid', 'checkPrimaryTrack')->name('shiptrip.checkprimarytrack');
        Route::get('/shiptrip/check/foreigntrackid', 'checkForeignTrack')->name('shiptrip.checkforeigntrack');
        Route::get('/shiptrip/check/resiln', 'checkResiLn')->name('shiptrip.checkresiln');

        Route::get('/shiptrip/check/shipmentedittrack', 'checkShipmentEditTrack')->name('shiptrip.checkshipmentedittrack');
        Route::get('/shiptrip/check/shipmentdate', 'checkShipmentDate')->name('shiptrip.checkshipmentdate');
        Route::get('/shiptrip/check/shipmentdatenow', 'checkShipmentDateNow')->name('shiptrip.checkshipmentdatenow');
        Route::get('/shiptrip/check/shipmentdatehournow', 'checkShipmentDateHourNow')->name('shiptrip.checkshipmentdatehournow');

        Route::get('/shiptrip/table/{navShip}/{navType}/{custTypeId}', 'shipTripTable')->name('shiptrip.table');
        Route::post('/shiptrip/hapus/shipment', 'hapusShipment')->name('shiptrip.hapusshipment');
        Route::post('/shiptrip/edit/shipment', 'editShipment')->name('shiptrip.editshipment');
        Route::post('/shiptrip/edit/note', 'editNote')->name('shiptrip.editnote');
        Route::post('/shiptrip/update/shipment', 'updateShipment')->name('shiptrip.updateshipment');
        Route::post('/shiptrip/update/endpoint', 'updateEndpoint')->name('shiptrip.updateendpoint');
        Route::get('/shiptrip/create/shipment/secondary', 'createShipmentSecondary')->name('shiptrip.createshipmentsecondary');

        Route::get('/shiptrip/tracking', 'trackShipment')->name('shiptrip.trackshipment');
        Route::post('/shiptrip/tracking/admin', [ShiptripController::class, 'trackShipmentAdmin']);
        // Route::get('/shiptrip/tracking/system', 'trackShipmentSystem')->name('shiptrip.trackshipmentsystem');
        // Route::get('/shiptrip/tracking/system/change', 'trackShipmentSystemChange')->name('shiptrip.trackshipmentsystemchange');
        
        Route::get('/shiptrip/warehouse/list', 'warehouseList')->name('shiptrip.warehouselist');
    });

    Route::controller(AnnouncerController::class)->group(function(){
        Route::post('/announcer/update', 'updateAnnouncer')->name('announcer.update');
    });

    Route::controller(DiskonController::class)->group(function(){
        Route::post('/diskonlist', 'index')->name('diskon.index');
        Route::get('/check/gettotaldisc', 'getTotalDisc')->name('diskon.gettotaldisc');
        Route::get('/diskonlist/table/{custTypeId}', 'table')->name('diskon.table');
    });

    Route::controller(ShipmentController::class)->group(function () {
        Route::get('/shiplist/table/order/{custTypeId}', 'tableOrder')->name('shiplist.tableorder');
        Route::post('/shiplist/hapus/order', 'hapusOrder')->name('shiplist.hapusorder');

        Route::post('/shiplist', 'shiplist')->name('shiplist.index');

        // Route::get('/shiplist/buat/invoice', 'buatInvoice')->name('shiplist.buatinvoice');
        Route::post('/shiplist/buat/invoice/queue', 'buatInvoiceQueue')->name('shiplist.buatinvoicequeue');
        Route::get('/shiplist/table/invoice/{custTypeId}', 'tableInvoice')->name('shiplist.tableinvoice');
        Route::post('/shiplist/edit/invoice', 'editInvoice')->name('shiplist.editinvoice');
        Route::get('/shiplist/edit/detilcons', 'editDetilCons')->name('shiplist.editdetilcons');
        Route::get('/shiplist/getdata/invoice', 'editInvoiceGetData')->name('shiplist.editinvoicegetdata');
        Route::post('/shiplist/hapus/invoice', 'hapusInvoice')->name('shiplist.hapusinvoice');
        Route::get('/shiplist/pindah/status', 'updateInvoiceStatus')->name('shiplist.updateinvoicestatus');

        Route::get('/shiplist/buat/invoice/additional', 'buatInvoiceAdditional')->name('shiplist.buatinvoiceadditional');
        Route::post('/shiplist/edit/invoice/additional', 'editInvoiceAdditional')->name('shiplist.editinvoiceadditional');

        Route::post('/shiplist/buat/resi', 'buatResi')->name('shiplist.buatresi');
        Route::post('/shiplist/edit/resi', 'editResi')->name('shiplist.editresi');
        Route::get('/shiplist/edit/order/status', 'editOrderStatus')->name('shiplist.editorderstatus');
        Route::get('/shiplist/table/tracking/{custTypeId}', 'tableTracking')->name('shiplist.tabletracking');
        Route::post('/check/getdatalist', 'getDataList')->name('shiplist.getdatalist');
        
        Route::get('/printout/invoice/{id}', 'printOutInvoice')->name('shiplist.printoutinvoice');
        Route::get('/printout/resi/{id}', 'printOutResi')->name('shiplist.printoutresi');
        Route::post('/check/lastshippingid', 'lastShippingId')->name('shiplist.lastshippingid');
        Route::get('/foreignrate', 'foreignRate')->name('shiplist.foreignrate');
        Route::post('/import', 'import')->name('shiplist.import');
        Route::get('/shiplist/resend', 'resend')->name('shiplist.resend');
        
        Route::get('/shiplist/warehouse/list', 'warehouseList')->name('shiplist.warehouselist');

        Route::post('/shiplist/changeto/corporate', 'changeToCor')->name('shiplist.changetocor');
    });

    Route::controller(PackerListController::class)->group(function () {
        Route::get('/shiplist/input/packlist', 'inputPacker')->name('shiplist.inputpacker');
        Route::get('/shiplist/table/packlist/{custTypeId}', 'tablePackerList')->name('shiplist.tablepackerlist');
        Route::get('/shiplist/table/packcheck/{custTypeId}', 'tablePackerChecked')->name('shiplist.tablepackerchecked');
    });
    
    Route::controller(VoidController::class)->group(function () {
        Route::post('/void', 'index')->name('void.index');
        Route::post('/void/create', 'create')->name('void.create');
        Route::get('/void/table/{custTypeId}', 'table')->name('voidlist.table');
        Route::get('/void/history/{id}', 'history')->name('voidlist.history');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::post('/custlist', 'index')->name('customer.index');
        Route::post('/custlist/submitref', 'submitref')->name('customer.submitref');
        Route::post('/custlist/tambah', 'tambah')->name('customer.tambah');
        Route::post('/custlist/edit', 'edit')->name('customer.edit');
        Route::post('/custlist/hapus', 'hapus')->name('customer.hapus');
        Route::post('/custlist/checkref', 'checkref')->name('customer.checkref');
        Route::get('/custlist/table/{custTypeId}', 'table')->name('customer.table');
        Route::get('/custlist/export/{custType}', 'export')->name('customer.export');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::post('/servlist', 'index')->name('service.index');
        Route::post('/servlist/tambah', 'tambah')->name('service.tambah');
        Route::post('/servlist/edit', 'edit')->name('service.edit');
        Route::post('/servlist/hapus', 'hapus')->name('service.hapus');
        Route::get('/servlist/table', 'table')->name('service.table');
        Route::get('/servlist/namechecking', 'nameChecking')->name('warehouse.namechecking');
        Route::get('/servlist/export', 'export')->name('service.export');
    });

    Route::controller(WareController::class)->group(function () {
        Route::post('/warehouse', 'index')->name('warehouse.index');
        Route::post('/warehouse/tambah', 'tambah')->name('warehouse.tambah');
        Route::post('/warehouse/edit', 'edit')->name('warehouse.edit');
        Route::post('/warehouse/hapus', 'hapus')->name('warehouse.hapus');
        Route::get('/warehouse/table', 'table')->name('warehouse.table');
        Route::get('/warehouse/inputchecking', 'inputChecking')->name('warehouse.inputchecking');
        Route::get('/warehouse/export', 'export')->name('warehouse.export');
        Route::get('/warehouse/get/list', 'getList')->name('warehouse.getlist');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::post('/profile', 'index')->name('profile.index');
        Route::get('/profile/gantipass', 'gantipass')->name('profile.gantipass');
    });

    Route::controller(BackupController::class)->group(function () {
        Route::post('/backup', 'index')->name('backup.index');
        Route::get('/backup/export', 'export')->name('backup.export');
        Route::get('/backup/exportpacker', 'exportPacker')->name('backup.exportpacker');
        Route::get('/backup/exportops', 'exportops')->name('backup.exportops');
    });

    Route::controller(BackupNewController::class)->group(function () {
        Route::get('/backup/new/create/export', 'newCreateExport')->name('backup.newcreateexport');
        Route::get('/backup/new/download/export', 'exportDownload')->name('backup.exportdownload');
    });

    Route::controller(HistoryController::class)->group(function () {
        Route::post('/history', 'index')->name('history.index');
        Route::get('/history/list/{id}', 'list')->name('history.list');
        Route::get('/history/table', 'table')->name('history.table');
    });

    Route::controller(TroubleshootController::class)->group(function () {
        Route::post('/troubleshoot', 'index')->name('troubleshoot.index');
    });

    Route::controller(JobhistoryController::class)->group(function () {
        Route::post('/jobhistory', 'index')->name('jobhistory.index');
    });

    Route::controller(ToolController::class)->group(function () {
        Route::post('/tool/bulky', 'indexBulky')->name('tool.bulkyindex');
        Route::get('/tool/bulky/input', 'inputBulky')->name('tool.inputbulky');
    });

    Route::controller(DokuController::class)->group(function(){
        Route::get('/doku/checking/invoice', 'checkDoku')->name('doku.checkinvoice');
    });

    Route::controller(WebhookController::class)->group(function(){
        Route::get('/webhook/table', 'table')->name('webhook.table');
        Route::get('/webhook/list/{id}', 'list')->name('webhook.list');
    });

    Route::get('/dev8th/testing/sendwa', [TestController::class, 'sendWa']);
    Route::get('/dev8th/testing/checkdoku', [TestController::class, 'checkDoku']);
    // Route::get('/dev8th/testing/reqdoku', [TestController::class, 'reqDoku']);
    // Route::get('/dev8th/testing/revinv', [TestController::class, 'revInv']);
    // Route::get('/dev8th/testing/timeout', [TestController::class, 'testTimeOut']);
    // Route::get('/test/printout/invoice/{id}', [TestController::class, 'printOutInvoice']);
    // Route::get('/dev8th/inserttrack', [TestController::class, 'insertTracking']);
    // Route::get('/dev8th/checkdoku', [TestController::class, 'checkDoku']);
    // Route::get('/dev8th/testdoku', [TestController::class, 'testDoku']);
    // Route::get('/dev8th/testnominal', [TestController::class, 'nominal']);
    // Route::get('/dev8th/checkoutmanual', [TestController::class, 'checkOutManual']);
    
    // Route::get('/sandbox', [TestController::class, 'sandbox']);
    // Route::get('/test/sendwa', [TestController::class, 'sendWa']);
    // Route::get('/dev8th/testing/updatetime', [TestController::class, 'updateTime']);
    // Route::get('/dev8th/input/order/queue', [TestController::class, 'orderqueue']);
});

Route::get('/d/g', [DriverController::class, 'logout']);
Route::get('/d/l', [DriverController::class, 'loginDriver']);
Route::post('/d/l/a', [DriverController::class, 'authDriver']);
Route::get('/d/h', [DriverController::class, 'index']);
Route::post('/d/m/l', [DriverController::class, 'list']);
Route::get('/d/m/l/t', [DriverController::class, 'table']);
Route::post('/d/m/l/u', [DriverController::class, 'updateStatus']);
Route::post('/d/m/l/u/q', [DriverController::class, 'qrCodeChecking']);
Route::post('/d/m/e', [DriverController::class, 'export']);
Route::post('/d/m/e/a', [BackupController::class, 'exportDriverStepOne']);
Route::get('/d/m/e/a/{random}', [BackupController::class, 'exportDriver']);
Route::post('/d/m/q', [DriverController::class, 'qrcode']);
Route::get('/d/p/g/', [ProfileController::class, 'gantipass']);

Route::get('/check/session', [AppController::class, 'checkSes'])->name('app.checkses');
Route::get('/check/expired', [AppController::class, 'checkExpired'])->name('app.checkexpired');
Route::get('/payment/{link}', [DokuController::class, 'index']);
Route::get('/c/k/l', [DokuController::class, 'checkLink']);
Route::post('/dokunotif', [DokuController::class, 'notification']);
Route::get('/encrypt/{pass}', [AppController::class, 'encrypt'])->name('app.encrypt');
Route::get('/p/{invoiceLink}', [ShipmentController::class, 'printOutInvoiceForCustomer'])->name('shiplist.printoutinvoice');
Route::controller(WebFormController::class)->group(function () {
    Route::get('/webform', 'index')->name('webform.index');
    Route::get('/webform/input', 'input')->name('webform.input');
});
Route::get('/404',function(){
    abort(404);
});

Route::get('/tracking/shipment', [ShiptripController::class, 'trackShipmentDirect']);
Route::get('/tracking/shipment/customer', [ShiptripController::class, 'trackShipmentCustomer']);
Route::get('/tracking/shipment/customer/select', [ShiptripController::class, 'trackShipmentCustomerSelect']);
Route::get('/shiptrip/tracking/system', [ShiptripController::class, 'trackShipmentSystem']);
Route::get('/shiptrip/tracking/system/change', [ShiptripController::class, 'trackShipmentSystemChange']);


Route::domain('invoice.mismasslogistic.com')->group(function(){
    Route::get('/', function(){
        return "Ini Adalah Halaman Invoice";
    });
});

Route::domain('pay.mismasslogistic.com')->group(function(){
    Route::get('/', function(){
        return "Ini Adalah Halaman Payment";
    });
});
// Route::get('/p/{invoiceLink}', [ShipmentController::class, 'printOutInvoiceForCustomer'])->domain('print.' . env('APP_URL'))->name('shiplist.printoutinvoice');
