
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipment Tracking Select</title>
  <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/tracking.css?v='.date('YmdHis')) }}">
  <link rel="stylesheet" href="{{ url('assets/customs/css/select2-trackresi-custom.css?v='.date('YmdHis')) }}">
</head>
<body>
    <select id="waybill-select">
        @foreach($wayBills as $w)
            <option value="{{$w['id']}}">{{$w['txt']}}</option>
        @endforeach
    </select>
</body>

<script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ url('assets/customs/js/tracking.js?v='.env('APP_VERSION')) }}"></script>
<script>
    // $("#waybill-select").select2({
    //     width: '100%',
    // });

    $(document).on("change","#waybill-select",function(){
        let id = $(this).val();
        $.ajax({
            type: "GET",
            url: location.origin+"/shiptrip/tracking/system/change",
            data:{
                id:id,
            },
            beforeSend: function() {
                resetDetailCust();
                resetTimeLine();
            },
            success: function(msg) {

                let data = JSON.parse(msg);

                if(window.location !== window.parent.location){
                    window.parent.postMessage(data, "*");
                    return;
                }

                console.log(data);
            }
        });
        
        function resetDetailCust(){
            $("#shipment-details").html("");
        }

        function resetTimeLine(){
            $("#timeline-wrapper").html("");
        }
    });
</script>
</html>