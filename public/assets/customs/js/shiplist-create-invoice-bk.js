
let type = "POST",
    url = location.origin+"/shiplist/buat/invoice/queue";

function ajaxPost(data, type, url) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: type,
            url: url,
            data: data,
            dataType: 'json',
            success: resolve,
            error: reject
        });
    });
}


async function queue(form) {
    try {
        let formId = "#"+form.id,
            invoiceId = "",
            templateId = "",
            uniqId = "",
            other = false,
            data = "",
            totalBiaya = 0,
            labelLength = 0,
            subTotalVal = "",
            wareValLength = $(formId+" input[name='warehouseval[]']").length;

        //Loading
        loading(form);
        $(".preloaderz .preloaderz-wrapper img").css("display","none");
        $(".preloaderz .preloaderz-wrapper .text").html("<h2>Create Invoice</h2><div>Preparing Data...</div>");

        for (let i = 0; i < wareValLength; i++) {

            let data = organizeData(i, invoiceId, uniqId, other, formId, templateId);

            // Loading progress
            $(".preloaderz .preloaderz-wrapper img").hide();
            $(".preloaderz .preloaderz-wrapper .text").html(
                `<h2>Create Invoice</h2><div>Input Data ${i+1}/${wareValLength}</div>`
            );

            let json = await ajaxPost(data, type, url);

            // Jika gagal → STOP + tampilkan sweetalert
            if (json.status == 505) {
                showQueueAlert(json);
                return; // STOP seluruh proses
            }
            
            console.log("ini adalah template ID : "+templateId);

            // Update value utk request berikutnya
            invoiceId  = json.invoiceId;
            templateId = json.templateId;
            uniqId     = json.uniqId;

            let subTotalVal = $(formId+" input[name='subTotal[]']").eq(i).val();
            totalBiaya += parseInt(subTotalVal.replaceAll(".", ""));
        }
        
        console.log("ini adalah template ID : "+templateId);

        // Jika semua Ajax A sukses → lanjut Ajax B
        await runAjaxB(invoiceId,form,uniqId,totalBiaya,templateId);

    } catch(err) {
        console.error(err);
        Swal.fire("Error", "Terjadi kesalahan AJAX", "error");
    }
}


async function runAjaxB(invoiceId,form,uniqId,totalBiaya,templateId) {
    let other = true,
        formId = "#"+form.id;

    let data = otherData(
        invoiceId, other, formId, uniqId, totalBiaya, templateId
    );

    $(".preloaderz .preloaderz-wrapper .text").html(
        `<h2>Create Invoice</h2><div>Finishing Input...</div>`
    );

    let json = await ajaxPost(data, type, url);

    $(".preloaderz .preloaderz-wrapper img").show();
    unLoading(form);

    if (json.status === 200) {
        createInvSuccess(json);
    } else {
        Swal.fire(json.title, json.text, 'error');
    }
}


function showQueueAlert(json) {
    $(".preloaderz .preloaderz-wrapper img").show();
    $(".preloaderz").hide();
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').css('padding-right', '0');

    Swal.fire({
        icon: 'warning',
        title: "Tunggu Antrian Whatsapp Blast...",
        html: `
            Now : <b>${json.now}</b><br>
            Last Created : <b>${json.createdtime}</b><br><br>
            Submit lagi dalam <b class="swalwatimer">${json.queuetime}</b> detik
        `,
        timer: json.queuetime * 1000,
        allowOutsideClick: false,
        showConfirmButton: false,

        didOpen: () => {
            const timerInterval = setInterval(() => {
                json.queuetime--;
                document.querySelector(".swalwatimer").textContent = json.queuetime;

                if (json.queuetime <= 0) {
                    clearInterval(timerInterval);
                }
            }, 1000);
        }
    });
}

