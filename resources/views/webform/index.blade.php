<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newCustom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/scrollTop.css?v='.date('YmdHis')) }}">
    <title>Web Form</title>

    <style>
        label {
            font-weight: bold;
            margin-top: 5px;
        }

        input.form-check-input {
            margin-top: 9px;
        }

        form#formOrder {
            padding: 10px;
        }

        label.error {
            display: block;
            width: 100%;
        }

        #agreeTncCol,
        #agreeTncCol .form-check {
            display: flex;
            flex-direction: column;
        }

        #agreeTncCol .form-check-label {
            order: 1;
        }

        #agreeTncCol label.error {
            order: 2;
        }

    </style>
</head>
<body>
    <div class="row-detail">
        <form id="formOrder">
            <input type="hidden" id="referral" name="referral">
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
                    <select class="form-control" name="kodeNegara" id="kodeNegara" style="width:100%" required>
                        <option value="">Select Country Phone Code</option>
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
                    <div class="form-check form-switch" style="margin-left:20">
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
                    <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="secondName" id="secondName" placeholder="Full Name" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 col-12">
                    <label for="email">Kode Negara</label>
                    <select class="form-control" name="secondKodeNegara" id="secondKodeNegara" style="width:100%" required>
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
            <div class="row mt-2">
                <div class="col">
                    <label for="email">Mengetahui informasi tentang Mismass melalui?</label>
                    <select class="form-control" name="knowFrom" id="knowFrom" style="width:100%" required>
                        <option value="" hidden>How do you know About Mismass</option>
                        @foreach($bn as $b)
                            @if($b->id!=1)
                                <option value="{{$b->id}}">{{$b->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @if(env('TNC_ENABLED', false))
            <div class="row mt-2">
                <div class="col" id="agreeTncCol">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="agreeTnc" id="agreeTnc" required>
                        <label class="form-check-label" for="agreeTnc">
                            Saya telah membaca dan menyetujui <a href="#" data-toggle="modal" data-target="#tncModalId" id="tncLinkId">Syarat &amp; Ketentuan</a>
                            / I have read and agree to the <a href="#" data-toggle="modal" data-target="#tncModalEn" id="tncLinkEn">Terms &amp; Conditions</a>
                        </label>
                    </div>
                </div>
            </div>
            @endif
            <div class="row mt-2">
                <div class="col">
                    <button type="submit" class="btn btn-primary mt-2" style="width:100%">Submit</button>
                </div>
            </div>
        </form>
    </div>

    @if(env('TNC_ENABLED', false))
    <div class="modal fade" id="tncModalId" tabindex="-1" role="dialog" aria-labelledby="tncModalIdLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tncModalIdLabel">Syarat &amp; Ketentuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ url(rawurlencode('T&C_Mismass_ID.pdf')) }}#toolbar=0" style="width:100%;height:100%;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tncModalEn" tabindex="-1" role="dialog" aria-labelledby="tncModalEnLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tncModalEnLabel">Terms &amp; Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ url(rawurlencode('T&C_Mismass_EN.pdf')) }}#toolbar=0" style="width:100%;height:100%;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="{{url('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-mask/jquery.mask.min.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script src="{{url('assets/plugins/jquery-validation/additional-methods.min.js')}}"></script>
    <script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{url('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('assets/customs/js/script.js?v='.env('APP_VERSION'))}}"></script>
    <script src="{{url('assets/customs/js/webform.js?v='.env('APP_VERSION'))}}"></script>
</body>
</html>
