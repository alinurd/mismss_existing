$(document).ready(function(){

loadData();

// $("#filterWarehouse,#filterBulan,#filterTahun").on("change",function(){
//     loadData();
// });

$("#filterWarehouse").on("change",function(){
    loadData();
});

$("input[name='filterTanggal']").on('apply.daterangepicker', function (ev, picker) {
    loadData();
});

$("input[name='filterTanggal']").on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    loadData();
});

$("#modes .nav-item .nav-link").on("click",function(){
    let id = $(this).attr("id");

    $("#modes .nav-item .nav-link").removeClass("active");

    if(id=="F"){
        $("#filterTahun option[value='2023'],#filterTahun option[value='2024']").removeAttr("hidden");
        $(".tracking-section").hide();
        $(".finance-section").show();
        $(".filterBy").text("Filter By Tanggal Invoice Otomatis");
        $("#modes .nav-item #F").addClass("active");
    }else{
        $(".tracking-section").show();
        $(".finance-section").hide();
        $(".filterBy").text("Filter By Tanggal Shipment Awal");
        $("#filterTahun option[value='2023'],#filterTahun option[value='2024']").attr("hidden",true);
        $("#modes .nav-item #T").addClass("active");
    }

    loadData();
})

function loadData(){

    let filterWarehouse = $("#filterWarehouse").val(),
        filterTanggal = $("#filterTanggal").val(),
        // filterBulan = $("#filterBulan").val(),
        // filterTahun = $("#filterTahun").val(),
        modes = $("#modes .nav-item .active").attr("id");

    setLoader();

    $.ajax({
        type: "GET",
        url: location.origin+"/load/dashboard",
        data: {
            'filterWarehouse':filterWarehouse,
            'filterTanggal':filterTanggal,
            // 'filterBulan':filterBulan,
            // 'filterTahun':filterTahun,
            'modes':modes
        },
        success: function(msg) {
            let json = JSON.parse(msg);

            if(modes=="F"){
                loadDataFinance(json);
            }else{
                loadDataTracking(json);
            }
                
        }
    });

}

function loadDataTracking(json){
    let totalShipment = json.totalResi!=null ? masking(json.totalResi)+" Resi" : "0 Resi",
        totalShipmentInd = json.totalResiInd!=null ? "Individual : "+masking(json.totalResiInd) : "Individual : 0",
        totalShipmentCor = json.totalResiCor!=null ? "Corporate : "+masking(json.totalResiCor) : "Corporate : 0",
        totalSukses = json.totalResiSukses!=null ? masking(json.totalResiSukses)+" Sukses" : "0 Sukses",
        totalSuksesInd = json.totalResiSuksesInd!=null ? "Individual : "+masking(json.totalResiSuksesInd) : "Individual : 0",
        totalSuksesCor = json.totalResiSuksesCor!=null ? "Corporate : "+masking(json.totalResiSuksesCor) : "Corporate : 0",
        totalProses = json.totalResiProses!=null ? masking(json.totalResiProses)+" Proses" : "0 Proses",
        totalProsesInd = json.totalResiProsesInd!=null ? "Individual : "+masking(json.totalResiProsesInd) : "Individual : 0",
        totalProsesCor = json.totalResiProsesCor!=null ? "Corporate : "+masking(json.totalResiProsesCor) : "Corporate : 0",
        totalRedline = json.totalResiRedline!=null ? masking(json.totalResiRedline)+" Redline" : "0 Redline",
        totalRedlineInd = json.totalResiRedlineInd!=null ? "Individual : "+masking(json.totalResiRedlineInd) : "Individual : 0",
        totalRedlineCor = json.totalResiRedlineCor!=null ? "Corporate : "+masking(json.totalResiRedlineCor) : "Corporate : 0",
        filterTahun = json.year;
                

    $("#titleTotalShipSuccess").html("Total Shipment & Sukses | Tahun "+filterTahun);
    $("#titleTotalProRed").html("Total Proses & Redline | Tahun "+filterTahun);

    $("#totalShipment").html(totalShipment);
    $("#totalShipmentInd").html(totalShipmentInd);
    $("#totalShipmentCor").html(totalShipmentCor);

    $("#totalSukses").html(totalSukses);
    $("#totalSuksesInd").html(totalSuksesInd);
    $("#totalSuksesCor").html(totalSuksesCor);
    
    $("#totalProses").html(totalProses);
    $("#totalProsesInd").html(totalProsesInd);
    $("#totalProsesCor").html(totalProsesCor);

    $("#totalRedline").html(totalRedline);
    $("#totalRedlineInd").html(totalRedlineInd);
    $("#totalRedlineCor").html(totalRedlineCor);

    loadDataChart([[json.yearTotalResi,json.yearTotalResiSukses],[json.yearTotalResiProses,json.yearTotalResiRedline]]);
}

