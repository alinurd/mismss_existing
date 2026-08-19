@php
    $yearLaunch = intVal(env("APP_LAUNCH_YEAR"));
    $yearNow = intVal(date("Y"));
    $yearLength = $yearNow-$yearLaunch;
    $yearLoop = $yearLaunch;
    
    $p=0;
    Auth::user()->dashboard_pendapatan_board>0?$p++:'';
    Auth::user()->dashboard_paid_board>0?$p++:'';
    $colBoard2 = 12 / ($p==0?1:$p);

    $n=0;
    Auth::user()->dashboard_berat_board>0?$n++:'';
    Auth::user()->dashboard_cust_board>0?$n++:'';
    Auth::user()->dashboard_cbm_board>0?$n++:'';
    Auth::user()->dashboard_diskon_board>0?$n++:'';
    $colBoard = 12 / ($n==0?1:$n);

    $z=0;
    Auth::user()->dashboard_berat_chart>0?$z++:'';
    Auth::user()->dashboard_pendapatan_chart>0?$z++:'';
    $colChart = 12 / ($z==0?1:$z);
@endphp
<style>
    canvas {
        min-height: 250px;
        height: 250px;
        max-height: 250px;
        max-width: 100%;
    }
    #titleTotalBeratCust,
    #titleTotalPendapatan{
        display:inline;
    }
    .small-box-footer{
        font-weight:700;
    }
    
    @media (max-width:500px) {
        .col-lg-3.col-6 {
            min-width: 100% !important;
        }
    }
    
    .form-group {
        margin-bottom: 0px !important;
    }
    
    .font-size-expand {
        font-size: 24px !important;
    }
    
    .icon-expand {
        font-size: 35px !important;
    }
</style>

