<style>
#video-container {
    line-height: 0;
    /* background-color:black; */
}
#qr-video {
  width:100%;
}

#video-container.example-style-1 .scan-region-highlight-svg,
#video-container.example-style-1 .code-outline-highlight {
    stroke: #00FFAB !important;
}

#video-container.example-style-2 {
    position: relative;
    width: max-content;
    height: max-content;
    overflow: hidden;
}
#video-container.example-style-2 .scan-region-highlight {
    border-radius: 30px;
    outline: rgba(0, 0, 0, .25) solid 50vmax;
}
#video-container.example-style-2 .scan-region-highlight-svg {
    display: none;
}
#video-container.example-style-2 .code-outline-highlight {
    stroke: rgba(255, 255, 255, .5) !important;
    stroke-width: 15 !important;
    stroke-dasharray: none !important;
}

#flash-toggle {
    display: none;
}

button.btn.btn-primary {
    border: none !important;
    background: #ffc107 linear-gradient(180deg, #ffd665, #ffc107) repeat-x !important;
    border-radius: 10px;
    line-height: 35px;
    color:black;
}

button.btn.btn-secondary {
    border-radius: 10px !important;
    line-height: 35px !important;
    border: none !important;
}

.logout.my-3.p-2, .gantipass.my-3.p-2 {
    background: #ffc107 !important;
}
</style>

<div class="container">
    <div class="border-bottom border-dark py-2">
        <p class="text-dark text-center fs-1"></p>
        <h1 class="text-center fs-lg-7 fs-md-4 fs-3 text-dark mb-2 fw-bold popin" style="font-size:28px !important">SCAN QR-CODE</h1>
        <div class="row align-items-center gx-xl-7">
            <div class="text-center mt-3" id="camera-not" style="display: block;">
                <span class="fw-bold text-danger">Camera Not Found</span>
            </div>
            <div id="video-container" class="example-style-1">
                <video id="qr-video" disablepictureinpicture="" playsinline=""></video>
                <div class="scan-region-highlight" style="position: absolute; display: none; pointer-events: none;"><svg class="scan-region-highlight-svg" viewBox="0 0 238 238" preserveAspectRatio="none" style="position:absolute;width:100%;height:100%;left:0;top:0;fill:none;stroke:#e9b213;stroke-width:4;stroke-linecap:round;stroke-linejoin:round"><path d="M31 2H10a8 8 0 0 0-8 8v21M207 2h21a8 8 0 0 1 8 8v21m0 176v21a8 8 0 0 1-8 8h-21m-176 0H10a8 8 0 0 1-8-8v-21"></path></svg><svg class="code-outline-highlight" preserveAspectRatio="none" style="display:none;width:100%;height:100%;fill:none;stroke:#e9b213;stroke-width:5;stroke-dasharray:25;stroke-linecap:round;stroke-linejoin:round"><polygon></polygon></svg></div>
            </div>
            <div class="mt-2 text-center">    
                <span class="text-dark fw-bold">Detected QR code: </span><br><span id="cam-qr-result" class="" style="font-size:14px !important">None</span>
                <br>
                <span class="text-dark fw-bold">Last Detected at: </span><br><span id="cam-qr-result-timestamp" class="" style="font-size:14px !important">None</span>
            </div>
        </div>
    </div>
</div>

<script>
    $(".qrcode").hide();
    $(".action").html("<i class='fas fa-home'></i>");
    $(".action").attr("data-url","{{url('/d/m/l')}}");

    function cameracek(value){
        const cameraNot = document.getElementById('camera-not');
        if(value == false){
            cameraNot.style.display = "block";
        }else{
            cameraNot.style.display = "none";
        }
    }
    
    function qrCodeChecking(data){
        $.ajax({
            type: 'POST',
            data: data,
            url: location.origin+"/d/m/l/u/q",
            success: function(msg){
                let json = JSON.parse(msg);
                
                if(json.status!=200){
                    Swal.fire({
                        title: "Gagal",
                        text: json.text,
                        icon: "error"
                    });
                    return false;
                }
                
                Swal.fire({
                    title: json.text,
                    icon: "success",
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: `Update`,
                    customClass: {
                        cancelButton: 'order-1',
                        denyButton: 'order-2',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        goUpdateResiDriver({
                            id:json.id,
                            status:json.tab
                        });
                    }
                });

            }
        });
    }
    
    function goUpdateResiDriver(data){
        $.ajax({
                type: 'POST',
                data: data,
                url: location.origin+"/d/m/l/u",
                success: function(msg){
                    let json = JSON.parse(msg);

                    if(json.status!=200){
                        Swal.fire({
                            title: "Gagal",
                            text: json.text,
                            icon: "error"
                        });
                        return false;
                    }
                    
                    Swal.fire({
                        title: "Berhasil",
                        text: "Resi Berhasil Diupdate!!!",
                        icon: "success"
                    });

                }
            });
        }
