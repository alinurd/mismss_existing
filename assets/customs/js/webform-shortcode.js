(()=>{

    $(document).ready(function(){
        
        if (window.addEventListener) {
            window.addEventListener("message",function(msg){
                if(msg.data!=""){
                    if(msg.data.status!=""){
                        if(msg.data.id==null){
                            showNotif(msg.data)
                        }else{
                            loadData(msg.data)
                        }
                    }
                }
            });
        }

        function showNotif(data){
            if(data.status=="Success"){
                Swal.fire({
                    title : data.status, 
                    text : data.text, 
                    icon : 'success'
                }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    }
                });
                return true;
            }

            if(data.noAdmin!=null){
                Swal.fire({
                    icon: 'error',
                    title: data.status,
                    text: data.text,
                    confirmButtonText: "Chat Admin",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open("https://api.whatsapp.com/send/?phone="+data.noAdmin, '_blank');
                    }
                });
                return false;
            }

            if(data.status!=null){
                Swal.fire(data.status, data.text, 'error');
                return false;
            }
            // data.status == "Success" ? Swal.fire(data.status, data.text, 'success') : ( data.noAdmin == null ? (data.status == null ? '' : Swal.fire(data.status, data.text, 'error')) : Swal.fire({
            //     icon: 'error',
            //     title: data.status,
            //     text: data.text,
            //     confirmButtonText: "Chat Admin",
            //   }).then((result) => {
            //     if (result.isConfirmed) {
            //         window.open("https://api.whatsapp.com/send/?phone="+data.noAdmin, '_blank');
            //     }
            //   }))
        }

        const urlParams = new URLSearchParams(window.location.search);
        const myParam = urlParams.get('paramName');
        const hasParam = urlParams.has('paramName');
        const allValues = urlParams.getAll('paramName');
        let refParam = "";
        for (const [key, value] of urlParams.entries()) {
            if(`${key}`=="ref"){
                // frame.contentWindow.postMessage({refId:`${value}`}, "*");
                console.log("value : "+`${value}`);
                refParam = "?ref="+`${value}`;
            }
        }

        console.log("refParam : "+refParam);
        $("#webform1").attr("src","https://app-mismass.com/webform"+refParam);
        // $('#theElement').css("height","650px").html("<iframe id='myFrame' scrolling='no' style='width:100%;height:100%;border:none' src='https://app-mismass.com/webform"+refParam+"'></iframe>");
    
        $('#tracking-number').attr('src','https://app-mismass.com/tracking/shipment/customer');

        function loadData(data){
            let trackingSection = document.getElementById("tracking-section"),
                shipmentDetails = document.getElementById("shipment-details"),
                // waybillSelect = document.getElementById("waybill-select"),
                timelineWrapper = document.getElementById("timeline-wrapper");

                if(data.status==500){
                    Swal.fire({
                        icon: "error",
                        title: "Data tidak ditemukan",
                        text: `Tracking number tidak ada.`,
                    });
                    return;
                }

                trackingSection.style.setProperty('display', 'flex', 'important');
                shipmentDetails.innerHTML = data.details;
                if(data.waybills!=""){
                    $('#waybill-select')
                    .css("width","100%")
                    .css("height","10%")
                    .attr('src','https://app-mismass.com/tracking/shipment/customer/select?id='+data.waybills[0]['id']);

                    $("#waybill-select").prev("br").remove();
                    // waybillSelect.innerHTML = "";
                    // data.waybills.forEach(wb => {
                    //     const opt = document.createElement("option");
                    //     opt.value = wb;
                    //     opt.textContent = wb;
                    //     waybillSelect.appendChild(opt);
                    // });
                }

                timelineWrapper.innerHTML = "";
                data.timeline.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "timeline-row" + (item.active ? " active" : "");
                    div.innerHTML = `
                        <div class="timeline-left">${item.date}<br>${item.time}</div>
                        <div class="timeline-center"><div class="circle"></div></div>
                        <div class="timeline-right">
                        <div class="location">${item.location}</div>
                        <div class="desc">${item.desc}</div>
                        </div>
                    `;
                    timelineWrapper.appendChild(div);
                });

        }
    });

})()