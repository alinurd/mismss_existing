<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-4">
                <h1>Shipment List</h1>
            </div>
            <div class="col">
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
                                <button type="button" id="IND" class="btn-noselect">Individual</button>
                                <button type="button" id="COR" class="btn-noselect ml-2">Corporate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div class="col-sm-12 m-100">
                <ul class="nav nav-pills" id="navTabOrder">
                    @if(Auth::user()->shiplist_tab_order)
                        <li class="nav-item">
                            <a class="nav-link left-nav" id="nav-ci-tab" data-toggle="tab" href="#nav-ci" role="tab">Create Invoice</a>
                        </li>
                    @endif
                    @if(Auth::user()->shiplist_tab_invoice)
                        <li class="nav-item">
                            <a class="nav-link center-nav" id="nav-ct-tab" data-toggle="tab" href="#nav-ct" role="tab">Create Tracking</a>
                        </li>
                    @endif
                    @if(Auth::user()->shiplist_tab_resi)
                        <li class="nav-item">
                            <a class="nav-link right-nav" id="nav-status-tab" data-toggle="tab" href="#nav-status" role="tab">Status</a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- <div class="col" style="height:100%;vertical-align:middle">
                @if(Auth::user()->shiplist_tab_order||Auth::user()->shiplist_tab_invoice||Auth::user()->shiplist_tab_resi)
                    <div class="row row-filter m-100">
                        <div class="col-4 col-filterWarehouse m-50 m-bottom" style="padding-left:0px;padding-right:0px;">
                            <select name="filterWarehouse" id="filterWarehouse" class="form-control" style="width:100%">
                                <option value="">ALL WAREHOUSE</option>
                                @foreach ($warehouse as $w)
                                <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 col-filterService m-50 m-bottom" style="padding-left:2px;padding-right:2px">
                            <select name="filterService" id="filterService" class="form-control" style="width:100%">
                                <option value="">ALL SERVICES</option>
                            </select>
                        </div>
                        <div class="col-3 w-filter" style="padding-left:2px;padding-right:2px">
                            <input type="text" class="form-control" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Order" autocomplete="off" readonly>
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
                @endif
            </div> -->
        </div>
        <div class="d-flex flex-wrap gap-1 mt-2">
            <select class="form-select" style="flex:1;" aria-label="Select Customer" name="filterCustomer" id="filterCustomer">
                <option value="">ALL CUSTOMER</option>
            </select>
            <select class="form-select" style="flex:1;" aria-label="Select warehouse" name="filterWarehouse" id="filterWarehouse">
                <option value="">ALL WAREHOUSE</option>
                @foreach ($warehouse as $w)
                <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                @endforeach
            </select>
            <select class="form-select" style="flex:1;" aria-label="Select Service" name="filterService" id="filterService">
                <option value="">ALL SERVICES</option>
            </select>
            <input type="text" class="form-control w-auto" style="flex:1;" name="filterTanggal" placeholder="Filter Tanggal Shipment" autocomplete="off" readonly>
            <button id="view" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
            @if(Auth::user()->shiplist_buat_invoice)
                <button id="selectMode" data-id="1" class="btn btn-outline-success"><i class="fas fa-check"></i> Select All</button>
            @endif
            <button id="reset" class="btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</button>
            @if(Auth::user()->shiplist_buat_invoice)
                <button id='createInv' class="btn btn-primary"><i class="fas fa-upload"></i> Buat Invoice</button>
            @endif
        </div>
        <div class="card mt-2 card-table">
            <div class="card-body">
                <div class="tab-content" id="nav-tabContent">
                    @if(Auth::user()->shiplist_tab_order)
                    <div class="tab-pane fade" id="nav-ci" role="tabpanel">
                        <table class="table table-striped" id="table-ci">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Resi Tracking</th>
                                    <th>Warehouse</th>
                                    <th>User Update</th>
                                    <th>Detail Customer</th>
                                    <th>Resi LN</th>
                                    <th>Status</th>
                                    @if(Auth::user()->shiplist_buat_invoice||Auth::user()->shiplist_hapus_order)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                    @if(Auth::user()->shiplist_tab_invoice)
                    <div class="tab-pane fade" id="nav-ct" role="tabpanel">
                        <table class="table table-striped" id="table-ct">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Resi Tracking</th>
                                    <th>No. Invoice</th>
                                    <!--<th>#NULL</th>-->
                                    <th>User Update</th>
                                    <th>Detail Customer</th>
                                    <th>Jumlah</th>
                                    @if(Auth::user()->shiplist_nom)
                                        <th>Rincian Biaya</th>
                                    @endif
                                    @if(Auth::user()->shiplist_edit_invoice||Auth::user()->shiplist_hapus_invoice||Auth::user()->shiplist_buat_resi||Auth::user()->shiplist_printout_invoice)
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                    @if(Auth::user()->shiplist_tab_resi)
                    <div class="tab-pane fade" id="nav-status" role="tabpanel">
                        <table class="table table-striped" id="table-status">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkAll"></th>
                                    <th>No. Invoice</th>
                                    <!--<th>#NULL</th>-->
                                    <th>Resi Tracking</th>
                                    <th id="thResi">Resi Lokal</th>
                                    <th>User Update</th>
                                    <th>Detail Customer</th>
                                    <th>Jumlah</th>
                                    @if(Auth::user()->shiplist_nom)
                                        <th>Rincian Biaya</th>
                                    @endif
                                    @if(Auth::user()->shiplist_printout_invoice||Auth::user()->shiplist_printout_resi||Auth::user()->shiplist_edit_resi)
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="updateInvoice" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="formUpdateInvoice">
                        <input type="hidden" name="invoiceId">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalJamInvoice">Tanggal Invoice Terbayar</label>
                                        <input type="text" name="tanggalJamInvoice" id="tanggalJamInvoice" class="form-control" placeholder="Pilih Tanggal Invoice" readonly required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class='alert-heading'><img src="{{url('/').'/assets/dist/pic/exclamation-triangle.png'}}" style="width:30px;padding-bottom:5px">PERHATIAN !</h4>
                                        <p class="alert-text"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="createInvoice" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formBuatInvoice">
                        @csrf
                        <input type="hidden" name="mismassOrderId">
                        <input type="hidden" name="dbCustId">
                        <input type="hidden" name="dbCustTypeId">
                        <input type="hidden" name="dbFirstName">
                        <input type="hidden" name="dbMiddleName">
                        <input type="hidden" name="dbLastName">
                        <input type="hidden" name="dbEmail">
                        <input type="hidden" name="dbPhone">
                        <input type="hidden" name="dbAddress">
                        <input type="hidden" name="dbSubDistrict">
                        <input type="hidden" name="dbDistrict">
                        <input type="hidden" name="dbCity">
                        <input type="hidden" name="dbProv">
                        <input type="hidden" name="dbPostalCode">
                        <input type="hidden" name="dbSecondName">
                        <input type="hidden" name="dbSecondPhone">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Invoice Individual</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div>Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="customer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>No.Tlp / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telpon"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Alamat Lengkap</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamat"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalInvoice">Tanggal Invoice</label>
                                        <input type="text" name="tanggalInvoice" id="tanggalInvoice" class="form-control" placeholder="Pilih Tanggal Invoice" value="{{date('d/m/Y')}}" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung detail customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/invoice')}}" target="_blank">Download Template</a></label>
                                <input type="file" name="import" id="import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div>

                            <div class="row-inputpos"></div>

                            <div class="row-mstrack mb-2"></div>

                            <div class="row-services"></div>

                            <div class="row mt-3 row-addservice">
                                <div class="col">
                                    <button type="button" id="btnAddServiceElement" data-mstrackid="" data-warehouseid="" data-serviceid="" class="btn btn-primary btn-block"><i class='fas fa-plus'></i> Add Service</button>
                                </div>
                            </div>
                            <!-- <div class="row mt-3">
                                <div class="col">
                                    <div class="totalKiloBoard">
                                        <div>
                                            <label for="">Total Berat (Kg)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalItemBoard">
                                        <div>
                                            <label for="">Total Item</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalCbmBoard">
                                        <div>
                                            <label for="">Total Cbm</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalDiskonBoard">
                                        <div>
                                            <label for="">Total Diskon (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="totalDetailBoard">
                                        <div class="detailBoard">
                                            <div class="board">
                                                <div>Total Berat (Kg)</div>
                                                <div class="totalBeratAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Item</div>
                                                <div class="totalItemAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total CBM</div>
                                                <div class="totalCbmAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Diskon (Rp)</div>
                                                <div class="totalDiskonAll">Rp.0</div>
                                            </div>
                                            <div class="board">
                                                <div>All Sub Total (Rp)</div>
                                                <div class="totalSubTotalAll">Rp.0</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class='form-group'>
                                                <label for='adjustFeeChange'>Apakah Ada Operasional Adjustment Fee ?</label>
                                                <select name='adjustFeeChange' class='form-control' required>
                                                    <option value='' selected hidden>Pilih Opsi Operasional Adjustment Fee</option>
                                                    <option value='ADA'>ADA</option>
                                                    <option value='TIDAK'>TIDAK ADA</option>
                                                </select>
                                            </div>
                                            <div class='form-group adjustFeeElement'>
                                                <label for='adjustFee'>Operasional Adjustment Fee (Rp)</label>
                                                <input type='text' name='adjustFee' class='form-control masking'>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <a class="pointlink adjustFeeBtn">Lihat Daftar Harga Adjustment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col" style='position:relative'>
                                    <div class="totalHargaBoard">
                                        <div>
                                            <label for="">Total Harga (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                    <div class="copyHandle">
                                        <i class="far fa-copy"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="convertToSGD">
                                        <label class="form-check-label" for="convertToSGD">Konversi Mata Uang Asing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 row-convert" style="display:none">
                                <div class="col">
                                    <label for="">Symbol</label>
                                    <input type="text" name="foreignSymbol" id="foreignSymbol" class="form-control" value="SGD" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Nilai Tukar Sekarang (Rp)</label>
                                    <input type="text" name="foreignRateValue" id="foreignRateValue" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="">Total (Rp)</label>
                                    <input type="text" name="totalHargaRP" id="totalHargaRP" class="form-control" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Total (SGD)</label>
                                    <input type="text" name="totalHargaConvert" id="totalHargaConvert" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="pembayaran">Pembayaran</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="" hidden>Pilih Pembayaran</option>
                                        <option value="DOKU">DOKU</option>
                                        <option value="BANK">BANK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 row-detil-pembayaran">
                                <div class="col col-doku">
                                    <label for="">Order Number Doku</label>
                                    <input type="text" name="invoiceDoku" id="invoiceDoku" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-doku">
                                    <label for="">Link Pembayaran Doku</label>
                                    <input type="text" name="linkDoku" id="linkDoku" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Bank</label>
                                    <input type="text" name="namaBank" id="namaBank" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Pemilik Rekening</label>
                                    <input type="text" name="namaRekening" id="namaRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">No. Rekening / Virtual Account</label>
                                    <input type="text" name="noRekening" id="noRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Format Alamat untuk Invoice</label>
                                    <select name="templateId" id="templateId" class="form-control" required>
                                    <option value="" hidden>Pilih Format</option>
                                    @foreach($template as $t)
                                    <option value="{{$t->id}}">{{$t->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit & Blast</button>
                            </div>
                        </div>
                        <!--<div class="modal-footer">-->
                        <!--    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
                        <!--    <button type="submit" class="btn btn-primary">Submit & Blast</button>-->
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="editInvoice" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditInvoice">
                        @csrf
                        <input type="hidden" name="mismassOrderId">
                        <input type="hidden" name="mismassInvoiceId">
                        <input type="hidden" name="createdAt">
                        <input type="hidden" name="mismassInvoiceLink">
                        <input type="hidden" name="serviceName">
                        <input type="hidden" name="custId">
                        <input type="hidden" name="custTypeId">
                        <input type="hidden" name="dbFirstName">
                        <input type="hidden" name="dbMiddleName">
                        <input type="hidden" name="dbLastName">
                        <input type="hidden" name="dbEmail">
                        <input type="hidden" name="dbPhone">
                        <input type="hidden" name="dbAddress">
                        <input type="hidden" name="dbSubDistrict">
                        <input type="hidden" name="dbDistrict">
                        <input type="hidden" name="dbCity">
                        <input type="hidden" name="dbProv">
                        <input type="hidden" name="dbPostalCode">
                        <input type="hidden" name="dbSecondName">
                        <input type="hidden" name="dbSecondPhone">
                        <input type="hidden" name="invAdd">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div>No. Invoice</div>
                                </div>
                                <div class="col text-right">
                                    <label for="noInvoice"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="customer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>No.Tlp / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telpon"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Alamat Lengkap</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamat"></label>
                                </div>
                            </div>
                            <div class="row row-edit-detil-penerima">
                                <div class="col text-right">
                                    <button type="button" class="btn btn-primary" id="btnEditDetilPenerima">Edit Detil Penerima</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalInvoice">Tanggal Invoice / Shipment</label>
                                        <input type="text" name="tanggalInvoice" id="tanggalInvoice" class="form-control" placeholder="Pilih Tanggal Invoice" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung detail customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/invoice')}}" target="_blank">Download Template</a></label>
                                <input type="file" name="import" id="import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div>

                            <div class="row-services"></div>

                            <div class="row mt-3">
                                <div class="col">
                                    <button type="button" id="btnAddServiceElement" class="btn btn-primary btn-block"><i class='fas fa-plus'></i> Add Service</button>
                                </div>
                            </div>
                            <!-- <div class="row mt-3">
                                <div class="col">
                                    <div class="totalKiloBoard">
                                        <div>
                                            <label for="">Total Berat (Kg)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalItemBoard">
                                        <div>
                                            <label for="">Total Item</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalCbmBoard">
                                        <div>
                                            <label for="">Total Cbm</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalDiskonBoard">
                                        <div>
                                            <label for="">Total Diskon (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row mt-3">
                                <div class="col">
                                    <div class="totalDetailBoard">
                                        <div class="detailBoard">
                                            <div class="board">
                                                <div>Total Berat (Kg)</div>
                                                <div class="totalBeratAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Item</div>
                                                <div class="totalItemAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total CBM</div>
                                                <div class="totalCbmAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Diskon (Rp)</div>
                                                <div class="totalDiskonAll">Rp.0</div>
                                            </div>
                                            <div class="board">
                                                <div>All Sub Total (Rp)</div>
                                                <div class="totalSubTotalAll">Rp.0</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class='form-group'>
                                                <label for='adjustFeeChange'>Apakah Ada Operasional Adjustment Fee ?</label>
                                                <select name='adjustFeeChange' class='form-control' required>
                                                    <option value='' selected hidden>Pilih Opsi Operasional Adjustment Fee</option>
                                                    <option value='ADA'>ADA</option>
                                                    <option value='TIDAK'>TIDAK ADA</option>
                                                </select>
                                            </div>
                                            <div class='form-group adjustFeeElement'>
                                                <label for='adjustFee'>Operasional Adjustment Fee (Rp)</label>
                                                <input type='text' name='adjustFee' class='form-control masking'>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <a class="pointlink adjustFeeBtn">Lihat Daftar Harga Adjustment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col" style='position:relative'>
                                    <div class="totalHargaBoard">
                                        <div>
                                            <label for="">Total Harga (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                    <div class="copyHandle">
                                        <i class="far fa-copy"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="convertToSGD">
                                        <label class="form-check-label" for="convertToSGD">Konversi Mata Uang Asing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 row-convert" style="display:none">
                                <div class="col">
                                    <label for="">Symbol</label>
                                    <input type="text" name="foreignSymbol" id="foreignSymbol" class="form-control" value="SGD" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Nilai Tukar Sekarang (Rp)</label>
                                    <input type="text" name="foreignRateValue" id="foreignRateValue" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="">Total (Rp)</label>
                                    <input type="text" name="totalHargaRP" id="totalHargaRP" class="form-control" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Total (SGD)</label>
                                    <input type="text" name="totalHargaConvert" id="totalHargaConvert" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="pembayaran">Pembayaran</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="" hidden>Pilih Pembayaran</option>
                                        <option value="DOKU">DOKU</option>
                                        <option value="BANK">BANK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 row-detil-pembayaran">
                                <div class="col col-doku">
                                    <label for="">Order Number Doku</label>
                                    <input type="text" name="invoiceDoku" id="invoiceDoku" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-doku">
                                    <label for="">Link Pembayaran Doku</label>
                                    <input type="text" name="linkDoku" id="linkDoku" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Bank</label>
                                    <input type="text" name="namaBank" id="namaBank" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Pemilik Rekening</label>
                                    <input type="text" name="namaRekening" id="namaRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">No. Rekening / Virtual Account</label>
                                    <input type="text" name="noRekening" id="noRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Format Alamat untuk Invoice</label>
                                    <select name="templateId" id="templateId" class="form-control" required>
                                    <option value="" hidden>Pilih Format</option>
                                    @foreach($template as $t)
                                    <option value="{{$t->id}}">{{$t->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Alasan Revisi</label>
                                    <input type="text" name="revisionNote" id="revisionNote" placeholder="Tulis Alasan Anda..." onkeyup="this.value=this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit & Blast</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="createInvoiceAdditional" tabindex="-1" role="dialog" aria-labelledby="createInvoiceAddLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                    <form id="formBuatInvoiceAdditional">
                        @csrf
                        <input type="hidden" name="mismassOrderId">
                        <input type="hidden" name="mismassInvoiceId">
                        <input type="hidden" name="createdAt">
                        <input type="hidden" name="mismassInvoiceLink">
                        <input type="hidden" name="serviceName">
                        <input type="hidden" name="custId">
                        <input type="hidden" name="custTypeId">
                        <input type="hidden" name="dbFirstName">
                        <input type="hidden" name="dbMiddleName">
                        <input type="hidden" name="dbLastName">
                        <input type="hidden" name="dbEmail">
                        <input type="hidden" name="dbPhone">
                        <input type="hidden" name="dbAddress">
                        <input type="hidden" name="dbSubDistrict">
                        <input type="hidden" name="dbDistrict">
                        <input type="hidden" name="dbCity">
                        <input type="hidden" name="dbProv">
                        <input type="hidden" name="dbPostalCode">
                        <input type="hidden" name="dbSecondName">
                        <input type="hidden" name="dbSecondPhone">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div>No. Invoice</div>
                                </div>
                                <div class="col text-right">
                                    <label for="noInvoice"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="customer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>No.Tlp / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telpon"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Alamat Lengkap</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamat"></label>
                                </div>
                            </div>
                            <div class="row row-edit-detil-penerima">
                                <div class="col text-right">
                                    <button type="button" class="btn btn-primary" id="btnEditDetilPenerima">Edit Detil Penerima</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalInvoice">Tanggal Invoice / Shipment</label>
                                        <input type="text" name="tanggalInvoice" id="tanggalInvoice" class="form-control" placeholder="Pilih Tanggal Invoice" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung detail customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/invoice')}}" target="_blank">Download Template</a></label>
                                <input type="file" name="import" id="import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div>

                            <div class="row-services"></div>

                            <div class="row mt-3">
                                <div class="col">
                                    <button type="button" id="btnAddServiceElementAdditional" class="btn btn-primary btn-block"><i class='fas fa-plus'></i> Add Service</button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="totalDetailBoard">
                                        <div class="detailBoard">
                                            <div class="board">
                                                <div>Total Berat (Kg)</div>
                                                <div class="totalBeratAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Item</div>
                                                <div class="totalItemAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total CBM</div>
                                                <div class="totalCbmAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Diskon (Rp)</div>
                                                <div class="totalDiskonAll">Rp.0</div>
                                            </div>
                                            <div class="board">
                                                <div>All Sub Total (Rp)</div>
                                                <div class="totalSubTotalAll">Rp.0</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class='form-group'>
                                                <label for='adjustFeeChange'>Apakah Ada Operasional Adjustment Fee ?</label>
                                                <select name='adjustFeeChange' class='form-control' required>
                                                    <option value='' selected hidden>Pilih Opsi Operasional Adjustment Fee</option>
                                                    <option value='ADA'>ADA</option>
                                                    <option value='TIDAK'>TIDAK ADA</option>
                                                </select>
                                            </div>
                                            <div class='form-group adjustFeeElement' style='display:none'>
                                                <label for='adjustFee'>Operasional Adjustment Fee (Rp)</label>
                                                <input type='text' name='adjustFee' class='form-control masking'>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <a class="pointlink adjustFeeBtn">Lihat Daftar Harga Adjustment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col" style='position:relative'>
                                    <div class="totalHargaBoard">
                                        <div>
                                            <label for="">Total Harga (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                    <div class="copyHandle">
                                        <i class="far fa-copy"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="convertToSGD">
                                        <label class="form-check-label" for="convertToSGD">Konversi Mata Uang Asing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 row-convert" style="display:none">
                                <div class="col">
                                    <label for="">Symbol</label>
                                    <input type="text" name="foreignSymbol" id="foreignSymbol" class="form-control" value="SGD" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Nilai Tukar Sekarang (Rp)</label>
                                    <input type="text" name="foreignRateValue" id="foreignRateValue" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="">Total (Rp)</label>
                                    <input type="text" name="totalHargaRP" id="totalHargaRP" class="form-control" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Total (SGD)</label>
                                    <input type="text" name="totalHargaConvert" id="totalHargaConvert" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="pembayaran">Pembayaran</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="" hidden>Pilih Pembayaran</option>
                                        <option value="DOKU">DOKU</option>
                                        <option value="BANK">BANK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 row-detil-pembayaran">
                                <div class="col col-doku">
                                    <label for="">Order Number Doku</label>
                                    <input type="text" name="invoiceDoku" id="invoiceDoku" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-doku">
                                    <label for="">Link Pembayaran Doku</label>
                                    <input type="text" name="linkDoku" id="linkDoku" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Bank</label>
                                    <input type="text" name="namaBank" id="namaBank" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Pemilik Rekening</label>
                                    <input type="text" name="namaRekening" id="namaRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">No. Rekening / Virtual Account</label>
                                    <input type="text" name="noRekening" id="noRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Format Alamat untuk Invoice</label>
                                    <select name="templateId" id="templateId" class="form-control" required>
                                    <option value="" hidden>Pilih Format</option>
                                    @foreach($template as $t)
                                    <option value="{{$t->id}}">{{$t->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit & Blast</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" data-backdrop="static" id="editInvoiceAdditional" tabindex="-1" role="dialog" aria-labelledby="editInvoiceAddLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditInvoiceAdditional">
                        @csrf
                        <input type="hidden" name="mismassOrderId">
                        <input type="hidden" name="mismassInvoiceId">
                        <input type="hidden" name="createdAt">
                        <input type="hidden" name="mismassInvoiceLink">
                        <input type="hidden" name="serviceName">
                        <input type="hidden" name="custId">
                        <input type="hidden" name="custTypeId">
                        <input type="hidden" name="dbFirstName">
                        <input type="hidden" name="dbMiddleName">
                        <input type="hidden" name="dbLastName">
                        <input type="hidden" name="dbEmail">
                        <input type="hidden" name="dbPhone">
                        <input type="hidden" name="dbAddress">
                        <input type="hidden" name="dbSubDistrict">
                        <input type="hidden" name="dbDistrict">
                        <input type="hidden" name="dbCity">
                        <input type="hidden" name="dbProv">
                        <input type="hidden" name="dbPostalCode">
                        <input type="hidden" name="dbSecondName">
                        <input type="hidden" name="dbSecondPhone">
                        <input type="hidden" name="invAdd">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div>No. Invoice</div>
                                </div>
                                <div class="col text-right">
                                    <label for="noInvoice"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="customer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>No.Tlp / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telpon"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div>Alamat Lengkap</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamat"></label>
                                </div>
                            </div>
                            <div class="row row-edit-detil-penerima">
                                <div class="col text-right">
                                    <button type="button" class="btn btn-primary" id="btnEditDetilPenerima">Edit Detil Penerima</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="tanggalInvoice">Tanggal Invoice / Shipment</label>
                                        <input type="text" name="tanggalInvoice" id="tanggalInvoice" class="form-control" placeholder="Pilih Tanggal Invoice" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung detail customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/invoice')}}" target="_blank">Download Template</a></label>
                                <input type="file" name="import" id="import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div>

                            <div class="row-services"></div>

                            <div class="row mt-3">
                                <div class="col">
                                    <button type="button" id="btnAddServiceElementAdditional" class="btn btn-primary btn-block"><i class='fas fa-plus'></i> Add Service</button>
                                </div>
                            </div>
                            <!-- <div class="row mt-3">
                                <div class="col">
                                    <div class="totalKiloBoard">
                                        <div>
                                            <label for="">Total Berat (Kg)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalItemBoard">
                                        <div>
                                            <label for="">Total Item</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalCbmBoard">
                                        <div>
                                            <label for="">Total Cbm</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="totalDiskonBoard">
                                        <div>
                                            <label for="">Total Diskon (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row mt-3">
                                <div class="col">
                                    <div class="totalDetailBoard">
                                        <div class="detailBoard">
                                            <div class="board">
                                                <div>Total Berat (Kg)</div>
                                                <div class="totalBeratAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Item</div>
                                                <div class="totalItemAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total CBM</div>
                                                <div class="totalCbmAll">0</div>
                                            </div>
                                            <div class="board">
                                                <div>Total Diskon (Rp)</div>
                                                <div class="totalDiskonAll">Rp.0</div>
                                            </div>
                                            <div class="board">
                                                <div>All Sub Total (Rp)</div>
                                                <div class="totalSubTotalAll">Rp.0</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class='form-group'>
                                                <label for='adjustFeeChange'>Apakah Ada Operasional Adjustment Fee ?</label>
                                                <select name='adjustFeeChange' class='form-control' required>
                                                    <option value='' selected hidden>Pilih Opsi Operasional Adjustment Fee</option>
                                                    <option value='ADA'>ADA</option>
                                                    <option value='TIDAK'>TIDAK ADA</option>
                                                </select>
                                            </div>
                                            <div class='form-group adjustFeeElement'>
                                                <label for='adjustFee'>Operasional Adjustment Fee (Rp)</label>
                                                <input type='text' name='adjustFee' class='form-control masking'>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <a class="pointlink adjustFeeBtn">Lihat Daftar Harga Adjustment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col" style='position:relative'>
                                    <div class="totalHargaBoard">
                                        <div>
                                            <label for="">Total Harga (Rp)</label>
                                            <div class="value">0</div>
                                        </div>
                                    </div>
                                    <div class="copyHandle">
                                        <i class="far fa-copy"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="convertToSGD">
                                        <label class="form-check-label" for="convertToSGD">Konversi Mata Uang Asing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 row-convert" style="display:none">
                                <div class="col">
                                    <label for="">Symbol</label>
                                    <input type="text" name="foreignSymbol" id="foreignSymbol" class="form-control" value="SGD" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Nilai Tukar Sekarang (Rp)</label>
                                    <input type="text" name="foreignRateValue" id="foreignRateValue" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="">Total (Rp)</label>
                                    <input type="text" name="totalHargaRP" id="totalHargaRP" class="form-control" readonly>
                                </div>
                                <div class="col">
                                    <label for="">Total (SGD)</label>
                                    <input type="text" name="totalHargaConvert" id="totalHargaConvert" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="pembayaran">Pembayaran</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="" hidden>Pilih Pembayaran</option>
                                        <option value="DOKU">DOKU</option>
                                        <option value="BANK">BANK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 row-detil-pembayaran">
                                <div class="col col-doku">
                                    <label for="">Order Number Doku</label>
                                    <input type="text" name="invoiceDoku" id="invoiceDoku" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-doku">
                                    <label for="">Link Pembayaran Doku</label>
                                    <input type="text" name="linkDoku" id="linkDoku" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Bank</label>
                                    <input type="text" name="namaBank" id="namaBank" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">Nama Pemilik Rekening</label>
                                    <input type="text" name="namaRekening" id="namaRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                                <div class="col col-bank">
                                    <label for="">No. Rekening / Virtual Account</label>
                                    <input type="text" name="noRekening" id="noRekening" onkeyup="this.value=this.value.toUpperCase()" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Format Alamat untuk Invoice</label>
                                    <select name="templateId" id="templateId" class="form-control" required>
                                    <option value="" hidden>Pilih Format</option>
                                    @foreach($template as $t)
                                    <option value="{{$t->id}}">{{$t->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="">Alasan Revisi</label>
                                    <input type="text" name="revisionNote" id="revisionNote" placeholder="Tulis Alasan Anda..." onkeyup="this.value=this.value.toUpperCase()" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit & Blast</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="createResi" tabindex="-1" role="dialog" aria-labelledby="createResiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formBuatResi">
                        @csrf
                        <input type="hidden" name="mismassInvoiceId">
                        <input type="hidden" name="custTypeId">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Resi Individual</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row" style='display:none'>
                                <div class="col">
                                    <div class="tanggalDrop">Tanggal Drop</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tanggalDrop"></label>
                                </div>
                            </div>
                            <div class="row" style='display:none'>
                                <div class="col">
                                    <div class="tanggalShipment">Tanggal Shipment</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tanggalShipment"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="noInvoiceTanggal">No. Invoice | Tanggal</div>
                                </div>
                                <div class="col text-right">
                                    <label for="noInvoiceTanggal"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="tipeCustomer">Tipe Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tipeCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="namaCustomer">Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="namaCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="telponWhatsapp">No. Telpon / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telponWhatsapp"></label>
                                </div>
                            </div>
                            <div class="row row-alamat-penerima">
                                <div class="col">
                                    <div class="alamatCustomer">Alamat Customer (Penerima)</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamatCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalBerat">Total Berat</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalBerat"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalPcs">Total Item</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalPcs"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalVolume">Total Volume</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalVolume"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalBayar">Total Pembayaran</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalBayar"></label>
                                </div>
                            </div>

                            <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung nomor resi customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/shipping')}}" target="_blank">Download Template</a></label>
                                <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="import" id="import" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div>

                            <div class="resi-element"></div>

                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit & Blast</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="editDetilPenerima" tabindex="-1" role="dialog" aria-labelledby="editDetilPenerimaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditDetilPenerima">
                        @csrf
                        <input type="hidden" name="orderId" id="orderId">
                        <input type="hidden" name="statusCust" id="statusCust" value="EDIT">
                        <input type="hidden" name="emailOld" id="emailOld">
                        <input type="hidden" name="phoneOld" id="phoneOld">

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
                                <div class="col-md-6 col-12">
                                    <label for="email">Alamat Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="phone">Nomor Whatsapp</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Whatsapp Number" required>
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
                                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="district" id="district" placeholder="District" required>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" data-backdrop="static" id="editResi" tabindex="-1" role="dialog" aria-labelledby="editResiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditResi">
                        @csrf
                        <input type="hidden" name="mismassInvoiceId">
                        <input type="hidden" name="custTypeId">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Resi Individual</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row" style='display:none'>
                                <div class="col">
                                    <div class="tanggalDrop">Tanggal Drop</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tanggalDrop"></label>
                                </div>
                            </div>
                            <div class="row" style='display:none'>
                                <div class="col">
                                    <div class="tanggalShipment">Tanggal Shipment</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tanggalShipment"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="noInvoiceTanggal">No. Invoice | Tanggal</div>
                                </div>
                                <div class="col text-right">
                                    <label for="noInvoiceTanggal"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="tipeCustomer">Tipe Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="tipeCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="namaCustomer">Nama Customer</div>
                                </div>
                                <div class="col text-right">
                                    <label for="namaCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="telponWhatsapp">No. Telpon / Whatsapp</div>
                                </div>
                                <div class="col text-right">
                                    <label for="telponWhatsapp"></label>
                                </div>
                            </div>
                            <div class="row row-alamat-penerima">
                                <div class="col">
                                    <div class="alamatCustomer">Alamat Customer (Penerima)</div>
                                </div>
                                <div class="col text-right">
                                    <label for="alamatCustomer"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalBerat">Total Berat</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalBerat"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalPcs">Total Item</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalPcs"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalVolume">Total Volume</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalVolume">0</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="totalBayar">Total Pembayaran</div>
                                </div>
                                <div class="col text-right">
                                    <label for="totalBayar"></label>
                                </div>
                            </div>

                            <!-- <div class="alert alert-success" style="background-color:#d1e7dd;border-color:#badbcc;color:#0f5132;padding:10px" role="alert">
                                <label for="">Upload secara langsung nomor resi customer penerima disini dengan format xls, xlsx. <a class="dwn-excel" href="{{url('/import/example/shipping')}}" target="_blank">Download Template</a></label>
                                <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="import" id="import" style='width:85%'><button type="button" id="importBtn" class="btn btn-primary" style="width:15%"><i class="fas fa-plus"></i> Import</button>
                            </div> -->

                            <div class="resi-element"></div>

                        </div>
                        <div class="modal-footer" style="justify-content: space-between;">
                            <div class="searchElem" style="width:50%;position:relative">
                                <input type="text" id="searching" class="form-control" placeholder="Cari">
                                <div id="searchingScore" style="position:absolute;top:20%;right:10px;font-size:15px;color:#878787;display:none;">0/0</div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit & Blast</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="resiLnModalSL" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document"  style="max-width:500px">
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
                            <div class="row row-photo">
                                <div class="col">
                                    <div class="card card-Upload mt-2">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Foto</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row row-btn-upload-img">
                                                <div class="col">
                                                    <div class="col-preview">
                                                        <button type="button" class="uploadImageNote"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                    <!-- <div class='text'></div> -->
                                                    <textarea rows="3" name="note" id="note" class="form-control"></textarea>
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

        <div class="modal fade" id="adjustFeeModal" tabindex="-1" role="dialog" aria-labelledby="adjustFeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Adjustment Fee</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>Operational Adjustment Fee merupakan tambahan biaya yang berlaku pada setiap
                        shipment dan setiap invoice, dengan ketentuan:</div>
                    <table class="mt-2">
                        <tr>
                            <td width="100px">Berat < 10 kg</td>
                            <td width="50px">→</td>
                            <td>IDR 15.000</td>
                        </tr>
                        <tr>
                            <td>11 – 20 kg</td>
                            <td>→</td>
                            <td>IDR 25.000</td>
                        </tr>
                        <tr>
                            <td>21 – 30 kg</td>
                            <td>→</td>
                            <td>IDR 35.000</td>
                        </tr>
                        <tr>
                            <td>31 – 40 kg</td>
                            <td>→</td>
                            <td>IDR 45.000</td>
                        </tr>
                        <tr>
                            <td>41 – 50 kg</td>
                            <td>→</td>
                            <td>IDR 55.000</td>
                        </tr>
                        <tr>
                            <td>Di atas 50 kg dst</td>
                            <td>→</td>
                            <td>IDR 65.000</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pickUpChargeModal" tabindex="-1" role="dialog" aria-labelledby="pickUpChargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Pickup Charge</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>Ketentuan Pick-Up Service Fee (Islandwide):</div>
                    <table class="mt-2">
                        <tr>
                            <td width="100px">10 – 19,99 kg</td>
                            <td width="50px">→</td>
                            <td>SGD 25</td>
                        </tr>
                        <tr>
                            <td>20 – 39,99 kg</td>
                            <td>→</td>
                            <td>SGD 35</td>
                        </tr>
                        <tr>
                            <td>40 – 59,99 kg</td>
                            <td>→</td>
                            <td>SGD 45</td>
                        </tr>
                        <tr>
                            <td>Berat > 60 kg</td>
                            <td>→</td>
                            <td>SGD 55</td>
                        </tr>
                    </table>
                    <div class="mt-2">Catatan:</div>
                    <div>* Parcel < 10 kg fee pick-up mulai dari SGD 25</div>
                    <div>* Parcel belum siap saat jadwal pick-up additional charge dapat berlaku (tergantung lokasi)</div>
                    <div>* Lokasi tanpa lift tambahan SGD 10 per lantai</div>
                    <div>* Refundable deposit SGD 15 / box untuk permintaan air-freight box</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script>
    wareH = "{{$warehouse}}";
    addServ = "{{$additional}}";
    auth = "{{Auth::user()->shiplist_printout_resi}}";
</script>
<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/custom-methods.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/checking/confirmation.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/checking/check-connection.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-main.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-order.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-invoice.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-resi.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-create-invoice.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-edit-invoice.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-void-invoice.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiplist-invoice-additional.js?v='.env('APP_VERSION'))}}"></script>