<section class="content-header pb-md-2 pb-1">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-4 col-12">
                <h1>Dashboard</h1>
                <div class="filterBy">Filter By Tanggal Create Invoice</div>
            </div>
            <div class="col">
                <ul class="nav nav-pills float-right mt-2" id="types">
                    <li class="nav-item">
                        <a class="nav-link left-nav active" id="F" data-toggle="tab" href="#nav-finance" role="tab">Finance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav" id="T" data-toggle="tab" href="#nav-tracking" role="tab">Tracking</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12 mt-md-0 mt-2 px-0">
                <div class="form-group">
                    <select id="filterCountry" class="form-control">
                        <option value="">ALL COUNTRY</option>
                        @foreach ($country as $c)
                        <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mt-md-0 mt-2 pl-md-2 px-0">
                <div class="form-group">
                    <select id="filterWarehouse" class="form-control">
                        <option value="">ALL WAREHOUSE</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mt-md-0 mt-2 pl-md-2 px-0">
                <input type="text" id="filterTanggal" name="filterTanggal" placeholder="Filter Tanggal" class="form-control" readonly />
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="finance-section">
            <div class="row">
                @if(Auth::user()->dashboard_pendapatan_board)
                <div class="col-lg-{{$colBoard2}} col-sm-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalPendapatan"></h3>
                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalPendapatanInd"></div>
                            <div id="totalPendapatanCor"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_paid_board)
                <div class="col-lg-{{$colBoard2}} col-sm-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalPaid"></h3>
                            <p>Total PAID</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cash-register" style="color: rgb(0,0,0,0.2);"></i>
                        </div>
                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                            <div id="totalUnpaid"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                @if(Auth::user()->dashboard_pendapatan_board)
                <div class="col-lg-{{$colBoard2}} col-sm-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalPendapatanForeign"></h3>
                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalPendapatanForeignInd"></div>
                            <div id="totalPendapatanForeignCor"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_paid_board)
                <div class="col-lg-{{$colBoard2}} col-sm-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalPaidForeign"></h3>
                            <p>Total PAID</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cash-register" style="color: rgb(0,0,0,0.2);"></i>
                        </div>
                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                            <div id="totalUnpaidForeign"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                @if(Auth::user()->dashboard_diskon_board)
                    <div class="col-lg-{{$colBoard2}} col-sm-12">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="totalDiskonRp"></h3>
                                <p>Total Diskon Dalam Rupiah</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave" style="color: rgb(255,255,255,0.2);"></i>
                            </div>
                            <div class="small-box-footer">
                                <div id="totalDiskonIndRp"></div>
                                <div id="totalDiskonCorRp"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-{{$colBoard2}} col-sm-12">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="totalDiskonSGD"></h3>
                                <p>Total Diskon Dalam SGD</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cash-register" style="color: rgb(0,0,0,0.2);"></i>
                            </div>
                            <div class="small-box-footer" style="color:black">
                                <div id="totalDiskonIndSGD"></div>
                                <div id="totalDiskonCorSGD"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                @if(Auth::user()->dashboard_berat_board)
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalBerat"></h3>
                            <p>Total Berat</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-weight-hanging" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalBeratInd"></div>
                            <div id="totalBeratCor"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_cbm_board)
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-orange" style='color:#fff!important'>
                        <div class="inner">
                            <h3 id="totalCbm"></h3>
                            <p>Total CBM</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box-open" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalCbmInd"></div>
                            <div id="totalCbmCor"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_cust_board)
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalCustomer"></h3>
                            <p>Total Customer</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-friends" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalCustomerInd"></div>
                            <div id="totalCustomerCor"></div>
                        </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_diskon_board)
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-cyan">
                        <div class="inner">
                            <h3 id="totalDiskon"></h3>
                            <p>Total Diskon</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags" style="color: rgb(0,0,0,0.2);"></i>
                        </div>
                        <a class="small-box-footer" href="#" data-id="diskonlist" link="{{url('/diskonlist')}}">
                            <div id="totalDiskonInd"></div>
                            <div id="totalDiskonCor"></div>
                        </a>
                    </div>
                </div>
                @endif
            </div>
                @if(Auth::user()->dashboard_berat_chart)
                <div class="row">
                    <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-0 bg-gradient-info">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-th"></i>
                                </div>
                                <div>
                                    <div>Total Berat & Customer</div>
                                    <div id="titleTotalBeratCust"></div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body card-body-chart card-body-beratCustChart">
                        </div>
                    </div>
                    </div>
                </div>
                @endif
                @if(Auth::user()->dashboard_pendapatan_chart)
                <div class="row">
                    <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-0 bg-gradient-warning">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-th"></i>
                                </div>
                                <div>
                                    <div>Total Pendapatan</div>
                                    <div id="titleTotalPendapatan"><div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-warning btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body card-body-chart card-body-pendapatanChart">
                        </div>
                    </div>
                    </div>
                </div>
                @else
                    <div class="card-body card-body-chart card-body-pendapatanChart" style="display:none">
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                    <div class="card collapsed-card">
                        <div class="card-header border-0 bg-gradient-info">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div>
                                    <div>Total Import</div>
                                    <div id='totalImport'></div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display:none">
                                            <div class="row">
                                                @if(Auth::user()->dashboard_pendapatan_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalPendapatanImport" class="font-size-expand"></h3>
                                                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalPendapatanIndImport"></div>
                                                            <div id="totalPendapatanCorImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(Auth::user()->dashboard_paid_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalPaidImport" class="font-size-expand"></h3>
                                                            <p>Total PAID</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                                                            <div id="totalUnpaidImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                        </div>
                                        <div class="row">
                                                @if(Auth::user()->dashboard_pendapatan_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalPendapatanForeignImport" class="font-size-expand"></h3>
                                                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalPendapatanForeignIndImport"></div>
                                                            <div id="totalPendapatanForeignCorImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(Auth::user()->dashboard_paid_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalPaidForeignImport" class="font-size-expand"></h3>
                                                            <p>Total PAID</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                                                            <div id="totalUnpaidForeignImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_diskon_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalDiskonRpImport" class="font-size-expand"></h3>
                                                            <p>Total Diskon Dalam Rupiah</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalDiskonIndRpImport"></div>
                                                            <div id="totalDiskonCorRpImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalDiskonSGDImport" class="font-size-expand"></h3>
                                                            <p>Total Diskon Dalam SGD</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="color:black">
                                                            <div id="totalDiskonIndSGDImport"></div>
                                                            <div id="totalDiskonCorSGDImport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_berat_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-danger">
                                                    <div class="inner">
                                                        <h3 id="totalBeratImport" class="font-size-expand"></h3>
                                                        <p>Total Berat</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-weight-hanging icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalBeratIndImport"></div>
                                                        <div id="totalBeratCorImport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if(Auth::user()->dashboard_cbm_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-orange" style='color:#fff!important'>
                                                    <div class="inner">
                                                        <h3 id="totalCbmImport" class="font-size-expand"></h3>
                                                        <p>Total CBM</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-box-open icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalCbmIndImport"></div>
                                                        <div id="totalCbmCorImport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_cust_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-success">
                                                    <div class="inner">
                                                        <h3 id="totalCustomerImport" class="font-size-expand"></h3>
                                                        <p>Total Customer</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-user-friends icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalCustomerIndImport"></div>
                                                        <div id="totalCustomerCorImport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if(Auth::user()->dashboard_diskon_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-cyan">
                                                    <div class="inner">
                                                        <h3 id="totalDiskonImport" class="font-size-expand"></h3>
                                                        <p>Total Diskon</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-tags icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                    </div>
                                                    <a class="small-box-footer" href="#" data-id="diskonlist" link="{{url('/diskonlist')}}">
                                                        <div id="totalDiskonIndImport"></div>
                                                        <div id="totalDiskonCorImport"></div>
                                                    </a>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="card collapsed-card">
                        <div class="card-header border-0 bg-gradient-danger">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-upload"></i>
                                </div>
                                <div>
                                    <div>Total Export</div>
                                    <div id='totalExport'></div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-danger btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display:none">
                                            <div class="row">
                                                @if(Auth::user()->dashboard_pendapatan_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalPendapatanExport" class="font-size-expand"></h3>
                                                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalPendapatanIndExport"></div>
                                                            <div id="totalPendapatanCorExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(Auth::user()->dashboard_paid_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalPaidExport" class="font-size-expand"></h3>
                                                            <p>Total PAID</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                                                            <div id="totalUnpaidExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                        </div>
                                        <div class="row">
                                                @if(Auth::user()->dashboard_pendapatan_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalPendapatanForeignExport" class="font-size-expand"></h3>
                                                            <p>Total Pendapatan (sudah di kurangi diskon)</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalPendapatanForeignIndExport"></div>
                                                            <div id="totalPendapatanForeignCorExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(Auth::user()->dashboard_paid_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalPaidForeignExport" class="font-size-expand"></h3>
                                                            <p>Total PAID</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="padding:15px 10px;color:black">
                                                            <div id="totalUnpaidForeignExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_diskon_board)
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h3 id="totalDiskonRpExport" class="font-size-expand"></h3>
                                                            <p>Total Diskon Dalam Rupiah</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-money-bill-wave icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer">
                                                            <div id="totalDiskonIndRpExport"></div>
                                                            <div id="totalDiskonCorRpExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-{{$colBoard2}} col-sm-12">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h3 id="totalDiskonSGDExport" class="font-size-expand"></h3>
                                                            <p>Total Diskon Dalam SGD</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-cash-register icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                        </div>
                                                        <div class="small-box-footer" style="color:black">
                                                            <div id="totalDiskonIndSGDExport"></div>
                                                            <div id="totalDiskonCorSGDExport"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_berat_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-danger">
                                                    <div class="inner">
                                                        <h3 id="totalBeratExport" class="font-size-expand"></h3>
                                                        <p>Total Berat</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-weight-hanging icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalBeratIndExport"></div>
                                                        <div id="totalBeratCorExport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if(Auth::user()->dashboard_cbm_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-orange" style='color:#fff!important'>
                                                    <div class="inner">
                                                        <h3 id="totalCbmExport" class="font-size-expand"></h3>
                                                        <p>Total CBM</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-box-open icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalCbmIndExport"></div>
                                                        <div id="totalCbmCorExport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            @if(Auth::user()->dashboard_cust_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-success">
                                                    <div class="inner">
                                                        <h3 id="totalCustomerExport" class="font-size-expand"></h3>
                                                        <p>Total Customer</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-user-friends icon-expand" style="color: rgb(255,255,255,0.2);"></i>
                                                    </div>
                                                    <div class="small-box-footer">
                                                        <div id="totalCustomerIndExport"></div>
                                                        <div id="totalCustomerCorExport"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if(Auth::user()->dashboard_diskon_board)
                                            <div class="col-lg-{{$colBoard2}} col-sm-12 col-md-6">
                                                <div class="small-box bg-cyan">
                                                    <div class="inner">
                                                        <h3 id="totalDiskonExport" class="font-size-expand"></h3>
                                                        <p>Total Diskon</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-tags icon-expand" style="color: rgb(0,0,0,0.2);"></i>
                                                    </div>
                                                    <a class="small-box-footer" href="#" data-id="diskonlist" link="{{url('/diskonlist')}}">
                                                        <div id="totalDiskonIndExport"></div>
                                                        <div id="totalDiskonCorExport"></div>
                                                    </a>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                        </div>
                    </div>
                    </div>
                </div>
        </div>
        <div class="tracking-section" style="display:none">
            <div class="row">
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-cyan">
                        <div class="inner">
                            <h3 id="totalShipment"></h3>
                            <p>Total Shipment (Resi Tracking)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-qrcode" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalShipmentInd"></div>
                            <div id="totalShipmentCor"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-success" style='color:#fff!important'>
                        <div class="inner">
                            <h3 id="totalSukses"></h3>
                            <p>Total Sukses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-double" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalSuksesInd"></div>
                            <div id="totalSuksesCor"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3 id="totalProses"></h3>
                            <p>Total Proses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck" style="color: rgb(255,255,255,0.2);"></i>
                        </div>
                        <div class="small-box-footer">
                            <div id="totalProsesInd"></div>
                            <div id="totalProsesCor"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-{{$colBoard}} col-sm-12 col-md-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalRedline"></h3>
                            <p>Total Redline</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-retweet" style="color: rgb(0,0,0,0.2);"></i>
                        </div>
                        <a class="small-box-footer">
                            <div id="totalRedlineInd"></div>
                            <div id="totalRedlineCor"></div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-0 bg-gradient-info">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-th"></i>
                                </div>
                                <div>
                                    <div>Total Shipment & Redline</div>
                                    <div id="titleTotalShipRedline"><div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body card-body-chart card-body-shipRedlineChart">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-0 bg-gradient-warning">
                            <h3 class="card-title d-flex">
                                <div class="mr-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-th"></i>
                                </div>
                                <div>
                                    <div>Total Proses & Sukses</div>
                                    <div id="titleTotalProSuccess"><div>
                                </div>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-warning btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body card-body-chart card-body-proSuccessChart">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>
<script>
    var paidStat = {{Auth::user()->dashboard_paid_board}};
</script>
<script src="{{url('assets/customs/js/dashboard-new.js?v='.env('APP_VERSION'))}}"></script>