function loadDataFinance(json){
    let totalBeratInd = json.totalBeratInd!=null?masking(json.totalBeratInd.toFixed(2))+" Kg":'0,00 Kg',
        totalBeratCor = json.totalBeratCor!=null?masking(json.totalBeratCor.toFixed(2))+" Kg":'0,00 Kg',
        totalBerat = json.totalBerat!=null?masking(json.totalBerat.toFixed(2))+' Kg':'0,00 Kg',
        totalCbmInd = json.totalCbmInd!=null?json.totalCbmInd.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
        totalCbmCor = json.totalCbmCor!=null?json.totalCbmCor.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
        totalCbm = json.totalCbm!=null?masking(json.totalCbm.toFixed(2))+' CBM':'0,00 CBM',
        totalCustomer = json.totalCustomer!=null?masking(json.totalCustomer):'0',
        totalCustomerInd = json.totalCustomerInd!=null?masking(json.totalCustomerInd):'0',
        totalCustomerCor = json.totalCustomerCor!=null?masking(json.totalCustomerCor):'0',
        filterTahun = json.year;
                

    $("#titleTotalBeratCust").html("Total Berat & Customer | Tahun "+filterTahun);
    $("#titleTotalPendapatan").html("Total Pendapatan | Tahun "+filterTahun);

    // $("#totalBerat").html(simpleWeight(Math.round(json.totalBerat)));
    // $("#totalBeratInd").html("Individual : "+simpleWeight(Math.round(json.totalBeratInd)));
    // $("#totalBeratCor").html("Corporate : "+simpleWeight(Math.round(json.totalBeratCor)));
    
    // if($("#totalBerat").length>0){
        $("#totalBerat").html(totalBerat);
        $("#totalBeratInd").html("Individual : "+totalBeratInd);
        $("#totalBeratCor").html("Corporate : "+totalBeratCor);
    // }

    // if($("#totalCustomer").length>0){
        $("#totalCustomer").html(totalCustomer);
        $("#totalCustomerInd").html("Individual : "+totalCustomerInd);
        $("#totalCustomerCor").html("Corporate : "+totalCustomerCor);
    // }

    
    $("#totalCbm").html(totalCbm);
    $("#totalCbmInd").html("Individual : "+totalCbmInd);
    $("#totalCbmCor").html("Corporate : "+totalCbmCor);

    // if($("#totalPendapatan").length>0){
        $("#totalPendapatan").html("Rp."+masking(json.totalPendapatan));
        $("#totalPendapatanInd").html("Individual : Rp."+masking(json.totalPendapatanInd));
        $("#totalPendapatanCor").html("Corporate : Rp."+masking(json.totalPendapatanCor));
    // }

    $("#totalPendapatanForeign").html(json.totalPendapatanForeign);
    $("#totalPendapatanForeignInd").html("Individual : "+json.totalPendapatanForeignInd);
    $("#totalPendapatanForeignCor").html("Corporate : "+json.totalPendapatanForeignCor);
    
    // if($("#totalDiskon").length>0){
        $("#totalDiskon").html("Rp."+masking(json.totalDiskon));
        $("#totalDiskonInd").html("Individual : Rp."+masking(json.totalDiskonInd));
        $("#totalDiskonCor").html("Corporate : Rp."+masking(json.totalDiskonCor));
    // }

    // if($("#totalPaid").length>0){
        if(paidStat){
            $("#totalPaid").html("Rp."+masking(json.totalPaid));
            $("#totalPaidForeign").html(json.totalPaidForeign);
        }
        $("#totalUnpaid").html("UNPAID Rp."+masking(json.totalUnpaid));
        $("#totalUnpaidForeign").html("UNPAID "+json.totalUnpaidForeign);
    // }

    loadDataChart([[json.yearBerat,json.yearCustomer],[json.yearInd,json.yearCor]]);
}