</script>
<script type="module">
    import QrScanner from "https://app-mismass.com/assets/customs/js/qr-scanner.min.js";

    const video = document.getElementById('qr-video');
    const videoContainer = document.getElementById('video-container');
    const camHasCamera = document.getElementById('cam-has-camera');
    const cameraNot = document.getElementById('camera-not');
    const camList = document.getElementById('cam-list');
    const camHasFlash = document.getElementById('cam-has-flash');
    const flashToggle = document.getElementById('flash-toggle');
    const flashState = document.getElementById('flash-state');
    const camQrResult = document.getElementById('cam-qr-result');
    const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');

    // function setResult(label, result) {


    //     console.log(result.data);
    //     label.textContent = result.data;
    //     camQrResultTimestamp.textContent = new Date().toString();
    //     label.style.color = 'teal';
    //     clearTimeout(label.highlightTimeout);
    //     label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);

    // }

    // ####### Web Cam Scanning #######

    videoContainer.className = "example-style-1";

    // const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
    //     onDecodeError: error => {
    //         camQrResult.textContent = error;
    //         camQrResult.style.color = 'inherit';
    //     },
    //     highlightScanRegion: true,
    //     highlightCodeOutline: true,
        
    // });

    function setRedirectResult(label, result) {

        // var str = result.data;
        // var cek_1 = str.indexOf("http");
        // var cek_2 = str.indexOf("www.");

        // if(cek_1 > -1){

        //   window.location.replace(result.data);

        // }
        qrCodeChecking({resi:result.data});

        // alert(result.data);
        
        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        clearTimeout(label.highlightTimeout);
        label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);

    }
    const scanner = new QrScanner(video, result => setRedirectResult(camQrResult, result), {
        onDecodeError: error => {
            camQrResult.textContent = error;
            camQrResult.style.color = 'inherit';
        },
        highlightScanRegion: true,
        highlightCodeOutline: true,
        
    });

          // $('#hasilscan').modal({backdrop: 'static',keyboard: false,show: true});

    const updateFlashAvailability = () => {
        scanner.hasFlash().then(hasFlash => {
            camHasFlash.textContent = hasFlash;
            flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
        });
    };

    scanner.start().then(() => {
        updateFlashAvailability();
        // List cameras after the scanner started to avoid listCamera's stream and the scanner's stream being requested
        // at the same time which can result in listCamera's unconstrained stream also being offered to the scanner.
        // Note that we can also start the scanner after listCameras, we just have it this way around in the demo to
        // start the scanner earlier.
        QrScanner.listCameras(true).then(cameras => cameras.forEach(camera => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.text = camera.label;
            camList.add(option);
        }));
    });

    // QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);
    QrScanner.hasCamera().then(hasCamera => cameracek(hasCamera));

    
    // for debugging
    window.scanner = scanner;

    document.getElementById('scan-region-highlight-style-select').addEventListener('change', (e) => {
        videoContainer.className = e.target.value;
        scanner._updateOverlay(); // reposition the highlight because style 2 sets position: relative
    });

    document.getElementById('show-scan-region').addEventListener('change', (e) => {
        const input = e.target;
        const label = input.parentNode;
        label.parentNode.insertBefore(scanner.$canvas, label.nextSibling);
        scanner.$canvas.style.display = input.checked ? 'block' : 'none';
    });

    document.getElementById('inversion-mode-select').addEventListener('change', event => {
        scanner.setInversionMode(event.target.value);
    });

    camList.addEventListener('change', event => {
        scanner.setCamera(event.target.value).then(updateFlashAvailability);
    });

    flashToggle.addEventListener('click', () => {
        scanner.toggleFlash().then(() => flashState.textContent = scanner.isFlashOn() ? 'on' : 'off');
    });

    // document.getElementById('start-button').addEventListener('click', () => {
    //     scanner.start();
    // });

    // document.getElementById('stop-button').addEventListener('click', () => {
    //     scanner.stop();
    // });

</script>