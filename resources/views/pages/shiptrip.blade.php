<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-4">
                <h1>Shipment Trip</h1>
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
        <div class="row">
            <div class="col m-100" style="display:inline-flex">
                <ul class="nav nav-pills float-right" id="navChoose" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link left-nav active pointlink" id="whabroad" data-toggle="tab" role="tab">WH Abroad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link center-nav pointlink" id="sgn" data-toggle="tab" role="tab">Warehouse HUB</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link center-nav pointlink" id="btm" data-toggle="tab" role="tab">Indonesia WH</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link center-nav pointlink" id="jkt" data-toggle="tab" role="tab">Jakarta WH</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav pointlink" id="end" data-toggle="tab" role="tab">Endpoint</a>
                    </li>
                </ul>
            </div>
            <div class="col m-100 col-filter-end" style="display:none">
                <div class="d-flex flex-wrap gap-1">
                    <!-- <input type="text" class="form-control w-auto" style="flex:1;" name="filterTanggal" placeholder="Filter Tanggal Shipment" autocomplete="off" readonly> -->
                    <button class="filter btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                    <button class="resetFilter btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</button>
                </div>
            </div>
        </div>
        <div class="col-filter-not-end mt-2">
            <div class="d-flex flex-wrap gap-1">
                <select class="form-select w-auto" style="flex:1;" aria-label="Select warehouse" name="filterWarehouse" id="filterWarehouse">
                    <option value="">ALL WAREHOUSE</option>
                    @foreach ($warehouse as $w)
                    <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                    @endforeach
                </select>
                <input type="text" class="form-control" style="flex:1;" name="filterTanggal" placeholder="Filter Tanggal Drop" autocomplete="off" readonly>
                <button class="filter btn-not-end btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                @if(Auth::user()->shiptrip_filter)
                    <button id="selectMode" data-id="1" class="btn btn-outline-success"><i class="fas fa-check"></i> Select All</button>
                @endif
                <button class="resetFilter btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</button>
                @if(Auth::user()->shiptrip_filter)
                    <button id='updateShip' class="btn btn-primary"><i class="fas fa-upload"></i> Update Shipment</button>
                @endif
            </div>
        </div>
        <div class="card mt-2 card-table">
            <div class="card-body">
                <table class="table table-striped" id="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Resi Tracking</th>
                            <th>Warehouse & Service</th>
                            <th>User Update</th>
                            <th>Shipper</th>
                            <th>Consignee</th>
                            <th>Resi LN</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="resiLnModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document"  style="max-width:500px">
                <div class="modal-content">
                    <form id="formEditCatatan">
                        <input type="hidden" name="msTrackId">
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
                                                    <!-- <input type="text" class="form-control" name="note" id="note"> -->
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
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="updateShipmentModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document"  style="max-width:1000px">
                <div class="modal-content">
                    <form id="formUpdateShipment">
                        <div id="inputPos"></div>
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div id="tablePos" style="overflow:auto;height:auto;"></div>
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
                            <div class="row">
                                <div class="col col-sm-12 col-mode-status">
                                    <div class="form-group">
                                        <label for="modeStatus">Mode Status Tracking</label>
                                        <select name="modeStatus" id="modeStatus" class="form-control" required>
                                            <option value="" hidden>Pilih Mode Status</option>
                                            <option value="AUTO">Otomatis</option>
                                            <option value="MANUAL">Manual</option>
                                            <option value="SKIP">Skip</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-track-status-skip">
                                    <div class="form-group">
                                        <label for="trackStatusSkip">Status Tracking Skip</label>
                                        <select name="trackStatusSkip" id="trackStatusSkip" class="form-control">
                                            <option value="" hidden>Pilih Status</option>
                                            @foreach($listStatusSkip as $lss)
                                                <option value="{{$lss->id}}" data-value="{{$lss->value}}">{{$lss->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-track-status-manual">
                                    <div class="form-group">
                                        <label for="trackStatusManual">Status Tracking Manual</label>
                                        <select name="trackStatusManual" id="trackStatusManual" class="form-control">
                                            <option value="" hidden>Pilih Status Manual</option>
                                            @foreach($listStatusManual as $lsm)
                                                <option value="{{$lsm->id}}" data-value="{{$lsm->value}}">{{$lsm->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-set-tanggal">
                                    <div class="form-group">
                                        <label for="tanggalShipment">Set Tanggal Shipment</label>
                                        <input type="text" name="tanggalShipment" id="tanggalShipment" class="form-control" placeholder="Tanggal Shipment" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-status">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="statusNow">Status Saat Ini</label>
                                        <textarea rows="3" name="statusNow" id="statusNow" class="form-control" readonly></textarea>
                                        <!-- <input type="text" name="statusNow" id="statusNow" class="form-control" readonly> -->
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="statusNext">Status Berikutnya</label>
                                        <textarea rows="3" name="statusNext" id="statusNext" class="form-control" readonly></textarea>
                                        <!-- <input type="text" name="statusNext" id="statusNext" class="form-control" readonly> -->
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

        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="editShipmentModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width:1000px">
                <div class="modal-content">
                    <form id="formEditShipment">
                        <input type="hidden" name="msTrackId">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row row-table">
                                <div class="col">
                                    <div id="tablePos"></div>
                                </div>
                            </div>
                            <div class="row row-detail">
                                <div class="col col-consignee">
                                    <div class="card">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Penerima/Consignee</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="consName"></div>
                                                    <div class="consPhone"></div>
                                                    <div class="consAddress"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Pengirim/Sender</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="sendName"></div>
                                                    <div class="sendPhone"></div>
                                                    <div class="sendAddress"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-warehouse">
                                <div class="col">
                                    <div class="card card-other mt-2">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Shipment</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 col-warehouse">
                                                    <label for="Warehouse">Warehouse</label>
                                                    <select name="warehouse" id="warehouse" class="form-control" style="width:100%" required>
                                                        <option value="">Pilih Warehouse</option>
                                                        @foreach ($warehouse as $w)
                                                            <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-12 col-service" style="display:none">
                                                    <label for="Service">Service</label>
                                                    <select name="service" id="service" class="form-control" style="width:100%">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-preview-warehouse">
                                <div class="col">
                                    <div class="card card-other mt-2">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Shipment</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 col-warehouse">
                                                    <label for="Warehouse">Warehouse</label>
                                                    <input type="text" name="warehouseview" id="warehouseview" class="form-control" readonly>
                                                </div>
                                                <div class="col-md-6 col-12 col-service" style="display:none">
                                                    <label for="Service">Service</label>
                                                    <input type="text" name="serviceview" id="serviceview" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-resiLN">
                                <div class="col">
                                    <div class="card card-resiLN">
                                        <div class="card-header card-header-ms">
                                            <h3 class="card-title">Data Resi Luar Negeri</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class='row row-resi-ln' id='00000'>
                                                <div class='col' style='padding-top:6px'>
                                                    <div style="display:flex;" class="resi-item">
                                                        <div class="noUrutResiLN">1.</div>
                                                        <div style='width: -webkit-fill-available'>
                                                            <input type='text' name='resiln[]' id='AKVNFLDOSJ' onkeyup='this.value=this.value.toUpperCase()' placeholder='Input Resi LN ...' class='form-control resiln' required>
                                                        </div>
                                                        <button type='button' data-edit-btn='' class='btn addResiLN' style='font-size:25px!important;color:green;padding:0px 5px 0px 10px'><i class='fas fa-plus-square'></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @for($a=0;$a<100;$a++)
                                                <div class='row row-resi-ln' style='display:none'>
                                                <div class='col' style='padding-top:6px'>
                                                    <div style="display:flex;" class="resi-item">
                                                        <div class="noUrutResiLN"></div>
                                                        <div style='width: -webkit-fill-available'>
                                                            <input type='text' name='resiln[]' onkeyup='this.value=this.value.toUpperCase()' placeholder='Input Resi LN ...' class='form-control resiln' required>
                                                        </div>
                                                        <button type='button' class='btn minResiLN text-primary' style='font-size:25px!important;padding:0px 5px 0px 10px'><i class='fas fa-backward'></i></button>
                                                        <button type='button' data-edit-btn='' class='btn addResiLN' style='font-size:25px!important;color:green;padding:0px 5px 0px 10px'><i class='fas fa-plus-square'></i></button>
                                                        <button type='button' class='btn deleteResiLN text-danger' style='font-size:25px!important;padding:0px 5px 0px 5px'><i class='fas fa-trash'></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-preview-resiLN">
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
                                                        <input type='file' name="file[]" id="file[]" class="form-control" accept="image/*" multiple style="display:none">
                                                        <button type="button" class="uploadImage"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <div class="row row-btn-upload-img">
                                        <div class="col">
                                            <label for="catatan">Catatan</label>
                                            <!-- <input type='text' name="catatan" id="catatan" class="form-control" placeholder="Catatan Untuk Shipment..."> -->
                                            <textarea rows="3" name="catatan" id="catatan" class="form-control" placeholder="Catatan Untuk Shipment..."></textarea>
                                        </div>
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

        <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="endUpdateModal" tabindex="-1" role="dialog" aria-labelledby="endUpdateLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="formEndUpdate">
                        <input type="hidden" name="shippingNumber">
                        <input type="hidden" name="msTrackId">
                        <input type="hidden" name="custTypeId">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-1">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="trackStatusId">Status Tracking</label>
                                        <select class="form-control" name="trackStatusId" id="trackStatusId" required></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-receiver">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="receiver">Nama Penerima</label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="receiver" id="receiver">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-reason">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="reason">Alasan</label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="reason" id="reason">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-location">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="location">Lokasi</label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="location" id="location">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 mb-3 row-bukti-foto">
                                <div class="col-sm-12 col-md-3">
                                    <div class="preview-bukti-foto">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-9 col-bukti-foto">
                                    <div class="form-group">
                                        <label for="buktiFoto">Bukti Foto</label>
                                        <input type="file" class="form-control" accept="image/*" name="buktiFoto[]" id="buktiFoto" placeholder="buktiFoto">
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

    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/checking/check-connection.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/checking/confirmation.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiptrip.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiptrip-main.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiptrip-delete-shipment.js?v='.env('APP_VERSION'))}}"></script>