function otherData(inv,ot,fid,uniqid,totalBiaya,templateId){
    let data = "",
        token = $("meta[name='csrf-token']").attr("content"),
        trackIdLength = $(fid+" input[name='trackId[]']").length;

    data += "_token="+token;
    data += "&other="+ot;
    data += "&invoiceId="+inv;
    data += "&uniqId="+uniqid;
    data += "&mismassOrderId="+$(fid+" input[name='mismassOrderId']").val();
    data += "&dbCustId="+$(fid+" input[name='dbCustId']").val();
    data += "&dbCustTypeId="+$(fid+" input[name='dbCustTypeId']").val();
    data += "&pembayaran="+$(fid+" select[name='pembayaran']").val();
    data += "&tanggalInvoice="+$(fid+" input[name='tanggalInvoice']").val();
    data += "&invoiceDoku="+$(fid+" input[name='invoiceDoku']").val();
    data += "&linkDoku="+$(fid+" input[name='linkDoku']").val();
    data += "&namaBank="+$(fid+" input[name='namaBank']").val();
    data += "&namaRekening="+$(fid+" input[name='namaRekening']").val();
    data += "&noRekening="+$(fid+" input[name='noRekening']").val();
    data += "&templateId="+$(fid+" select[name='templateId']").val();
    data += "&foreignRateValue="+$(fid+" input[name='foreignRateValue']").val();
    data += "&foreignSymbol="+$(fid+" input[name='foreignSymbol']").val();
    data += "&senderFirstName="+$(fid+" input[name='senderFirstName[]']").eq(0).val();
    data += "&senderMiddleName="+$(fid+" input[name='senderMiddleName[]']").eq(0).val();
    data += "&senderLastName="+$(fid+" input[name='senderLastName[]']").eq(0).val();
    data += "&senderEmail="+$(fid+" input[name='senderEmail[]']").eq(0).val();
    data += "&senderPhone="+$(fid+" input[name='senderPhone[]']").eq(0).val();
    data += "&senderAddress="+$(fid+" input[name='senderAddress[]']").eq(0).val();
    data += "&consFirstName="+$(fid+" input[name='consFirstName[]']").eq(0).val();
    data += "&consMiddleName="+$(fid+" input[name='consMiddleName[]']").eq(0).val();
    data += "&consLastName="+$(fid+" input[name='consLastName[]']").eq(0).val();
    data += "&consEmail="+$(fid+" input[name='consEmail[]']").eq(0).val();
    data += "&consPhone="+$(fid+" input[name='consPhone[]']").eq(0).val();
    data += "&consAddress="+$(fid+" input[name='consAddress[]']").eq(0).val();
    for(let i=0;i<trackIdLength;i++){
        data += "&trackId[]="+$(fid+" input[name='trackId[]']").eq(i).val();
    }
    data += "&totalBiaya="+totalBiaya;
    data += "&templateId="+templateId;

    return data;
}

