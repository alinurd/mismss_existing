<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Export</title>
</head>
	<style>
		.loader-wrapper{
			display: flex;
			align-items: center;
			justify-content: center;
			height: calc(100vh - 100px);
		}
		.loader{
			color:white;
			font-size:30px;
			text-align:center;
		}
		.loaderDetail,
		.loaderDateRange{
			font-size:20px;
		}
		.loaderStatus{
			margin-top:20px;
		}
	</style>
<body style="background-color:grey">
	<input type="hidden" id="tipeCustomer" value="{{$tipeCustomer}}"/>
	<input type="hidden" id="jenisExport" value="{{$jenisExport}}"/>
	<input type="hidden" id="warehouse" value="{{$idWarehouse}}"/>
	<input type="hidden" id="corType" value="{{$corType}}"/>
	<input type="hidden" id="customer" value="{{$idCustomer}}"/>
	<input type="hidden" id="packer" value="{{$packer}}"/>
	<input type="hidden" id="driver" value="{{$driver}}"/>
	<input type="hidden" id="reference" value="{{$reference}}"/>
	<input type="hidden" id="filterCustomer" value="{{$filterCustomer}}"/>
	<input type="hidden" id="paymentStatus" value="{{$paymentStatus}}"/>
	<input type="hidden" id="tanggalAwal" value="{{$tanggalAwal}}"/>
	<input type="hidden" id="tanggalAkhir" value="{{$tanggalAkhir}}"/>
	<div class="loader-wrapper">
		<div class="loader">
			<div class="loaderTitle">{{$loaderTitle}}</div>
			<div class="loaderDetail">{{$loaderDetail}}</div>
			<div class="loaderDateRange">{{$loaderDateRange}}</div>
			<div class="loaderStatus">Loading Export...</div>
		</div>
	</div>
</body>
<script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="{{ url('assets/customs/js/xlsx.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/function/number-format.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/function/date-format.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/backup/function/customer-type.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/backup/function/invoice-status.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/backup/function/show-forwarder.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/backup/function/packer.js?v='.env('APP_VERSION')) }}"></script>
<script src="{{ url('assets/customs/js/backup/function/driver.js?v='.env('APP_VERSION')) }}"></script>
<!-- <script src="{{ url('assets/customs/js/backup/function/get-reference-name.js?v='.env('APP_VERSION')) }}"></script> -->
@if($jenisExport=="BW")
	@if($tipeCustomer=="IND")
		<script src="{{ url('assets/customs/js/backup/newbackupbywhind.js?v='.env('APP_VERSION')) }}"></script>
	@elseif($tipeCustomer=="COR")
		<script src="{{ url('assets/customs/js/backup/newbackupbywhcor.js?v='.env('APP_VERSION')) }}"></script>
	@endif
@endif
<script>
	const tipeCustomer = document.getElementById("tipeCustomer").value ?? "",
        	jenisExport = document.getElementById("jenisExport").value ?? "",
            idWarehouse = document.getElementById("warehouse").value ?? "",
            corType = document.getElementById("corType").value ?? "",
            idCustomer = document.getElementById("customer").value ?? "",
            packer = document.getElementById("packer").value ?? "",
            driver = document.getElementById("driver").value ?? "",
            reference = document.getElementById("reference").value ?? "",
            filterCustomer = document.getElementById("filterCustomer").value ?? "",
            paymentStatus = document.getElementById("paymentStatus").value ?? "",
            tanggalAwal = document.getElementById("tanggalAwal").value ?? "",
            tanggalAkhir = document.getElementById("tanggalAkhir").value ?? "",
			loader = document.getElementsByClassName("loader-wrapper")[0],
			url = location.origin+'/backup/new/download/export?tipecustomer='+tipeCustomer+'&driver='+driver+'&packer='+packer+'&reference='+reference+'&paymentStatus='+paymentStatus+'&jenisexport='+jenisExport+'&idwarehouse='+idWarehouse+'&idcustomer='+idCustomer+'&filtercustomer='+filterCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir+'&cortype='+corType,
			borderStyle = {
				top: { style: 'thin', color: { rgb: "FF000000" } },
				bottom: { style: 'thin', color: { rgb: "FF000000" } },
				left: { style: 'thin', color: { rgb: "FF000000" } },
				right: { style: 'thin', color: { rgb: "FF000000" } }
			},
			borderStyleTop = {
				top: { style: 'thin', color: { rgb: "FF000000" } },
				left: { style: 'thin', color: { rgb: "FF000000" } },
				right: { style: 'thin', color: { rgb: "FF000000" } }
			},
			borderStyleMid = {
				left: { style: 'thin', color: { rgb: "FF000000" } },
				right: { style: 'thin', color: { rgb: "FF000000" } }
			},
			borderStyleBot = {
				bottom: { style: 'thin', color: { rgb: "FF000000" } },
				left: { style: 'thin', color: { rgb: "FF000000" } },
				right: { style: 'thin', color: { rgb: "FF000000" } }
			};
	
	moment.locale("id");

	// fetch(url,{method:"GET"})
	// .then(data=>{
	// 	createDataToExport(data);
	// })
	// .catch(err=>{
	// 	loader.style.display = "none";
	// 	console.error(err);
	// });

	fetchData(url)
	.then(data=>{
		console.log("Loading Export...");
		createDataToExport(data);
	});

	async function fetchData(url) {
		try {
			const response = await fetch(url,{method:"GET"});

			if (!response.ok) { // Check if the HTTP status code indicates success
			throw new Error(`HTTP error! status: ${response.status}`);
			}

			const data = await response.json(); // Assuming JSON response
			return data;
		} catch (error) {
			console.error("Error fetching data:", error);
			// Handle the error appropriately, e.g., display an error message to the user
			return null;
		}
	}
	
	function pembulatan(a){
        if(a<=0.3&&a>0){
            return 1;
        }
        if(isFloat(a)){
            f = a - Math.floor(a);
            if (f.toFixed(2) > 0.3) {
                return Math.ceil(a); // Rounds up
            } else {
                return Math.floor(a); // Rounds down
            }
        }
        return a;
    }
    
    function isFloat(num) {
      return typeof num === 'number' && !Number.isNaN(num) && num % 1 !== 0;
    }
</script>
</html>