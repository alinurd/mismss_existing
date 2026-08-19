<style>
    .services {
        padding: 10px;
        border: 1px solid black;
    }
    .orderNum{
        margin-left:7px;
    }
    .detail-control{
        width:20px;
        height:20px;
        cursor: pointer;
    }
    .hidden-child{
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
    }
    .shown-child{
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 m-100">
                <h1>Shipment List Packer</h1>
            </div>
            <div class="col m-100">
                <ul class="nav nav-pills float-right" id="navChooseCust" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link left-nav active pointlink" id="IND" data-toggle="tab" role="tab">Individual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav pointlink" id="COR" data-toggle="tab" role="tab">Corporate</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- <div class="card card-daftar">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group" style="text-align:center;margin-bottom:0">
                            <label for="daftar">Pilih Tipe Customer!</label>
                            <div class="btn-daftar mt-2">
                                <button type="button" id="IND" class="btn-noselect btn-select">Individual</button>
                                <button type="button" id="COR" class="btn-noselect ml-2">Corporate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row row-filter">
            <div class="col m-100">
                <ul class="nav nav-pills" id="navTabOrder">
                    <li class="nav-item">
                        <a class="nav-link left-nav active" id="nav-tab-list" data-toggle="tab" href="#nav-list" role="tab">Shipment List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav" id="nav-tab-checked" data-toggle="tab" href="#nav-checked" role="tab">Checked</a>
                    </li>
                </ul>
            </div>
            <div class="col m-100">
                <div class="row row-filter">
                    <div class="col-6 w-filter" style="padding-left:2px;padding-right:2px">
                        <input type="text" class="form-control" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Shipment" autocomplete="off" readonly>
                    </div>
                    <div class="col fit-btn" style="padding-left:2px;padding-right:2px">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle btn-view"
                                    type="button" id="dropdownMenu1" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <a id="view" class="dropdown-item" href="#!"><i class="fas fa-search"></i> Cari</a>
                                <a id="reset" class="dropdown-item" href="#!"><i class="fas fa-sync-alt"> Reset</i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4 card-table">
            <div class="card-body">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="nav-list" role="tabpanel">
                        <table class="table table-striped" id="table-list">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Resi Tracking</th>
                                    <th>No. Invoice</th>
                                    <th>User Update</th>
                                    <th>Detail Customer</th>
                                    <th>Resi LN</th>
                                    <th>Jumlah</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-checked" role="tabpanel">
                        <table class="table table-striped" id="table-checked">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkAll"></th>
                                    <th>Resi Tracking</th>
                                    <th>No. Invoice</th>
                                    <th>User Update</th>
                                    <th>Tanggal Check</th>
                                    <th>Detail Customer</th>
                                    <th>Resi LN</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="resiLnModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document"  style="max-width:1000px">
                <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row row-resiln">
                                <div class="col">
                                    <div class="card card-resiln mt-2">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Resi Luar Negeri</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-catatan">
                                <div class="col">
                                    <div class="card card-catatan mt-2">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Catatan</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class='row'>
                                                <div class='col' style='padding-top:6px'>
                                                    <div class='text'></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </div>
        </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<!-- <script>
    wareH = "{{$warehouse}}";
    addServ = "{{$additional}}";
    auth = "{{Auth::user()->shiplist_printout_resi}}";
</script>
<script src="{{url('assets/customs/js/shiplist-main.js?v='.env('APP_VERSION'))}}"></script> -->
<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-packer.js?v='.env('APP_VERSION'))}}"></script>
<!-- <script src="{{url('assets/customs/js/shiplist-order.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-invoice.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-resi.js?v='.env('APP_VERSION'))}}"></script> -->