function loadDataChart(data) {
    let chartId,node,color,type,chartCanvas,
        modes = $("#modes .nav-item .active").attr("id"),
        financeArray = [
            ["beratCustChart",["#dd3645","#28a745"],["Berat","Customer"]],
            ["pendapatanChart",["#ffc107","#0888e6"],["Individual","Corporate"]]
        ],
        trackingArray =[
            ["shipSuccessChart",["#0888e6","#28a745"],["Shipment","Sukses"]],
            ["proRedChart",["#ffc107","#dd3645"],["Proses","Redline"]]
        ],
        length = modes=="F" ? financeArray.length : trackingArray.length,
        array = modes=="F" ? financeArray : trackingArray;
    
    $(".card-body-chart").html("");
    // $("canvas").remove();

    for (var i = 0; i <= length - 1; i++) {

        chartId = array[i][0];
        node = document.createElement("canvas");
        node.setAttribute("id", chartId);
        node.setAttribute("class", "chart");
        document.getElementsByClassName("card-body-" + chartId)[0].appendChild(node);

        type="bar",
        display=true;

        chartCanvas = $("#" + chartId).get(0).getContext('2d');

        var graphChartData = {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: false,
                borderColor: array[i][1][0],
                pointRadius: 5,
                pointHoverRadius: 7,
                pointColor: array[i][1][0],
                pointBackgroundColor: array[i][1][0],
                backgroundColor: array[i][1][0],
                hoverBackgroundColor: 'white',
                data: data[i][0],
                label: array[i][2][0],
            }, {
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: false,
                borderColor: array[i][1][1],
                pointRadius: 5,
                pointHoverRadius: 7,
                pointColor: array[i][1][1],
                pointBackgroundColor: array[i][1][1],
                backgroundColor: array[i][1][1],
                hoverBackgroundColor: 'white',
                data: data[i][1],
                label: array[i][2][1],
            }]
        };

        var graphChartOptions = {
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }

                        if (this._chart.canvas.id == "beratCustChart") {
                            if (tooltipItem.datasetIndex == 0) {
                                label = maskMoney(Math.round(tooltipItem.yLabel));
                                label += " Kg";
                                return label;
                            }
                            label = maskMoney(tooltipItem.yLabel);
                            label += " Customer";
                            return label;
                        }
                        
                        if(this._chart.canvas.id == "pendapatanChart") {
                            label = maskMoney(tooltipItem.yLabel);
                            label = "Rp." + label;
                            return label;
                        }

                        if(this._chart.canvas.id == "shipSuccessChart"){
                            if (tooltipItem.datasetIndex == 0) {
                                label = maskMoney(tooltipItem.yLabel);
                                label += " Resi";
                                return label;
                            }
                            label = maskMoney(tooltipItem.yLabel);
                            label += " Sukses";
                            return label;
                        }

                        if(this._chart.canvas.id == "proRedChart"){
                            if (tooltipItem.datasetIndex == 0) {
                                label = maskMoney(tooltipItem.yLabel);
                                label += " Proses";
                                return label;
                            }
                            label = maskMoney(tooltipItem.yLabel);
                            label += " Redline";
                            return label;
                        }

                        return label;
                    }
                }
            },
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: display
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontColor: "#000000",
                    },
                    gridLines: {
                        display: false,
                        color: "#e7e7e7",
                        drawBorder: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        fontColor: "#000000",
                        callback: function(value, index, values) {
                            return simpleMoney(value);
                        }
                    },
                    gridLines: {
                        display: true,
                        color: "#e7e7e7",
                        drawBorder: false
                    }
                }, ]
            },
        }

        var graphChart = new Chart(chartCanvas, {
            type: type,
            data: graphChartData,
            options: graphChartOptions
        });

    }

}

function setLoader(){

    let loaderOne = "<span class='loader2'></span> Loading...",
        loaderTwo = "<div style='text-align:center'><span class='loader1'></span></div>",
        loaderThree = "Loading...";

    $("#totalBerat").html(loaderOne);
    $("#totalBeratInd").html(loaderThree);
    $("#totalBeratCor").html(loaderThree);

    $("#totalCustomer").html(loaderOne);
    $("#totalCustomerInd").html(loaderThree);
    $("#totalCustomerCor").html(loaderThree);

    $("#totalCbm").html(loaderOne);
    $("#totalCbmInd").html(loaderThree);
    $("#totalCbmCor").html(loaderThree);

    $("#totalPendapatan").html(loaderOne);
    $("#totalPendapatanInd").html(loaderThree);
    $("#totalPendapatanCor").html(loaderThree);

    $("#totalPendapatanForeign").html(loaderOne);
    $("#totalPendapatanForeignInd").html(loaderThree);
    $("#totalPendapatanForeignCor").html(loaderThree);

    $("#totalDiskon").html(loaderOne);
    $("#totalDiskonInd").html(loaderThree);
    $("#totalDiskonCor").html(loaderThree);

    $("#totalPaid").html(loaderOne);
    $("#totalUnpaid").html(loaderThree);

    $("#totalPaidForeign").html(loaderOne);
    $("#totalUnpaidForeign").html(loaderThree);

    $("#totalShipment").html(loaderOne);
    $("#totalShipmentInd").html(loaderThree);
    $("#totalShipmentCor").html(loaderThree);

    $("#totalSukses").html(loaderOne);
    $("#totalSuksesInd").html(loaderThree);
    $("#totalSuksesCor").html(loaderThree);
    
    $("#totalProses").html(loaderOne);
    $("#totalProsesInd").html(loaderThree);
    $("#totalProsesCor").html(loaderThree);

    $("#totalRedline").html(loaderOne);
    $("#totalRedlineInd").html(loaderThree);
    $("#totalRedlineCor").html(loaderThree);

    $(".card-body-chart").html(loaderTwo);
}

});