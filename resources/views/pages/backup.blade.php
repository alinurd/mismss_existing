<style>
    .daterangepicker.ltr.single.opensright.show-calendar{
        width:auto;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row row-middle">
            <div class="col-md-6 col-lg-6">
                @if(Auth::user()->export_all)
                    <form id="formBackup">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Rekap Data</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="tipeCustomer">Tipe Customer</label>
                                            <select name="tipeCustomer" id="tipeCustomer" class="form-control" required>
                                                <option value="" hidden>Pilih Tipe Customer</option>
                                                <option value="IND">Individual</option>
                                                <option value="COR">Corporate</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-export">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="jenisExport">Jenis Export</label>
                                            <select name="jenisExport" id="jenisExport" class="form-control">
                                                <option value="" hidden>Pilih Jenis Export</option>
                                                <option value="BW">By Warehouse</option>
                                                <option value="BC">By Customer</option>
                                                <option value="BR">By Reference</option>
                                                @if(Auth::user()->role_id!=537469 && Auth::user()->role_id!=518374)
                                                    <option value="BP">By Packer</option>
                                                    <option value="BD">By Driver</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-warehouse">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="warehouse">Warehouse</label>
                                            <select name="warehouse" id="warehouse" class="form-control">
                                                <option value="" hidden>Pilih Warehouse</option>
                                                <option value="ALL WAREHOUSE">All Warehouse</option>
                                                @foreach ($warehouse as $w)
                                                <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-corporate-type">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="corType">Tipe Export Corporate</label>
                                            <select name="corType" id="corType" class="form-control">
                                                <option value="" hidden>Pilih Tipe Export</option>
                                                <option value="ALL">Export All Data</option>
                                                <option value="PRIMARY">Export Only Primary</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-customer">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="customer">Customer</label>
                                            <select name="customer" id="customer" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Customer</option>
                                                <option value="0000001">Yusuf</option>
                                                <option value="0000002">Pujo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-driver">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="driver">Driver</label>
                                            <select name="driver" id="driver" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Driver</option>
                                                @foreach ($driver as $d)
                                                    <option value="{{$d->username}}">{{$d->fullname}} - {{$d->username}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-packer">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="packer">Packer</label>
                                            <select name="packer" id="packer" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Packer</option>
                                                @foreach ($packer as $p)
                                                    <option value="{{$p->username}}">{{$p->fullname}} - {{$p->username}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-reference">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="reference">Reference</label>
                                            <select name="reference" id="reference" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Reference</option>
                                                @foreach ($marketing as $m)
                                                    @if(Auth::user()->role_id==537469)
                                                        @if($m->id==Auth::user()->id)
                                                            <option value="{{$m->id}}">{{$m->fullname}} - {{$m->username}}</option>
                                                        @endif
                                                    @else
                                                        <option value="{{$m->id}}">{{$m->fullname}} - {{$m->username}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-paymentStatus">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="paymentStatus">Payment Status</label>
                                            <select name="paymentStatus" id="paymentStatus" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Payment Status</option>
                                                <option value="ALL">All Status</option>
                                                <option value="PAID">PAID</option>
                                                <option value="UNPAID">UNPAID</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-filterCustomer">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="filterCustomer">Filter Customer</label>
                                            <select name="filterCustomer" id="filterCustomer" class="form-control" style="width:100%">
                                                <option value="" hidden>Pilih Filter Customer</option>
                                                <option value="ALL">Created By All</option>
                                                <option value="ADM">Created By Admin</option>
                                                <option value="WEB">Created By WEBFORM</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Filter Tanggal Create Invoice</label>
                                            <div class="form-input">
                                                <input type="text" class="form-control" id="filterTanggal" name="filterTanggal" readonly required>
                                                <input type="hidden" id="tanggalAwal" name="tanggalAwal" />
                                                <input type="hidden" id="tanggalAkhir" name="tanggalAkhir" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col text-right">
                                        <!-- <a href="{{url('/backup/export/')}}" target="_blank" class="btn btn-primary">Download Excel</a> -->
                                        <button type="submit" class="btn btn-primary">Download Excel</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                @endif
                @if(Auth::user()->export_packer)
                    <form id="formBackupPacker">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Rekap Data</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="tipeCustomer">Tipe Customer</label>
                                            <select name="tipeCustomer" id="tipeCustomer" class="form-control" required>
                                                <option value="" hidden>Pilih Tipe Customer</option>
                                                <option value="IND">Individual</option>
                                                <option value="COR">Corporate</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Tanggal</label>
                                            <div class="form-input">
                                                <input type="text" class="form-control" id="tanggalAwal" name="tanggalAwal" readonly required>
                                                <span class="date-span"><i class="fas fa-arrow-right"></i></span>
                                                <input type="text" class="form-control" id="tanggalAkhir" name="tanggalAkhir" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col text-right">
                                        <!-- <a href="{{url('/backup/export/')}}" target="_blank" class="btn btn-primary">Download Excel</a> -->
                                        <button type="submit" class="btn btn-primary">Download Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>

<script src="{{ url('assets/customs/js/backup.js?v='.env('APP_VERSION')) }}"></script>