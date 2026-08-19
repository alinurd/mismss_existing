<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3 m-100">
                <h1>Customer List</h1>
            </div>
            <div class="col-sm-6 m-100">
                <ol class="breadcrumb float-sm-right">
                    @if(Auth::user()->custlist_buat)
                    <button type="button" id="tambahBtn" class="btn bg-gradient-success"><i class="fas fa-user-plus"></i> Add Customer</button>
                    @endif
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col m-100">
                <ul class="nav nav-pills" id="navChooseCust" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link left-nav active" id="I" data-toggle="tab" href="#nav-individual" role="tab">Individual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav" id="C" data-toggle="tab" href="#nav-corporate" role="tab">Corporate</a>
                    </li>
                </ul>
            </div>
            <div class="col d-flex flex-wrap gap-1 w-filter" style="padding-left:2px;padding-right:2px">
                <input type="text" class="form-control w-auto" style="flex:1;" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Terdaftar" autocomplete="off" readonly>
                <a id="view" class="filter btn-not-end btn btn-success"><i class="fas fa-filter"></i> Filter</a>
                @if(Auth::user()->custlist_export)
                    <a href="{{url('/custlist/export/individual?filterTanggal=')}}" id="exportBtn" target="_blank" class="btn bg-gradient-info p-2 mr-1"><i class="fas fa-file-excel"></i> Export Excel</a>
                @endif
                <a id="reset" class="resetFilter btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</a>
            </div>
            <!-- <div class="col-2 w-filter" style="padding-left:2px;padding-right:2px">
                <input type="text" class="form-control" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Terdaftar" autocomplete="off" readonly>
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
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-individual" role="tabpanel">
                                <table class="table table-striped" id="tableInd">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th style="width:100px">User</th>
                                            <th>Nama ID</th>
                                            <th>Kontak & Alamat</th>
                                            <th>Total Invoice</th>
                                            <th>Berat/Item</th>
                                            @if(Auth::user()->custlist_nom)
                                            <th>Total Pembayaran</th>
                                            @endif
                                            <th>Reference</th>
                                            <th>Informasi</th>
                                            @if(Auth::user()->custlist_edit||Auth::user()->custlist_hapus)
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="nav-corporate" role="tabpanel">
                                <table class="table table-striped" id="tableCor">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th style="width:100px">User</th>
                                            <th>Nama ID</th>
                                            <th>Kontak & Alamat</th>
                                            <th>Total Invoice</th>
                                            <th>Total Resi</th>
                                            <th>Berat/Item</th>
                                            @if(Auth::user()->custlist_nom)
                                            <th>Total Pembayaran</th>
                                            @endif
                                            <th>Reference</th>
                                            <th>Informasi</th>
                                            @if(Auth::user()->custlist_edit||Auth::user()->custlist_hapus)
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="addCustomer" role="dialog" aria-labelledby="addCustomerLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formTambahCustomer">
                        @csrf
                        <input type="hidden" name="choosenCustID" id="choosenCustID">
                        <input type="hidden" name="statusCust" id="statusCust" value="UNREG">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label for="firstName">Nama Depan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="firstName" id="firstName" placeholder="First Name / Nama Depan" required>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="middleName">Nama Tengah</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="middleName" id="middleName" placeholder="Middle Name / Nama Tengah (Optional)">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="lastName">Nama Terakhir</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="lastName" id="lastName" placeholder="Last Name / Nama Terakhir (Optional)">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 col-12">
                                    <label for="email">Alamat Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="email">Kode Negara</label>
                                    <select class="form-control" name="kodeNegara" style="width:100%" required>
                                        <option value="">Pilih Kode Negara</option>
                                        @foreach($kn as $k)
                                            <option value="{{$k->id}}">(+{{$k->code}}) - {{$k->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="phone">Nomor Whatsapp</label>
                                    <input type="number" class="form-control" name="phone" id="phone" onkeypress="return onlyNumberKey(event)" placeholder="Whatsapp Number" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="address">Alamat</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="address" id="address" placeholder="Address" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="subDistrict">Kelurahan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="subDistrict" id="subDistrict" placeholder="Sub-District">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="district">Kecamatan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="district" id="district" placeholder="District">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="city">Kabupaten / Kota</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="city" id="city" placeholder="City" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="prov">Provinsi</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="prov" id="prov" placeholder="Region" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="postalCode">Kode Pos</label>
                                    <input type="text" class="form-control" name="postalCode" id="postalCode" placeholder="Postal Code" required>
                                </div>
                            </div>
                            <!-- <div class="row mt-2">
                                <div class="col-12">
                                    <label for="prov">Reference (Optional)</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="reference" id="reference" placeholder="Reference">
                                </div>
                            </div> -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="editCustomer" role="dialog" aria-labelledby="editCustomerLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditCustomer">
                        @csrf
                        <input type="hidden" name="choosenCustID" id="choosenCustID">
                        <input type="hidden" name="statusCust" id="statusCust" value="EDIT">
                        <input type="hidden" name="emailOld" id="emailOld">
                        <input type="hidden" name="phoneOld" id="phoneOld">
                        <input type="hidden" name="kodeNegaraOld" id="kodeNegaraOld">
                        <input type="hidden" name="custId" id="custId">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label for="firstName">Nama Depan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="firstName" id="firstName" placeholder="First Name / Nama Depan" required>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="middleName">Nama Tengah</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="middleName" id="middleName" placeholder="Middle Name / Nama Tengah (Optional)">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label for="lastName">Nama Terakhir</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="lastName" id="lastName" placeholder="Last Name / Nama Terakhir (Optional)">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 col-12">
                                    <label for="email">Alamat Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="email">Kode Negara</label>
                                    <select class="form-control" name="kodeNegara" style="width:100%" required>
                                        <option value="">Pilih Kode Negara</option>
                                        @foreach($kn as $k)
                                            <option value="{{$k->id}}">(+{{$k->code}}) - {{$k->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="phone">Nomor Whatsapp</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Whatsapp Number" onkeypress="return onlyNumberKey(event)" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="address">Alamat</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="address" id="address" placeholder="Address" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="subDistrict">Kelurahan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="subDistrict" id="subDistrict" placeholder="Sub-District">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="district">Kecamatan</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="district" id="district" placeholder="District">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="city">Kabupaten / Kota</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="city" id="city" placeholder="City" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 col-12">
                                    <label for="prov">Provinsi</label>
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="prov" id="prov" placeholder="Region" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="postalCode">Kode Pos</label>
                                    <input type="text" class="form-control" name="postalCode" id="postalCode" placeholder="Postal Code" required>
                                </div>
                            </div>
                            @if(Auth::user()->role_id=="157834"||Auth::user()->role_id=="248576"||Auth::user()->role_id=="674521")
                            <div class="row mt-2">
                                <div class="col-12">
                                    <label for="prov">Reference (Optional)</label>
                                    <select name="reference" id="reference" class="form-control">
                                        <option value="0">TIDAK ADA REFERENCE</option>
                                        @foreach($cs as $c)
                                            <option value="{{$c->id}}">{{strtoupper($c->fullname)}} - {{strtoupper($c->username)}}</option>
                                        @endforeach
                                    </select>
                                    <!-- <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="reference" id="reference" placeholder="Reference"> -->
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="reference" id="reference">
                            @endif
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
<script src="{{url('assets/customs/js/custlist.js?v='.env('APP_VERSION'))}}"></script>