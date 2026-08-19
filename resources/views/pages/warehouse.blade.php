<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3 m-100">
                <h1>Warehouse List</h1>
            </div>
            <div class="col-sm-6 m-100">
                <ol class="breadcrumb float-sm-right">
                    @if(Auth::user()->warelist_export)
                    <a id="exportBtn" href="{{url('warehouse/export')}}" target="_blank" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</a>
                    @endif
                    <!-- <button type="button" id="kelola-btn" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</button> -->
                    @if(Auth::user()->warelist_buat)
                    <button type="button" id="tambahBtn" class="btn bg-gradient-success"><i class="fas fa-user-plus"></i> Add Warehouse</button>
                    @endif
                </ol>
            </div>
        </div>
        <div class="row" style="row-gap: 0px !important;column-gap: 8px !important;">
            <div class="col-2 m-100" style="padding-left:0px;padding-right:0px;">
                <select name="filterCountry" id="filterCountry" class="form-control" style="width:100%">
                    <option value="">ALL COUNTRY</option>
                    @foreach ($country as $c)
                    <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2 m-100" style="padding-left:0px;padding-right:0px;">
                <select name="filterRoute" id="filterRoute" class="form-control" style="width:100%">
                    <option value="">ALL ROUTE</option>
                    @foreach ($route as $r)
                    <option value="{{$r->id}}">{{$r->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2 m-100" style="padding-left:0px;padding-right:0px;">
                <select name="filterWarehouse" id="filterWarehouse" class="form-control" style="width:100%">
                    <option value="">ALL WAREHOUSE</option>
                </select>
            </div>
            <div class="col-2 m-100" style="padding-left:0px;padding-right:0px;">
                <select name="filterCustType" id="filterCustType" class="form-control" style="width:100%">
                    <option value="">ALL TYPE</option>
                    <option value="IND">INDIVIDUAL</option>
                    <option value="COR">CORPORATE</option>
                </select>
            </div>
            <div class="col d-flex flex-wrap gap-1 w-filter" style="padding-left:0px;padding-right:0px">
                <input type="text" class="form-control" style="flex:1;" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Create Invoice" autocomplete="off" readonly>
                <a id="view" class="filter btn-not-end btn btn-success"><i class="fas fa-filter"></i> Filter</a>
                <a id="reset" class="resetFilter btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</a>
            </div>
            <!-- <div class="col fit-btn" style="padding-left:2px;padding-right:2px">
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
            </div> -->
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="table">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User</th>
                                    <th>ID & Warehouse</th>
                                    <th>Negara</th>
                                    <th>Deskripsi</th>
                                    <th>Berat/Item</th>
                                    @if(Auth::user()->warelist_nom)
                                    <th>Total Pendapatan (Rp)</th>
                                    <th>Total Pendapatan (SGD)</th>
                                    @endif
                                    @if(Auth::user()->warelist_edit||Auth::user()->warelist_hapus)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" data-backdrop="static" id="tambahWarehouse" tabindex="-1" role="dialog" aria-labelledby="addWarehouseLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahWarehouse">
                        @csrf
                        <input type="hidden" name="warehouseIDOld" id="warehouseIDOld">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Warehouse</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" class="form-control" required>
                                        <option value="" hidden>Pilih Country</option>
                                        @foreach ($country as $c)
                                            <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="route">Route</label>
                                        <select name="route" id="route" class="form-control" required>
                                            <option value="" hidden>Pilih Route</option>
                                            @foreach ($route as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="warehouseID">ID Warehouse</label>
                                    <input type="text" name="warehouseID" id="warehouseID" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="warehouseName">Nama Panjang ID Warehouse</label>
                                    <input type="text" name="warehouseName" id="warehouseName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="warehouseLoc">Negara / Lokasi Warehouse</label>
                                    <input type="text" name="warehouseLoc" id="warehouseLoc" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" data-backdrop="static" id="editWarehouse" tabindex="-1" role="dialog" aria-labelledby="addWarehouseLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditWarehouse">
                        @csrf
                        <input type="hidden" name="warehouseIDOld" id="warehouseIDOld">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Warehouse</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" class="form-control" required>
                                        <option value="" hidden>Pilih Country</option>
                                        @foreach ($country as $c)
                                            <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="route">Route</label>
                                        <select name="route" id="route" class="form-control" required>
                                            <option value="" hidden>Pilih Route</option>
                                            @foreach ($route as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="warehouseID">ID Warehouse</label>
                                    <input type="text" name="warehouseID" id="warehouseID" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="warehouseName">Nama Panjang ID Warehouse</label>
                                    <input type="text" name="warehouseName" id="warehouseName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="warehouseLoc">Negara / Lokasi Warehouse</label>
                                    <input type="text" name="warehouseLoc" id="warehouseLoc" onkeyup="this.value = this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/warehouse.js?v='.env('APP_VERSION'))}}"></script>