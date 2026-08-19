<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mismass Payment Page</title>
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
    <link rel="stylesheet" href="{{url('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('login-assets/css/main.css?v='.date('YmdHis'))}}">
    <link rel="stylesheet" type="text/css" href="{{url('assets/customs/css/custom.css?v='.date('YmdHis'))}}">
    <!--===============================================================================================-->
    <script src="https://jokul.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    <style>
        body{
            font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        .icon{
            font-size:20px;
            text-align: center;
        }
        .totalCost{
            text-align: center;    
            color: #198754;
            font-weight: bold;
            font-size: 18px;
        }
        .cost{
            font-size: 40px;
            font-weight: 700;
            color:#f86305;
        }
        .center{
            width:60%!important;
        }
        .label-inv{
            text-align:center;
            font-weight:700;
        }
        .no-inv{
            text-align:center;  
            color: #198754;
            font-weight: bold;
            font-size: 24px;
        }
    </style>
</head>

<body>


    <div class="limiter">
        <div class="container-login100">

            <div class="wrap-login100 p-t-30 p-b-50">
                <div class="login100-form validate-form p-b-33 p-t-20 p-l-20 p-r-20">
                    <img class="center" src="{{url('assets/dist/pic/logo-adm-2.png')}}"> 
                    <h2 class="text-center">Payment Page</h2>
                    <hr>
                    <div>
                        <div class="label-inv">Invoice Number</div>
                        <div class="no-inv">{{$custInvoice}}</div>
                    </div><br>
                    <table class="p-l-20">
                        <tr>
                            <td class="icon"><i class="fas fa-user"></i></td>
                            <th class="p-l-10">{{$custName}}</th>
                        </tr>
                        <tr>
                            <td class="icon"><i class="fas fa-phone"></i></td>
                            <th class="p-l-10">{{$custPhone}}</th>
                        </tr>
                        <tr>
                            <td class="icon"><i class="fas fa-at"></i></td>
                            <th class="p-l-10">{{$custEmail}}</th>
                        </tr>
                        <tr>
                            <td class="icon"><i class="fas fa-map-marker-alt"></i></td>
                            <th class="p-l-10">{{$custAddress}}</th>
                        </tr>
                    </table>

                    <div class="container-login100-form-btn m-t-32">
                        <div>
                            <div class="totalCost">Total Cost</div>
                            <div class="cost">{{$totalCost}}</div>
                        </div>
                    </div>

                    <div class="container-login100-form-btn m-t-32">
                        @if($paymentStatus=="SUCCESS")
                            <div class='bg-success text-center text-white p-l-15 p-r-15 p-b-15 p-t-15' style='border-radius:20px'><i class='fas fa-check-square' style='font-size:30px'></i> <div style='font-size:20px'>Payment Success</div></div>
                        @else
                            <button class='login100-form-btn' id='payNow'>Pay Now</button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/bootstrap/js/popper.js')}}"></script>
<!--===============================================================================================-->
<script src="{{url('login-assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->

@if($paymentStatus!="SUCCESS")
<script>
    var payNow = document.getElementById('payNow');
    // Example: the payment page will show when the button is clicked
    payNow.addEventListener('click', function () {
        loadJokulCheckout('{{$url}}'); // Replace it with the response.payment.url you retrieved from the response
    });
</script>
@endif

<script>
    setInterval(checkStatusLink,1000);

    function checkStatusLink(){
        $.ajax({
            url: location.origin+"/c/k/l",
            type: 'GET',
            data: {'link':"{{$link}}"},
            success: function(msg) {
                
                let json = JSON.parse(msg)

                if(json.status==200){
                    return true;
                }

                window.location = location.origin+"/404";
            }
        });
    }
</script>

</html>