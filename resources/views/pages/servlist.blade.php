<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3 m-100">
                <h1>Service List</h1>
            </div>
            <div class="col-sm-6 m-100">
                <ol class="breadcrumb float-sm-right">
                    @if(Auth::user()->servlist_export)
                    <a href="{{url('/servlist/export')}}" target="_blank" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</a>
                    @endif
                    <!-- <button type="button" id="kelola-btn" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</button> -->
                    @if(Auth::user()->servlist_buat)
                    <button type="button" id="tambahBtn" class="btn bg-gradient-success"><i class="fas fa-user-plus"></i> Add Service</button>
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
            <div class="col-4 m-100" style="padding-left:0px;padding-right:0px;">
                <select name="filterWarehouse" id="filterWarehouse" class="form-control" style="width:100%">
                    <option value="">ALL WAREHOUSE</option>
                </select>
            </div>
            <div class="col d-flex flex-wrap gap-1 w-filter" style="padding-left:0px;padding-right:0px">
                <input type="text" class="form-control" style="flex:1;" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Create Service" autocomplete="off" readonly>
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
                                    <th style="width:100px" data-priority="1">User</th>
                                    <th>Service</th>
                                    <th data-priority="3">ID & Warehouse</th>
                                    <th data-priority="4">Negara</th>
                                    @if(Auth::user()->servlist_nom)
                                    <th>Harga/Kg</th>
                                    <th>Harga/Item</th>
                                    <th>Harga/Vol</th>
                                    <th>Harga/CBM</th>
                                    @endif
                                    <!--<th>Deskripsi</th>-->
                                    @if(Auth::user()->servlist_edit||Auth::user()->servlist_hapus)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="addService" tabindex="-1" role="dialog" aria-labelledby="addServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahService">
                        @csrf
                        <input type="hidden" name="serviceNameOld" id="serviceNameOld" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <label for="country">Country</label>
                                    <select name="country" class="form-control country">
                                        <option value="" hidden>Pilih Country</option>
                                        @foreach ($country as $c)
                                            <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="warehouse">Warehouse</label>
                                    <select name="warehouse" class="form-control warehouse">
                                        <option value="" hidden>Pilih Warehouse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="serviceName">Nama Service</label>
                                    <input type="text" name="serviceName" id="serviceName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 col-12">
                                    <label for="priceKg">Harga/Kg</label>
                                    <input type="text" name="priceKg" id="priceKg" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceVol">Harga/Vol</label>
                                    <input type="text" name="priceVol" id="priceVol" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/Item</label>
                                    <input type="text" name="priceItem" id="priceItem" class="form-control masking" value="0">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/CBM</label>
                                    <input type="text" name="priceCbm" id="priceCbm" class="form-control masking" value="0">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="deskripsi">Deskripsi (Optional)</label>
                                    <textarea name="description" id="description" onkeyup="this.value=this.value.toUpperCase()" cols="10" rows="3" class="form-control"></textarea>
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
        <div class="modal fade" data-backdrop="static" id="editService" tabindex="-1" role="dialog" aria-labelledby="editServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditService">
                        @csrf
                        <input type="hidden" name="serviceNameOld" id="serviceNameOld" value="">
                        <input type="hidden" name="serviceID" id="serviceID" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <label for="country">Country</label>
                                    <select name="country" class="form-control country">
                                        <option value="" hidden>Pilih Country</option>
                                        @foreach ($country as $c)
                                            <option value="{{$c->id}}">{{substr($c->id,0,2)}} - {{$c->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="warehouse">Warehouse</label>
                                    <select name="warehouse" class="form-control warehouse">
                                        <option value="" hidden>Pilih Warehouse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="serviceName">Nama Service</label>
                                    <input type="text" name="serviceName" id="serviceName" onkeyup="this.value = this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 col-12">
                                    <label for="priceKg">Harga/Kg</label>
                                    <input type="text" name="priceKg" id="priceKg" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceVol">Harga/Vol</label>
                                    <input type="text" name="priceVol" id="priceVol" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/Item</label>
                                    <input type="text" name="priceItem" id="priceItem" class="form-control masking">
                                </div>
                                <div class="col-md-3 col-12">
                                    <label for="priceItem">Harga/CBM</label>
                                    <input type="text" name="priceCbm" id="priceCbm" class="form-control masking" value="0">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="deskripsi">Deskripsi (Optional)</label>
                                    <textarea name="description" id="description" onkeyup="this.value=this.value.toUpperCase()" cols="10" rows="3" class="form-control"></textarea>
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
<script src="{{url('assets/customs/js/servlist.js?v='.env('APP_VERSION'))}}"></script>