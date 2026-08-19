@include('sweetalert::alert')
<!DOCTYPE html>
<html lang="en">

<head>
    <title>MisMass Apps</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/x-icon" href="{{url('assets/dist/pic/favicon.ico')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/fonts/Linearicons-Free-v1.0.0/icon-font.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/animate/animate.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/css-hamburgers/hamburgers.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/animsition/css/animsition.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/select2/select2.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/vendor/daterangepicker/daterangepicker.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="{{url('assets/plugins/sweetalert2/sweetalert2.min.css')}}">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="{{url('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/css/main.css?v='.date('YmdHis'))}}">
    <link rel="stylesheet" type="text/css" href="{{url('assets/customs/css/custom.css?v='.date('YmdHis'))}}">
    <!--===============================================================================================-->
    <script src="{{url('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
</head>

<body>


    <div class="limiter">
        <div class="container-login100">

            <div class="wrap-login100 p-t-30 p-b-50">
                <img class="center" src="{{url('assets/dist/pic/logo-adm-2.png')}}">
                <form class="login100-form validate-form p-b-33 p-t-5" method="post" action="{{url('/auth')}}">
                    @csrf
                    <div class="wrap-input100 validate-input" data-validate="Enter username">
                        <input class="input100" type="text" name="username" value="" placeholder="Username">
                        <span class="focus-input100" data-placeholder="&#xe82a;"></span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Enter password">
                        <input class="input100" type="password" name="password" value="" placeholder="Password">
                        <span class="focus-input100" data-placeholder="&#xe80f;"></span>
                        <div class="focus-input101"><i class="fas fa-eye"></i></div>
                    </div>

                    <div class="wrap-remember-me">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="remember">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Remember Me</label>
                        </div>
                    </div>

                    <div class="container-login100-form-btn m-t-32">
                        <button class="login100-form-btn">
                            Login
                        </button>
                    </div>

                </form>
            </div>

            @if (session('status'))
            <script>
                Swal.fire(
                    'Gagal!',
                    'Username / Password Salah!',
                    'error'
                )
            </script>
            @endif

        </div>
    </div>

    <div id="dropDownSelect1"></div>

</body>

<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/animsition/js/animsition.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/bootstrap/js/popper.js')}}"></script>
<script src="{{url('login-assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/daterangepicker/moment.min.js')}}"></script>
<script src="{{url('login-assets/vendor/daterangepicker/daterangepicker.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/countdowntime/countdowntime.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/js/main.js')}}"></script>

<script>
    let look = document.getElementsByClassName("focus-input101")[0];
    let passInp = document.querySelector("input[name='password']");
    look.addEventListener("click", lookPass);

    function lookPass() {
        if (look.childNodes[0].classList.contains("fa-eye")) {
            passInp.setAttribute("type", "text");
            look.childNodes[0].classList.remove("fa-eye");
            look.childNodes[0].classList.add("fa-eye-slash");
        } else {
            passInp.setAttribute("type", "password");
            look.childNodes[0].classList.remove("fa-eye-slash");
            look.childNodes[0].classList.add("fa-eye");
        }
    }
</script>

</html>