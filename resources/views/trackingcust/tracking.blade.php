
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="https://app-mismass.com/assets/dist/pic/favicon.ico">
  <title>Shipment Tracking</title>
  <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/newCustom.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/tracking.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/select2-trackresi-custom.css?v='.date('YmdHis')) }}">
</head>
<body>
    <!-- <div class="container"> -->
        <div class="top-row">
            <div class="trackingInput">
                <input id="tracking-input" type="text" placeholder="Input number tracking" />
                <span class="clear-icon" id="clear-btn">&times;</span>
            </div>
            <button id="tracking-btn">🔍 TRACK</button>
        </div>
        <label id="tracking-input-error" class="error fail-alert is-invalid" for="tracking-input" style="display:none">Tidak Boleh Kosong</label>

        <div class="middle-row" id="tracking-section">
            <div class="middle-col">
                <label>Detail Shipment</label>
                <div class="shipment-details" id="shipment-details"></div>
            </div>
            <div class="middle-col">
                <label for="waybill-select">Select Waybill</label>
                <select id="waybill-select"></select>
                <div class="timeline-wrapper" id="timeline-wrapper"></div>
            </div>
        </div>
  <!-- </div> -->
</body>

<script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ url('assets/dist/js/adminlte.min.js') }}"></script>
<script src="{{ url('assets/customs/js/script.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/tracking.js?v='.env('APP_VERSION')) }}"></script>
<script>
    $("#waybill-select").select2({
        width: '100%',
    });

    $("#tracking-btn").on("click",function(){

        let id = $("#tracking-input").val();
        console.log(id);

        if(id==""){
            trackInputErrShow();
            return;
        }

        $.ajax({
            type: "GET",
            url: location.origin+"/shiptrip/tracking/system",
            data:{
                id:id,
            },
            beforeSend: function() {
                loadingBtn();
                resetDetailCust();
                resetTimeLine();
            },
            success: function(msg) {

                let data = JSON.parse(msg);
                
                console.log(data);
                
                unLoadingBtn();
                resetTrackingNumber();

                if(window.location !== window.parent.location){
                    window.parent.postMessage(data, "*");
                    return;
                }
                

                createElement(data,id);
            }
        });
    });

    $(document).on("change","#waybill-select",function(){
        let id = $(this).val();
        $.ajax({
            type: "GET",
            url: location.origin+"/shiptrip/tracking/system/change",
            data:{
                id:id,
            },
            beforeSend: function() {
                loadingBtn();
                resetDetailCust();
                resetTimeLine();
            },
            success: function(msg) {

                let data = JSON.parse(msg);

                if(window.location !== window.parent.location){
                    window.parent.postMessage(data, "*");
                    return;
                }

                createElement(data,id);
            }
        });
    });

    $("#tracking-input").on("keyup",function(){
        trackInputErrHide();
    });

    function loadingBtn(){
        $("#tracking-btn").text("Loading...");
    };

    function unLoadingBtn(){
        $("#tracking-btn").text("🔍 TRACK");
    };

    function resetTrackingNumber(){
        $("#tracking-input").val("");
    }

    function resetDetailCust(){
        $("#shipment-details").html("");
    }

    function resetTimeLine(){
        $("#timeline-wrapper").html("");
    }

    function trackInputErrShow(){
        $("#tracking-input").addClass("border-error");
        $("#tracking-input-error").show();
    }

    function trackInputErrHide(){
        $("#tracking-input").removeClass("border-error");
        $("#tracking-input-error").hide();
    }
</script>
</html>