function organizeData(i,inv,uniqid,ot,fid,tid){
    let data = "",
        token = $("meta[name='csrf-token']").attr("content");

    data += "_token="+token;
    data += "&templateId="+tid;
    data += "&other="+ot;
    data += "&invoiceId="+inv;
    data += "&uniqId="+uniqid;
    data += "&dbCustId="+$(fid+" input[name='dbCustId']").val();
    data += "&dbCustTypeId="+$(fid+" input[name='dbCustTypeId']").val();
    data += "&mismassOrderId="+$(fid+" input[name='mismassOrderId']").val();
    data += "&warehouseval="+$(fid+" input[name='warehouseval[]']").eq(i).val();
    data += "&serviceval="+$(fid+" input[name='serviceval[]']").eq(i).val();

    data += "&senderFirstName="+$(fid+" input[name='senderFirstName[]']").eq(i).val();
    data += "&senderMiddleName="+$(fid+" input[name='senderMiddleName[]']").eq(i).val();
    data += "&senderLastName="+$(fid+" input[name='senderLastName[]']").eq(i).val();
    data += "&senderEmail="+$(fid+" input[name='senderEmail[]']").eq(i).val();
    data += "&senderPhone="+$(fid+" input[name='senderPhone[]']").eq(i).val();
    data += "&senderAddress="+$(fid+" input[name='senderAddress[]']").eq(i).val();
    data += "&senderSubDistrict="+$(fid+" input[name='senderSubDistrict[]']").eq(i).val();
    data += "&senderDistrict="+$(fid+" input[name='senderDistrict[]']").eq(i).val();
    data += "&senderCity="+$(fid+" input[name='senderCity[]']").eq(i).val();
    data += "&senderProv="+$(fid+" input[name='senderProv[]']").eq(i).val();
    data += "&senderPostalCode="+$(fid+" input[name='senderPostalCode[]']").eq(i).val();

    data += "&consFirstName="+$(fid+" input[name='consFirstName[]']").eq(i).val();
    data += "&consMiddleName="+$(fid+" input[name='consMiddleName[]']").eq(i).val();
    data += "&consLastName="+$(fid+" input[name='consLastName[]']").eq(i).val();
    data += "&consEmail="+$(fid+" input[name='consEmail[]']").eq(i).val();
    data += "&consPhone="+$(fid+" input[name='consPhone[]']").eq(i).val();
    data += "&consAddress="+$(fid+" input[name='consAddress[]']").eq(i).val();
    data += "&consSubDistrict="+$(fid+" input[name='consSubDistrict[]']").eq(i).val();
    data += "&consDistrict="+$(fid+" input[name='consDistrict[]']").eq(i).val();
    data += "&consCity="+$(fid+" input[name='consCity[]']").eq(i).val();
    data += "&consProv="+$(fid+" input[name='consProv[]']").eq(i).val();
    data += "&consPostalCode="+$(fid+" input[name='consPostalCode[]']").eq(i).val();

    data += "&panjang="+$(fid+" input[name='panjang[]']").eq(i).val();
    data += "&lebar="+$(fid+" input[name='lebar[]']").eq(i).val();
    data += "&tinggi="+$(fid+" input[name='tinggi[]']").eq(i).val();
    data += "&kg="+$(fid+" input[name='kg[]']").eq(i).val();
    data += "&cbm="+$(fid+" input[name='cbm[]']").eq(i).val();
    data += "&actualKg="+$(fid+" input[name='actualKg[]']").eq(i).val();
    data += "&item="+$(fid+" input[name='item[]']").eq(i).val();
    data += "&pricePer="+$(fid+" input[name='pricePer[]']").eq(i).val();

    data += "&discount="+$(fid+" input[name='discount"+i+"']").val();
    data += "&additionalDesc="+$(fid+" input[name='additionalDesc"+i+"']").val();
    data += "&additionalNominal="+$(fid+" input[name='additionalNominal"+i+"']").val();

    data += "&packing="+$(fid+" input[name='packing"+i+"']").val();
    data += "&packingPer="+$(fid+" input[name='packingPer"+i+"']").val();
    data += "&packingTotal="+$(fid+" input[name='packingTotal"+i+"']").val();
    data += "&packingDesc="+$(fid+" input[name='packingDesc"+i+"']").val();

    data += "&import="+$(fid+" input[name='import"+i+"']").val();
    data += "&importPer="+$(fid+" input[name='importPer"+i+"']").val();
    data += "&importTotal="+$(fid+" input[name='importTotal"+i+"']").val();
    data += "&importDesc="+$(fid+" input[name='importDesc"+i+"']").val();

    data += "&document="+$(fid+" input[name='document"+i+"']").val();
    data += "&documentPer="+$(fid+" input[name='documentPer"+i+"']").val();
    data += "&documentTotal="+$(fid+" input[name='documentTotal"+i+"']").val();
    data += "&documentDesc="+$(fid+" input[name='documentDesc"+i+"']").val();

    data += "&medicine="+$(fid+" input[name='medicine"+i+"']").val();
    data += "&medicinePer="+$(fid+" input[name='medicinePer"+i+"']").val();
    data += "&medicineTotal="+$(fid+" input[name='medicineTotal"+i+"']").val();
    data += "&medicineDesc="+$(fid+" input[name='medicineDesc"+i+"']").val();
    
    data += "&insurancePriceItem="+$(fid+" input[name='insurancePriceItem"+i+"']").val();
    data += "&insurancePercent="+$(fid+" input[name='insurancePercent"+i+"']").val();
    data += "&insuranceTotal="+$(fid+" input[name='insuranceTotal"+i+"']").val();

    data += "&feePriceItem="+$(fid+" input[name='feePriceItem"+i+"']").val();
    data += "&feePercent="+$(fid+" input[name='feePercent"+i+"']").val();
    data += "&feeTotal="+$(fid+" input[name='feeTotal"+i+"']").val();
    
    data += "&taxPriceItem="+$(fid+" input[name='taxPriceItem"+i+"']").val();
    data += "&taxPercent="+$(fid+" input[name='taxPercent"+i+"']").val();
    data += "&taxTotal="+$(fid+" input[name='taxTotal"+i+"']").val();

    data += "&extraCostPrice="+$(fid+" input[name='extraCostPrice"+i+"']").val();
    data += "&extraCostDest="+$(fid+" input[name='extraCostDest"+i+"']").val();
    data += "&extraCostVendorName="+$(fid+" input[name='extraCostVendorName"+i+"']").val();
    data += "&extraCostShippingNum="+$(fid+" input[name='extraCostShippingNum"+i+"']").val();

    data += "&pickUpWeight="+$(fid+" input[name='pickUpWeight"+i+"']").val();
    data += "&pickUpCharge="+$(fid+" input[name='pickUpCharge"+i+"']").val();

    data += "&subTotal="+$(fid+" input[name='subTotal[]']").eq(i).val();

    return data;
}

function createInvSuccess(json){
    let custTypeId = $('.btn-select').attr('id');

    Swal.fire({
        icon: 'success',
        title: json.title,
        text: json.text,
        showCancelButton: true,
        reverseButtons:true,
        cancelButtonColor:"#dc3545",
        confirmButtonText: "Print Invoice",
        cancelButtonText: "Close",
    }).then((result) => {
        if (result.isConfirmed) {
            window.open(json.url, '_blank');
        }
    });

    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
    refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggal=&filterWarehouse=&filterService=","table-ct_info");
    refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=","table-status_info");

}