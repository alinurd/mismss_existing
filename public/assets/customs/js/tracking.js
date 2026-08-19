function createElement(data,id){

    if(data.status==500){
        Swal.fire({
        icon: "error",
        title: "Data tidak ditemukan",
        text: "Tracking number "+id+" tidak ada.",
        });

        $("#clear-btn").hide();
        
        return;
    }

let trackingSection = document.getElementById("tracking-section"),
    shipmentDetails = document.getElementById("shipment-details"),
    waybillSelect = document.getElementById("waybill-select"),
    timelineWrapper = document.getElementById("timeline-wrapper");

    trackingSection.style.display = "flex";
    shipmentDetails.innerHTML = "";
    shipmentDetails.innerHTML = data.details;

    // console.log(data.waybills[0]);
    if(data.waybills!=""){
        waybillSelect.innerHTML = "";
        data.waybills.forEach(wb => {
            const opt = document.createElement("option");
            opt.value = wb['id'];
            opt.textContent = wb['txt'];
            waybillSelect.appendChild(opt);
        });
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

document.addEventListener("click", function(e) {
    if (e.target && e.target.id === "copyResi") {
      const shipNum = e.target.getAttribute("data-shipnum");
      navigator.clipboard.writeText(shipNum).then(() => {
        console.log("Resi disalin!");
        Swal.fire({
            title: 'Resi disalin!',
            // icon: 'info',
            position: 'top',
            showConfirmButton: false,
            timer: 500,
            customClass: {
                popup: 'small-alert',
                title: 'alert-title',
                icon: 'alert-icon'
            }
          });
      }).catch(err => {
        console.error("Gagal menyalin resi: ", err);
      });
    }
});