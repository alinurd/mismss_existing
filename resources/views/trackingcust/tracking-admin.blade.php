
 
<link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/customs/css/tracking.css?v='.date('YmdHis')) }}">
<link rel="stylesheet" href="{{ url('assets/customs/css/select2-trackresi-custom.css?v='.date('YmdHis')) }}">
<style>    
    /*body{*/
    /*    margin: 0;*/
    /*    font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";*/
    /*    background-color: #f8f8f8;*/
    /*    padding: 40px;*/
    /*}*/
    .content{
        padding:40px 0px 40px 0px;
        margin-top:80px;
    }
    .container{
        max-width: 1000px;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
</style>
<section class="content">
    <div class="container">
        <h5>Masukkan nomor resi</h5><br>
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
  </div>
</section>

<script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ url('assets/customs/js/tracking.js?v='.env('APP_VERSION')) }}"></script>
<script>
        trackingInput = document.getElementById("tracking-input"),
        clearBtn = document.getElementById("clear-btn"),
        trackingBtn = document.getElementById("tracking-btn"),
        trackingSection = document.getElementById("tracking-section"),
        shipmentDetails = document.getElementById("shipment-details"),
        waybillSelect = document.getElementById("waybill-select"),
        timelineWrapper = document.getElementById("timeline-wrapper");

    $("#waybill-select").select2({
        width: '100%',
    });

    $("#tracking-btn").on("click",function(){

        let id = $("#tracking-input").val();

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
                
                unLoadingBtn();
                resetTrackingNumber();

                if(window.location !== window.parent.location){
                    window.parent.postMessage(data, "*");
                    return false;
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

    clearBtn.addEventListener("click", () => {
      trackingInput.value = "";
      trackingSection.style.display = "none";
      shipmentDetails.innerHTML = "";
      waybillSelect.innerHTML = "";
      timelineWrapper.innerHTML = "";
      clearBtn.style.display = "none";
    });

    trackingInput.addEventListener("input", () => {
      clearBtn.style.display = trackingInput.value ? "block" : "none";
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