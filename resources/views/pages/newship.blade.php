<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Shipment</h1>
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
        <form id="formCreateShipment">
            @csrf
            <input type="hidden" name="custTypeId" id="custTypeId">
            <div class="card card-other">
                <div class="card-header card-header-ms">
                    <h3 class="card-title">Data Shipment</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-format-resi">
                        <div class="col-12 col-format-resi">
                            <label for="formatResi">Format Resi</label>
                            <select name="formatResi" id="formatResi" class="form-control" required>
                                <option value="" hidden>Pilih Format Resi</option>
                                <option value="PRM">Resi Primary</option>
                                <option value="SEC">Resi Secondary</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-anchor-track" style="display:none">
                            <label for="anchorTrack">Input Nomor Resi Primary</label>
                            <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="anchorTrack" id="anchorTrack">
                        </div>
                        <div class="col-12 col-track-id" style="display:none">
                            <label for="trackId">Nomor Resi Primary</label>
                            <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="trackId" id="trackId">
                        </div>
                    </div>
                    <div class="row row-wareserv mt-2" style="display:none">
                        <div class="col-12 col-warehouse">
                            <label for="Warehouse">Warehouse</label>
                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:100%" required>
                                <option value="">Pilih Warehouse</option>
                                @foreach ($warehouse as $w)
                                    <option value="{{$w->id}}">{{$w->id}} - {{$w->name}} - {{$w->location}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-12 col-service" style="display:none">
                            <label for="Service">Service</label>
                            <select name="service" id="service" class="form-control select2" style="width:100%">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label for="tanggalDrop">Tanggal Drop (Tanggal tiba di warehouse LN)</label>
                            <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="tanggalDrop" id="tanggalDrop" placeholder="Tanggal Drop" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-customer">
                <div class="card-header card-header-ms">
                    <h3 class="card-title">Detail Customer</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-stat-cust">
                        <div class="col">
                            <div class="form-group">
                                <label for="customer">Status Customer</label>
                                <select name="statusCust" id="statusCust" class="form-control select2" style="width:100%" required>
                                    <option value="" hidden>Pilih Customer</option>
                                    <option value="NOREG">BARU</option>
                                    <option value="REG">TERDAFTAR</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-reg">
                        <div class="col">
                            <div class="form-group">
                                <label for="Reg">Nama Customer</label>
                                <select name="regCust" id="regCust" class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row-detail">
                        <div class="row">
                            <div class="col">
                                <label for="consLabel">Consignee / Penerima</label>
                            </div>
                        </div>
                        <div class="row mt-2">
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
                                <select class="form-control select2" name="kodeNegara" id="kodeNegara" style="width:100%" required>
                                    <option value="">Pilih Kode Negara</option>
                                    @foreach($kn as $k)
                                        <option value="{{$k->id}}">(+{{$k->code}}) - {{$k->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="phone">Nomor Whatsapp</label>
                                <input type="text" class="form-control" name="phone" id="phone" onkeypress="return onlyNumberKey(event)" placeholder="Whatsapp Number" required>
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
                        <div class="row mt-2 row-check">
                            <div class="col">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sameSender">
                                    <label class="form-check-label" for="sameSender">Data sender sama dengan penerima</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 row-label-second">
                            <div class="col">
                                <label for="secondLabel">Sender / Pengirim</label>
                            </div>
                        </div>
                        <div class="row mt-2 row-detil-second">
                            <div class="col-md-12 col-12">
                                <label for="secondName">Nama Lengkap</label>
                                <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondName" id="secondName" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="row mt-2 row-phone-second">
                            <div class="col-md-6 col-12">
                                <label for="email">Kode Negara</label>
                                <select class="form-control select2" name="secondKodeNegara" id="secondKodeNegara" style="width:100%" required>
                                    <option value="">Pilih Kode Negara</option>
                                    @foreach($kn as $k)
                                        <option value="{{$k->id}}">(+{{$k->code}}) - {{$k->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="secondPhone">Nomor Telepon</label>
                                <input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" name="secondPhone" id="secondPhone" placeholder="Phone Number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <!-- <div style='font-size:25px!important;color:white;padding:0px 5px 0px 5px'><i class='fas fa-trash'></i></div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            <button type="submit" class="btn btn-primary" style="width:100%">Create Shipment & Blast</button>
        </form>
    </div>
    </form>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/newship.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/shiptrip-main.js?v='.env('APP_VERSION'))}}"></script>