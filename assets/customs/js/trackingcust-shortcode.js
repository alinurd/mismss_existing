// (()=>{

//     $(document).ready(function(){

//         $('#tracking-number').attr('src','https://app-mismass.com/tracking/shipment/customer');

//         if (window.addEventListener) {
//             window.addEventListener("message",function(msg){
//                 msg.data!=""?msg.data.status!=""?loadData(msg.data):'':'';
//             });
//         }

//         function loadData(data){
//             let trackingSection = document.getElementById("tracking-section"),
//                 shipmentDetails = document.getElementById("shipment-details"),
//                 // waybillSelect = document.getElementById("waybill-select"),
//                 timelineWrapper = document.getElementById("timeline-wrapper");

//                 if(data.status==500){
//                     Swal.fire({
//                         icon: "error",
//                         title: "Data tidak ditemukan",
//                         text: `Tracking number tidak ada.`,
//                     });
//                     return;
//                 }

//                 trackingSection.style.display = "flex";
//                 shipmentDetails.innerHTML = data.details;
//                 if(data.waybills!=""){
//                     $('#waybill-select').attr('src','https://app-mismass.com/tracking/shipment/customer/select?id='+data.waybills[0]['id']);
//                     // waybillSelect.innerHTML = "";
//                     // data.waybills.forEach(wb => {
//                     //     const opt = document.createElement("option");
//                     //     opt.value = wb['id'];
//                     //     opt.textContent = wb['txt'];
//                     //     waybillSelect.appendChild(opt);
//                     // });
//                 }

//                 timelineWrapper.innerHTML = "";
//                 data.timeline.forEach(item => {
//                     const div = document.createElement("div");
//                     div.className = "timeline-row" + (item.active ? " active" : "");
//                     div.innerHTML = `
//                         <div class="timeline-left">${item.date}<br>${item.time}</div>
//                         <div class="timeline-center"><div class="circle"></div></div>
//                         <div class="timeline-right">
//                         <div class="location">${item.location}</div>
//                         <div class="desc">${item.desc}</div>
//                         </div>
//                     `;
//                     timelineWrapper.appendChild(div);
//                 });

//         }

//     });

// })()