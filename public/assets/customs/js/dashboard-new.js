$(document).ready(function(){
    $(".daterangepicker").remove();

    moment.locale("id");

    const start = moment().startOf('month');
    const end = moment();

    updateDisplay(start, end);

    $('#filterTanggal').daterangepicker({
        // opens: 'left',
        startDate: start,
        endDate: end,
        // maxSpan: { days: 30 },
        locale: {
            format: 'DD MMM YYYY',
            applyLabel: 'Filter',
            cancelLabel: 'Reset',
            customRangeLabel: 'Rentang Tanggal'
        },
        ranges: {
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '14 Hari Terakhir': [moment().subtract(13, 'days'), moment()],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
            'Semua Tahun': [moment('2023'), moment()]
        }
    }, updateDisplay);

    $("input[name='filterTanggal']").on('apply.daterangepicker', function (ev, picker) {
        filterData(picker.startDate,picker.endDate);
    });

    $("input[name='filterTanggal']").on('cancel.daterangepicker', function(ev, picker) {
        const s = moment().startOf('month'),
              e = moment();
        $('#filterTanggal').data('daterangepicker').setStartDate(s);
        $('#filterTanggal').data('daterangepicker').setEndDate(e);
        updateDisplay(s,e);
    });

    $("#filterCountry").on("change",function(){
        const filterTanggal = document.getElementById("filterTanggal").value,
              filterCountry = document.getElementById("filterCountry").value
              array = filterTanggal.split(" - ");

        $.ajax({
            type: "GET",
            url: location.origin+"/warehouse/get/list",
            data: {
                countryId : filterCountry,
                routeId : "",
                mode : "A"
            },
            success: function(msg) {
                $("#filterWarehouse").html("");
                $("#filterWarehouse").html(msg);
            }
        });
        filterData(moment(array[0],"DD MMM YYYY"),moment(array[1],"DD MMM YYYY"));
    });

    $("#filterWarehouse").on("change",function(){
        const filterTanggal = document.getElementById("filterTanggal").value,
              array = filterTanggal.split(" - ");
        filterData(moment(array[0],"DD MMM YYYY"),moment(array[1],"DD MMM YYYY"));
    });
    
    $('#filterTanggal').on('show.daterangepicker', function (ev, picker) {
        if ($('.drp-year-select').length) return;

        let years = "";
        for (let y = 2023; y <= moment().year(); y++) {
            years+="<option value="+y+">"+y+"</option>";
        }
    
        let yearSelect = "<div class='drp-year-select' style='padding:8px;border-top:1px solid #eee'>"+
                "<label style='font-size:12px'>Pilih Tahun</label>"+
                "<select id='yearPicker' class='form-control form-control-sm'>"+
                    "<option value=''>-- Pilih Tahun --</option>"+years+
                "</select>"+
            "</div>";
    
        picker.container.find('.drp-buttons').before(yearSelect);
    });
    
    $(document).on('change', '#yearPicker', function () {
        let year = $(this).val();
        if (!year) return;
    
        let picker = $('#filterTanggal').data('daterangepicker');
    
        let start = moment(`${year}-01-01`);
        let end   = moment(`${year}-12-31`);
    
        picker.setStartDate(start);
        picker.setEndDate(end);

        picker.oldStartDate = start.clone();
        picker.oldEndDate = end.clone();

        updateDisplay(start,end);

        picker.updateElement();

        $("#filterTanggal").trigger("apply.daterangepicker",picker);
        picker.hide();

        $(".daterangepicker .ranges ul li").removeClass("active");
        $(".daterangepicker").removeClass("show-calendar");
    });
    

    $("#types .nav-item .nav-link").on("click",function(){
        let id = $(this).attr("id");
        const filterTanggal = document.getElementById("filterTanggal").value,
              array = filterTanggal.split(" - ");
    
        $("#types .nav-item .nav-link").removeClass("active");
    
        if(id=="F"){
            // $("#filterTahun option[value='2023'],#filterTahun option[value='2024']").removeAttr("hidden");
            $(".tracking-section").hide();
            $(".finance-section").show();
            $(".filterBy").text("Filter By Tanggal Create Invoice");
            $("#types .nav-item #F").addClass("active");
        }else{
            $(".tracking-section").show();
            $(".finance-section").hide();
            $(".filterBy").text("Filter By Tanggal Shipment Awal");
            // $("#filterTahun option[value='2023'],#filterTahun option[value='2024']").attr("hidden",true);
            $("#types .nav-item #T").addClass("active");
        }
    
        filterData(moment(array[0],"DD MMM YYYY"),moment(array[1],"DD MMM YYYY"));
    })

    function updateDisplay(s,e) {
        $('#filterTanggal').val(s.format('DD MMM YYYY') + ' - ' + e.format('DD MMM YYYY'));
        filterData(s,e);
    }

    function filterData(s,e){
        const diffDays = moment(e).diff(moment(s), 'days') + 1;
        let c = document.getElementById("filterCountry").value,
            w = document.getElementById("filterWarehouse").value,
            t = $("#types .nav-item .active").attr("id"),
            m,filter,
            isValidDiff = checkDiffDays(diffDays);
        
        if(!isValidDiff){
            updateDisplay(start, end);
            Swal.fire({
                title: "Gagal!",
                text: "Maksimal Filter Tanggal 31 Hari",
                icon: "error"
              });
            return false;
        }

        if(diffDays<=31){
            m = "Harian";
        }else if(diffDays<=366){
            m = "Bulanan";
        }else{
            m = "Tahunan";
        }

        filter = {
                "country" : c,
                "warehouse" : w,
                "tanggalAwal" : s.format("YYYY-MM-DD"),
                "tanggalAkhir" : e.format("YYYY-MM-DD"),
                "type" : t,
                "mode" : m
            }

        loadData(filter);
    }

    function loadData(filter){

        setLoader();

        if(filter['type']=="F"){
            processFinance(filter);
        }else{
            processTracking(filter);
        }
        
    }

    function processFinance(filter){
        $("#titleTotalBeratCust").html("Loading ...");
        $("#titleTotalPendapatan").html("Loading ...");
        $.ajax({
            type: "GET",
            cache: false,
            url: location.origin+"/load/dashboard",
            data: {
                'filterCountry':filter['country'],
                'filterWarehouse':filter['warehouse'],
                'filterTanggalAwal':filter['tanggalAwal'],
                'filterTanggalAkhir':filter['tanggalAkhir'],
                'type':filter['type'],
                'mode':filter['mode']
            },
            success: function(msg) {
                loadDataFinance(msg);
            }
        });
    }

    async function processTracking(filter){
        const kinds = ["AR","PS"];
        $("#titleTotalProSuccess").html("Loading ...");
        $("#titleTotalShipRedline").html("Loading ...");
        for (const kind of kinds) {

            try {
                const response = await $.ajax({
                    type: "GET",
                    cache: false,
                    url: location.origin+"/load/dashboard",
                    data: {
                        'filterCountry':filter['country'],
                        'filterWarehouse':filter['warehouse'],
                        'filterTanggalAwal':filter['tanggalAwal'],
                        'filterTanggalAkhir':filter['tanggalAkhir'],
                        'type':filter['type'],
                        'mode':filter['mode'],
                        'kind':kind
                    },
                    success: function(msg) {
                        // let json = JSON.parse(msg);
        
                        if(kind=="AR"){
                            loadDataTrackingAllRedline(msg);
                        }else{
                            loadDataTrackingProSuccess(msg);
                        }
                    }
                });
          
                console.log(`Data ${kind} selesai:`, response);
            } catch (err) {
                console.error(`Error pada data ${kind}:`, err);
            }
    
        }
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
            totalImport = json.totalImport!=null?"Rp."+masking(json.totalImport):"Rp.0",
            totalBeratIndImport = json.totalBeratInd!=null?masking(json.totalBeratIndImport.toFixed(2))+" Kg":'0,00 Kg',
            totalBeratCorImport = json.totalBeratCor!=null?masking(json.totalBeratCorImport.toFixed(2))+" Kg":'0,00 Kg',
            totalBeratImport = json.totalBerat!=null?masking(json.totalBeratImport.toFixed(2))+' Kg':'0,00 Kg',
            totalCbmIndImport = json.totalCbmInd!=null?json.totalCbmIndImport.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
            totalCbmCorImport = json.totalCbmCor!=null?json.totalCbmCorImport.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
            totalCbmImport = json.totalCbm!=null?masking(json.totalCbmImport.toFixed(2))+' CBM':'0,00 CBM',
            totalCustomerImport = json.totalCustomer!=null?masking(json.totalCustomerImport):'0',
            totalCustomerIndImport = json.totalCustomerInd!=null?masking(json.totalCustomerIndImport):'0',
            totalCustomerCorImport = json.totalCustomerCor!=null?masking(json.totalCustomerCorImport):'0',
            totalExport = json.totalExport!=null?"Rp."+masking(json.totalExport):"Rp.0",
            totalBeratIndExport = json.totalBeratInd!=null?masking(json.totalBeratIndExport.toFixed(2))+" Kg":'0,00 Kg',
            totalBeratCorExport = json.totalBeratCor!=null?masking(json.totalBeratCorExport.toFixed(2))+" Kg":'0,00 Kg',
            totalBeratExport = json.totalBerat!=null?masking(json.totalBeratExport.toFixed(2))+' Kg':'0,00 Kg',
            totalCbmIndExport = json.totalCbmInd!=null?json.totalCbmIndExport.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
            totalCbmCorExport = json.totalCbmCor!=null?json.totalCbmCorExport.toFixed(2).replace(".",",")+" CBM":'0,00 CBM',
            totalCbmExport = json.totalCbm!=null?masking(json.totalCbmExport.toFixed(2))+' CBM':'0,00 CBM',
            totalCustomerExport = json.totalCustomer!=null?masking(json.totalCustomerExport):'0',
            totalCustomerIndExport = json.totalCustomerInd!=null?masking(json.totalCustomerIndExport):'0',
            totalCustomerCorExport = json.totalCustomerCor!=null?masking(json.totalCustomerCorExport):'0';
                

        $("#titleTotalBeratCust").html(json.dateRangeTitle+" | "+json.dateRange);
        $("#titleTotalPendapatan").html(json.dateRangeTitle+" | "+json.dateRange);

        $("#totalImport").html("Rp."+masking(json.totalPendapatanImport)+" + "+json.totalPendapatanForeignImport);
        $("#totalBeratImport").html(totalBeratImport);
        $("#totalBeratIndImport").html("Individual : "+totalBeratIndImport);
        $("#totalBeratCorImport").html("Corporate : "+totalBeratCorImport);
        $("#totalCustomerImport").html(totalCustomerImport);
        $("#totalCustomerIndImport").html("Individual : "+totalCustomerIndImport);
        $("#totalCustomerCorImport").html("Corporate : "+totalCustomerCorImport);
        $("#totalCbmImport").html(totalCbmImport);
        $("#totalCbmIndImport").html("Individual : "+totalCbmIndImport);
        $("#totalCbmCorImport").html("Corporate : "+totalCbmCorImport);
        $("#totalPendapatanImport").html("Rp."+masking(json.totalPendapatanImport));
        $("#totalPendapatanIndImport").html("Individual : Rp."+masking(json.totalPendapatanIndImport));
        $("#totalPendapatanCorImport").html("Corporate : Rp."+masking(json.totalPendapatanCorImport));
        $("#totalPendapatanForeignImport").html(json.totalPendapatanForeignImport);
        $("#totalPendapatanForeignIndImport").html("Individual : "+json.totalPendapatanForeignIndImport);
        $("#totalPendapatanForeignCorImport").html("Corporate : "+json.totalPendapatanForeignCorImport);
        $("#totalDiskonImport").html("Rp."+masking(json.totalDiskonImport));
        $("#totalDiskonIndImport").html("Individual : Rp."+masking(json.totalDiskonIndImport));
        $("#totalDiskonCorImport").html("Corporate : Rp."+masking(json.totalDiskonCorImport));
        $("#totalDiskonRpImport").html("Rp."+masking(json.totalDiskonRpImport));
        $("#totalDiskonSGDImport").html(json.totalDiskonSGDImport);
        $("#totalDiskonIndRpImport").html("Individual : Rp."+masking(json.totalDiskonIndRpImport));
        $("#totalDiskonCorRpImport").html("Corporate : Rp."+masking(json.totalDiskonCorRpImport));
        $("#totalDiskonIndSGDImport").html("Individual : "+json.totalDiskonIndSGDImport);
        $("#totalDiskonCorSGDImport").html("Corporate : "+json.totalDiskonCorSGDImport);
        if(paidStat){
            $("#totalPaidImport").html("Rp."+masking(json.totalPaidImport));
            $("#totalPaidForeignImport").html(json.totalPaidForeignImport);
        }
        $("#totalUnpaidImport").html("UNPAID Rp."+masking(json.totalUnpaidImport));
        $("#totalUnpaidForeignImport").html("UNPAID "+json.totalUnpaidForeignImport);

        $("#totalExport").html("Rp."+masking(json.totalPendapatanExport)+" + "+json.totalPendapatanForeignExport);
        $("#totalBeratExport").html(totalBeratExport);
        $("#totalBeratIndExport").html("Individual : "+totalBeratIndExport);
        $("#totalBeratCorExport").html("Corporate : "+totalBeratCorExport);
        $("#totalCustomerExport").html(totalCustomerExport);
        $("#totalCustomerIndExport").html("Individual : "+totalCustomerIndExport);
        $("#totalCustomerCorExport").html("Corporate : "+totalCustomerCorExport);
        $("#totalCbmExport").html(totalCbmExport);
        $("#totalCbmIndExport").html("Individual : "+totalCbmIndExport);
        $("#totalCbmCorExport").html("Corporate : "+totalCbmCorExport);
        $("#totalPendapatanExport").html("Rp."+masking(json.totalPendapatanExport));
        $("#totalPendapatanIndExport").html("Individual : Rp."+masking(json.totalPendapatanIndExport));
        $("#totalPendapatanCorExport").html("Corporate : Rp."+masking(json.totalPendapatanCorExport));
        $("#totalPendapatanForeignExport").html(json.totalPendapatanForeignExport);
        $("#totalPendapatanForeignIndExport").html("Individual : "+json.totalPendapatanForeignIndExport);
        $("#totalPendapatanForeignCorExport").html("Corporate : "+json.totalPendapatanForeignCorExport);
        $("#totalDiskonExport").html("Rp."+masking(json.totalDiskonExport));
        $("#totalDiskonIndExport").html("Individual : Rp."+masking(json.totalDiskonIndExport));
        $("#totalDiskonCorExport").html("Corporate : Rp."+masking(json.totalDiskonCorExport));
        $("#totalDiskonRpExport").html("Rp."+masking(json.totalDiskonRpExport));
        $("#totalDiskonSGDExport").html(json.totalDiskonSGDExport);
        $("#totalDiskonIndRpExport").html("Individual : Rp."+masking(json.totalDiskonIndRpExport));
        $("#totalDiskonCorRpExport").html("Corporate : Rp."+masking(json.totalDiskonCorRpExport));
        $("#totalDiskonIndSGDExport").html("Individual : "+json.totalDiskonIndSGDExport);
        $("#totalDiskonCorSGDExport").html("Corporate : "+json.totalDiskonCorSGDExport);
        if(paidStat){
            $("#totalPaidExport").html("Rp."+masking(json.totalPaidExport));
            $("#totalPaidForeignExport").html(json.totalPaidForeignExport);
        }
        $("#totalUnpaidExport").html("UNPAID Rp."+masking(json.totalUnpaidExport));
        $("#totalUnpaidForeignExport").html("UNPAID "+json.totalUnpaidForeignExport);
        
        $("#totalBerat").html(totalBerat);
        $("#totalBeratInd").html("Individual : "+totalBeratInd);
        $("#totalBeratCor").html("Corporate : "+totalBeratCor);

        $("#totalCustomer").html(totalCustomer);
        $("#totalCustomerInd").html("Individual : "+totalCustomerInd);
        $("#totalCustomerCor").html("Corporate : "+totalCustomerCor);
    
        $("#totalCbm").html(totalCbm);
        $("#totalCbmInd").html("Individual : "+totalCbmInd);
        $("#totalCbmCor").html("Corporate : "+totalCbmCor);

        $("#totalPendapatan").html("Rp."+masking(json.totalPendapatan));
        $("#totalPendapatanInd").html("Individual : Rp."+masking(json.totalPendapatanInd));
        $("#totalPendapatanCor").html("Corporate : Rp."+masking(json.totalPendapatanCor));

        $("#totalPendapatanForeign").html(json.totalPendapatanForeign);
        $("#totalPendapatanForeignInd").html("Individual : "+json.totalPendapatanForeignInd);
        $("#totalPendapatanForeignCor").html("Corporate : "+json.totalPendapatanForeignCor);
        
        $("#totalDiskon").html("Rp."+masking(json.totalDiskon));
        $("#totalDiskonInd").html("Individual : Rp."+masking(json.totalDiskonInd));
        $("#totalDiskonCor").html("Corporate : Rp."+masking(json.totalDiskonCor));
        $("#totalDiskonRp").html("Rp."+masking(json.totalDiskonRp));
        $("#totalDiskonSGD").html(json.totalDiskonSGD);
        $("#totalDiskonIndRp").html("Individual : Rp."+masking(json.totalDiskonIndRp));
        $("#totalDiskonCorRp").html("Corporate : Rp."+masking(json.totalDiskonCorRp));
        $("#totalDiskonIndSGD").html("Individual : "+json.totalDiskonIndSGD);
        $("#totalDiskonCorSGD").html("Corporate : "+json.totalDiskonCorSGD);

        if(paidStat){
            $("#totalPaid").html("Rp."+masking(json.totalPaid));
            $("#totalPaidForeign").html(json.totalPaidForeign);
        }
        $("#totalUnpaid").html("UNPAID Rp."+masking(json.totalUnpaid));
        $("#totalUnpaidForeign").html("UNPAID "+json.totalUnpaidForeign);

        loadDataChart([[json.dataTotalBerat,json.dataTotalCust],[json.dataTotalIncomeInd,json.dataTotalIncomeCor],[json.dataLabel]]);
    }

    function loadDataTrackingAllRedline(json){
        let totalShipment = json.totalResi!=null ? masking(json.totalResi)+" Resi" : "0 Resi",
            totalShipmentInd = json.totalResiInd!=null ? "Individual : "+masking(json.totalResiInd) : "Individual : 0",
            totalShipmentCor = json.totalResiCor!=null ? "Corporate : "+masking(json.totalResiCor) : "Corporate : 0",
            totalRedline = json.totalResiRedline!=null ? masking(json.totalResiRedline)+" Redline" : "0 Redline",
            totalRedlineInd = json.totalResiRedlineInd!=null ? "Individual : "+masking(json.totalResiRedlineInd) : "Individual : 0",
            totalRedlineCor = json.totalResiRedlineCor!=null ? "Corporate : "+masking(json.totalResiRedlineCor) : "Corporate : 0";
    
            $("#titleTotalShipRedline").html(json.dateRangeTitle+" | "+json.dateRange);
    
            $("#totalShipment").html(totalShipment);
            $("#totalShipmentInd").html(totalShipmentInd);
            $("#totalShipmentCor").html(totalShipmentCor);
    
            $("#totalRedline").html(totalRedline);
            $("#totalRedlineInd").html(totalRedlineInd);
            $("#totalRedlineCor").html(totalRedlineCor);
    
            loadDataChart([[json.dataTotalResi,json.dataTotalRedline],[],[json.dataLabel],[json.dataKind]]);
    }

    function loadDataTrackingProSuccess(json){
        let totalSukses = json.totalResiSukses!=null ? masking(json.totalResiSukses)+" Sukses" : "0 Sukses",
            totalSuksesInd = json.totalResiSuksesInd!=null ? "Individual : "+masking(json.totalResiSuksesInd) : "Individual : 0",
            totalSuksesCor = json.totalResiSuksesCor!=null ? "Corporate : "+masking(json.totalResiSuksesCor) : "Corporate : 0",
            totalProses = json.totalResiProses!=null ? masking(json.totalResiProses)+" Proses" : "0 Proses",
            totalProsesInd = json.totalResiProsesInd!=null ? "Individual : "+masking(json.totalResiProsesInd) : "Individual : 0",
            totalProsesCor = json.totalResiProsesCor!=null ? "Corporate : "+masking(json.totalResiProsesCor) : "Corporate : 0";

            $("#titleTotalProSuccess").html(json.dateRangeTitle+" | "+json.dateRange);

            $("#totalSukses").html(totalSukses);
            $("#totalSuksesInd").html(totalSuksesInd);
            $("#totalSuksesCor").html(totalSuksesCor);
            
            $("#totalProses").html(totalProses);
            $("#totalProsesInd").html(totalProsesInd);
            $("#totalProsesCor").html(totalProsesCor);

            loadDataChart([[json.dataTotalProses,json.dataTotalSukses],[],[json.dataLabel],[json.dataKind]]);
    }

    function loadDataChart(data){
        console.log(data);
        let chartId,node,color,type,chartCanvas,
            types = $("#types .nav-item .active").attr("id"),
            financeArray = [
                ["beratCustChart",["#dd3645","#28a745"],["Berat","Customer"]],
                ["pendapatanChart",["#ffc107","#0888e6"],["Individual","Corporate"]]
            ],
            trackingShipRedArray =[
                ["shipRedlineChart",["#0888e6","#dd3645"],["Shipment","Redline"]]
            ],
            trackingProSuccessArray =[
                ["proSuccessChart",["#ffc107","#28a745"],["Proses","Sukses"]]
            ],
            length = types=="F" ? financeArray.length : (data[3]=="AR" ? trackingShipRedArray.length : trackingProSuccessArray.length),
            array = types=="F" ? financeArray : (data[3]=="AR" ? trackingShipRedArray : trackingProSuccessArray);
        
        if(types=="F"){
            $(".card-body-chart").html("");
        }else{
            if(data[3]=="AR"){
                $(".card-body-shipRedlineChart").html("");
            }else{
                $(".card-body-proSuccessChart").html("");
            }
        }
    
        for (var i = 0; i <= length - 1; i++) {
            chartId = array[i][0];
            node = document.createElement("canvas");
            node.setAttribute("id", chartId);
            node.setAttribute("class", "chart");
            document.getElementsByClassName("card-body-" + chartId)[0].appendChild(node);
    
            type = setTypeChart(),
            display = true;
    
            chartCanvas = $("#" + chartId).get(0).getContext('2d');

            console.log(data[i][0]);
    
            var graphChartData = {
                labels: data[2][0],
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
                    data: data[i][0].map(normalizeNumberChart),
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
                    data: data[i][1].map(normalizeNumberChart),
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
                                    label = maskMoney(tooltipItem.yLabel.toFixed(2));
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
    
                            if(this._chart.canvas.id == "shipRedlineChart"){
                                if (tooltipItem.datasetIndex == 0) {
                                    label = maskMoney(tooltipItem.yLabel);
                                    label += " Resi";
                                    return label;
                                }
                                label = maskMoney(tooltipItem.yLabel);
                                label += " Redline";
                                return label;
                            }
    
                            if(this._chart.canvas.id == "proSuccessChart"){
                                if (tooltipItem.datasetIndex == 0) {
                                    label = maskMoney(tooltipItem.yLabel);
                                    label += " Proses";
                                    return label;
                                }
                                label = maskMoney(tooltipItem.yLabel);
                                label += " Sukses";
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

    function setTypeChart(){
        const filterTanggal = document.getElementById("filterTanggal").value,
              array = filterTanggal.split(" - "),
              start = moment(array[0],"DD MMM YYYY"),
              end = moment(array[1],"DD MMM YYYY"),
              diffDays = moment(end).diff(moment(start), 'days') + 1;

        if(diffDays<=31){
            return "bar";
        }else if(diffDays<=365){
            return "line";
        }else{
            return "line";
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

function checkDiffDays(e){
    if(e>31&&e<365){
        return false;
    }
    return true;
}

function normalizeNumberChart(v){
    let num = Number(v);

    if (isNaN(num)) return undefined;

    let val = Number(num.toFixed(2));
    return val === 0 ? undefined : val;
}