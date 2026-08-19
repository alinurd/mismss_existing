<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
	<title>Apps Kurir</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <style>
        :root{
            --driver-primary-textcolor:#212529;
            --driver-secondary-textcolor:#008000;
            --driver-dark-textcolor:#0b0c0e;
            --driver-light-textcolor:#ffffff;
            --driver-primary-bgcolor:#ffc107;
            --driver-primary-grad-bgcolor:#b88b07;
            --driver-secondary-bgcolor:#0000ff;
        }
        .main{
            margin:auto;
            max-width:450px;
            background-color:#f6f6f6;
            position:relative;
        }

        .navbar{
            color:var(--driver-primary-textcolor);
            background:var(--driver-primary-bgcolor);
            font-weight:700;
            font-size:18px;
            /* background:linear-gradient(180deg, var(--driver-primary-bgcolor) 65%, var(--driver-primary-grad-bgcolor) 115%); */
            height:50px;
            padding:10px 20px;

            max-width:inherit;
            width:100%;
            position:fixed;
            top: 0;
            z-index:999;
        }
        .hello,.action{ 
            cursor:pointer;
        }

        #page-content{
            margin-top:50px;
            min-height:100vh;
        }

        .qrcode{
            padding:5px;
            width:70px;
            height:70px;
            /* background:linear-gradient(180deg, var(--driver-primary-bgcolor) 65%, var(--driver-primary-grad-bgcolor) 115%); */
            background:var(--driver-primary-bgcolor);
            border-radius:50%;
            border-color: var(--driver-dark-textcolor);
            display:flex;
            justify-content:center;
            align-items:center;

            max-width:inherit;
            position:fixed;
            bottom:30px;
            right:15px;

            cursor:pointer;
        }

        .qrcode i{
            font-size:40px;
        }

        #btnToTop{
            display:none;
        }

        .btn-form{
            padding:5px;
            width:40px;
            height:40px;
            background:#dbdada;
            border-radius:50%;

            position:fixed;
            bottom:120px;
            right:30px;

            display:flex;
            justify-content:center;
            align-items:center;

            cursor:pointer;
        }

        .btn-form i{
            font-size:20px;
        }

        .main-menu{
            display:none;
            position:fixed;
            top:0;
            left:0;
            right:0;
            bottom:0;
            z-index: 999;
            /* From https://css.glass */
            background: rgba(0, 0, 0, 0.47);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.12);
        }

        .main-menu .wrapper{
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .main-menu .wrapper .wrapper-content{
            text-align:center;
        }

        .main-menu .wrapper .wrapper-content .gantipass,
        .main-menu .wrapper .wrapper-content .logout{
            cursor:pointer;
            border: 1px solid black;
            border-radius:10px;
        }

        .main-menu .wrapper .wrapper-content .gantipass .icon,
        .main-menu .wrapper .wrapper-content .logout .icon{
            font-size:40px;
        }

        .main-menu .closeBtn{
            position:fixed;
            top:20px;
            right:20px;
            font-size:20px;
            cursor:pointer;
        }
    </style>
</head>
<body>

    <div class="main">
        <div class="navbar">
            <div class="hello"><i class="fas fa-user-circle"></i> Hello, {{$user}}</div>
            <div class="action"></div>
        </div>
        <div id="page-content"></div>
        <div class="qrcode" data-url="{{url('/d/m/q')}}"><i class="fas fa-qrcode"></i></div>
        <div id="btnToTop">
            <div class="btn-form">
                <i class="fas fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="main-menu">
        <div class="wrapper">
            <div class="wrapper-content">
                <div class="gantipass my-3 p-2">
                    <div class="icon"><i class="fas fa-user"></i></div>
                    <div class="text">Ganti Password</div>
                </div>
                <div class="logout my-3 p-2">
                    <div class="icon"><i class="fas fa-sign-out-alt"></i></div>
                    <div class="text">Logout</div>
                </div>
            </div>
        </div>
        <div class="closeBtn"><i class="fas fa-times"></i></div>
    </div>

    <div class="modal fade" data-backdrop="static" id="modalGantiPass" tabindex="-1" role="dialog" aria-labelledby="modalGantiPassTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="formGantiPass">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Ganti Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passlama">Password Lama</label>
                                        <input type="password" class="form-control" name="passlama" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passbaru">Password Baru</label>
                                        <input type="password" class="form-control" name="passbaru" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passlama">Password Baru Lagi</label>
                                        <input type="password" class="form-control" name="passbarulagi" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</body>
<script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
<script src="{{ url('assets/plugins/jquery-validation/jquery.validate.js') }}"></script>
<script src="{{ url('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="{{ url('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ url('assets/plugins/datatables/jquery.dataTables.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}" defer></script>
<script src="{{ url('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ url('assets/plugins/moment/moment-with-locales.min.js') }}"></script>
<script src="{{ url('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('assets/customs/js/script.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/profile.js?v='.env('APP_VERSION')) }}"></script>
<script>
	    jQuery(document).ready(function($) {

      if (window.history && window.history.pushState) {

        $(window).on('popstate', function() {
          var hashLocation = location.hash;
          var hashSplit = hashLocation.split("#!/");
          var hashName = hashSplit[1];

          if (hashName !== '') {
            var hash = window.location.hash;
            if (hash === '') {
                window.location="{{url('/d/h')}}";
                return false;
            }
          }
        });

        window.history.pushState('forward', null, null);
      }

    });
	</script>
<script>
    let t = "{{ csrf_token() }}",
        v = "{{url('/d/m/l')}}",
        offset = 0,
        perLoad = 10,
        status = "shipment";
        searchText = "",
        spinner = "",
        notFound = "";

    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom',
        iconColor: 'white',
        customClass: {
            popup: 'colored-toast',
        },
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
    });

	moment.locale("id");
    
    $(".action").html("<i class='fas fa-file-excel'></i>");
    $(".action").attr("data-url","{{url('/d/m/e')}}");
    pageReload(v,t);
    
    // const redirectURL = "{{url('/d/h')}}"; 
    // history.pushState(null, null, location.href);
    // window.addEventListener("popstate", function () {
    //   history.pushState(null, null, location.href); // re-push state
    //   location.href = redirectURL; // force redirect
    // });

    $(".action,.qrcode").on("click",function(){
        let v = $(this).attr("data-url");

        $(".action").html("<i class='fas fa-file-excel'></i>");
        $(".action").attr("data-url","{{url('/d/m/e')}}");

        pageReload(v,t);
    });

    $(".hello").on("click",function(){
        $(".main-menu").show();
    });

    $(".closeBtn").on("click",function(){
        $(".main-menu").hide();
    });

    $(".logout").on("click",function(){
        logout('driver');
    });

    $(".gantipass").on("click",function(){
        $("#modalGantiPass").modal("show");
    });

    $(".modal button[data-dismiss='modal']").on("click",function(){
        $("#modalGantiPass").modal("hide");
    });

    $("#formGantiPass").validate({
        errorClass: "error fail-alert is-invalid",
        rules: {
            passlama: {
                required: true,
                minlength: 8
            },
            passbaru: {
                required: true,
                minlength: 8,
            },
            passbarulagi: {
                required: true,
                minlength: 8,
                equalTo: "input[name='passbaru']",
            },
        },
        messages: {
            passlama: {
                required: "Tidak Boleh Kosong",
                minlength: "Minimal 8 Karakter",
            },
            passbaru: {
                required: "Tidak Boleh Kosong",
                minlength: "Minimal 8 Karakter",
            },
            passbarulagi: {
                required: "Tidak Boleh Kosong",
                minlength: "Minimal 8 Karakter",
                equalTo: "Password Tidak Sama",
            },
        },
        submitHandler: function(form) {

            $.ajax({
                url: location.origin+"/d/p/g",
                type: 'POST',
                data: $(form).serialize(),
                success: function(msg) {

                    var json = JSON.parse(msg);
                    if (json.status == 200) {

                        //Notif Sukses
                        Swal.fire(json.status, json.text, 'success');
                        $('#modalGantiPass').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        $('body').css('padding-right', '0');

                    } else {

                        //Notif Gagal
                        Swal.fire(json.status, json.text, 'error');

                        //Reset Form
                        $('#formGantiPass')[0].reset();

                    }
                }
            });

        }

    });
</script>
</html>