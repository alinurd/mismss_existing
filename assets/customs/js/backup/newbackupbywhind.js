function createDataToExport(data){
    const loaderStatus = document.getElementsByClassName("loaderStatus")[0],
          rows = data.list,
          tanggalTitle = data.tanggalTitle,
          idWarehouse = data.idWarehouse,
          totalColumn = idWarehouse=="ALL WAREHOUSE" ? 25 : 24;

    loaderStatus.innerText = "Creating Header";
    console.log("Creating Header");

    let totalPendapatanAll = 0,
        totalSGDAll = 0,
        totalRpAll = 0,
        totalKomisiPackerAll = 0,
        totalKomisiDriverAll = 0,
        totalBeratAll = 0,
        totalItemAll = 0,
        totalCbmAll = 0,
        totalPaidAll = 0,
        totalUnpaidAll = 0,
        totalDiskonAll = 0;
    rows.forEach((r, idx) => {
        totalPendapatanAll += parseInt(r['invoicesubtotal']),
        totalSGDAll += parseInt(r['invoicesubtotalsgd']),
        totalRpAll += parseInt(r['invoicesubtotalrupiah']),
        totalKomisiPackerAll += parseInt(packerCheck(r['komisipackerbyberat'],r['packing_created_by'])),
        totalKomisiDriverAll += parseInt(mismassDriverCheck(r['komisidriverbyberat'],r['forwarder_id'],r['track_status_id'],r['shipping_status'])),
        totalBeratAll += parseFloat(r['invoiceweight'].toFixed(2)),
        totalItemAll += parseInt(r['invoiceitem']),
        totalCbmAll += parseInt(r['invoicecbm']),
        totalPaidAll += parseInt(r['invoicepaid']),
        totalUnpaidAll += parseInt(r['invoiceunpaid']),
        totalDiskonAll += parseInt(r['invoicediscount']);
    });

    const ws_data = [
        [data.title,"","","","","","","","",""],
        ["","","","","","","","","",""],
        [
            "Total Pendapatan",
            { v: rupiah(totalPendapatanAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Total Komisi Packer",
            { v: rupiah(totalKomisiPackerAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Total Berat Actual",
            { v: totalBeratAll.toFixed(2), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Total Profit",
            { v: rupiah(totalPaidAll-totalDiskonAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
        ],
        [
            "Total Paid",
            { v: rupiah(totalPaidAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Total Komisi Driver",
            { v: rupiah(totalKomisiDriverAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Total Berat Pembulatan",
            { v: pembulatan(totalBeratAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "Profit sudah dikurangi UNPAID dan Diskon, belum dikurangi Komisi AE dan biaya lainnya.",
            ""
        ],
        [
            "Total Unpaid",
            { v: rupiah(totalUnpaidAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "",
            "",
            "Total Item/Box",
            { v: totalItemAll, t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "",
            ""
        ],
        [
            "Total Diskon",
            { v: rupiah(totalDiskonAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "",
            "",
            "Total CBM",
            { v: totalCbmAll.toFixed(2), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "",
            ""
        ],
        [
            "",
            "",
            "",
            "",
            "Total CBM (kgs)",
            { v: totalCbmAll.toFixed(2)*100, t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },
            "",
            ""
        ],
        ["","","","","","","","","",""],
        [{ v: 'Total Pendapatan Dalam Mata Uang (Bruto)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },"","","","","","","","",""],
        ["Rupiah",{ v: rupiah(totalRpAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },"","","","","","","",""],
        ["Dollar Singapore",{ v: dollarSG(totalSGDAll), t: 's', s: {font: {bold: true, color: {rgb: '000000'}}} },"","","","","","","",""],
        ["","","","","","","","","",""],
        ["Data Shipment Periode "+tanggalTitle,"","","","",{ v: " (Berdasarkan Tanggal Create Invoice)", t: 's', s: {font: {bold: true, sz: 18,color: {rgb: '008000'}}} },"","","","",""],
        getHeaderColumnIND(idWarehouse)
    ];

    let merge = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: totalColumn } },
            { s: { r: 3, c: 6 }, e: { r: 5, c: 7 } },
            { s: { r: 8, c: 0 }, e: { r: 8, c: 9 } },
            { s: { r: 12, c: 0 }, e: { r: 12, c: 4 } },
            { s: { r: 12, c: 5 }, e: { r: 12, c: totalColumn } },
        ];

    let arrayGetBodyColumn = {
        "rows" : rows,
        "idWarehouse" : idWarehouse,
        "ws_data" : ws_data,
        "merge" : merge,
        "dataTitle" : data.title
    };
    getBodyColumnIND(arrayGetBodyColumn);
}

function getHeaderColumnIND(idWarehouse){
    if(idWarehouse=="ALL WAREHOUSE"){
        return [
            { v: 'No.', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'No. Invoice', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} }, 
            { v: 'Tanggal Create Invoice', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} }, 
            { v: 'Pembayaran', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Create Resi By', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'No. Resi', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Reference', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Data Customer', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Warehouse', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Service', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Harga Satuan', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Berat Actual (Kg)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Berat (Kg)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Item/Box', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'CBM', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'CBM (Kgs)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Harga Service', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Additional', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Diskon', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Sub Total', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Adjustment Fee', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Total Biaya', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Total SGD', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Komisi Packer', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Komisi Driver', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
            { v: 'Profit', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        ];
    }

    return [
        { v: 'No.', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'No. Invoice', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} }, 
        { v: 'Tanggal Create Invoice', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} }, 
        { v: 'Pembayaran', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Create Resi By', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'No. Resi', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Reference', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Data Customer', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Service', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Harga Satuan', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Berat Actual (Kg)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Berat (Kg)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Item/Box', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'CBM', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'CBM (Kgs)', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Harga Service', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Additional', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Diskon', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Sub Total', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Adjustment Fee', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Total Biaya', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Total SGD', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Komisi Packer', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Komisi Driver', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
        { v: 'Profit', t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyle} },
    ];
}

function getBodyColumnIND(array){
    if(array['idWarehouse']=="ALL WAREHOUSE"){
        allBodyColumnIND(array['rows'],array['ws_data'],array['merge'],array['dataTitle']);
    }else{
        nonAllBodyColumnIND(array['rows'],array['ws_data'],array['merge'],array['dataTitle']);
    }
}

function allBodyColumnIND(rows,ws_data,merge,dataTitle){
    const loaderStatus = document.getElementsByClassName("loaderStatus")[0];

    let num = 1,
        invData, komisiDriver, komisiPacker,
        mergeNum = 14,
        dataLength = rows.length;
        
    rows.forEach((r, idx) => {
        loaderStatus.innerText = "Exporting "+(num)+" / "+dataLength;
        console.log("Exporting "+(num)+" / "+dataLength);

        komisiPacker = packerCheck(r['komisipackerbyberat'],r['packing_created_by']);
        komisiDriver = mismassDriverCheck(r['komisidriverbyberat'],r['forwarder_id'],r['track_status_id'],r['shipping_status']);
        invData = {
            "bank_name" : r['bank_name'],
            "invoice_status" : r['invoice_status'],
            "payment_success_at" : r['payment_success_at'],
            "payment_success_auto_at" : r['payment_success_auto_at'],
            "shipping_created_at" : r['shipping_created_at']
        };

        driverByData = {
            "forwarder_id" : r['forwarder_id'],
            "forwarder_name" : r['forwarder_name'],
            "track_status_id" : r['track_status_id'],
            "shipping_status" : r['shipping_status'],
            "shipping_success_by" : r['shipping_success_by'],
            "shipping_updated_by" : r['shipping_updated_by'],
        }

        driverAtData = {
            "forwarder_id" : r['forwarder_id'],
            "track_status_id" : r['track_status_id'],
            "shipping_status" : r['shipping_status'],
            "shipping_success_at" : r['shipping_success_at'],
            "shipping_updated_at" : r['shipping_updated_at'],
        }

        ws_data.push([
            { v: num, t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}}, 
            { v: r['mismass_invoice_id'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop}},
            { v: dateFormatCustom(r['created_at'],2), t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            invoiceStatus(r['invoice_status']),
            { v: r['shipping_created_by'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            showForwarder(r['forwarder_id'],r['forwarder_name']),
            { v: r['reffullname'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            { v: r['cons_first_name']+" "+r['cons_middle_name']+" "+r['cons_last_name'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            { v: r['wareid']+" - "+r['warename'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},alignment:{vertical:"center",horizontal:"center"},border:borderStyleTop} },
            { v: r['service_name'], t: 's', s: {border:borderStyleTop}},
            { v: rupiah(r['service_price_per']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: parseFloat(r['invoiceweight']).toFixed(2), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: pembulatan(r['invoiceweight']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: r['invoiceitem'], t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: r['invoicecbm'].toFixed(2), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: parseFloat(r['invoicecbm'].toFixed(2))*100, t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(r['invoiceadtfee'])-parseInt(r['invoiceadditional'])+parseInt(r['invoicediscount'])), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoiceadditional']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoicediscount']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(r['invoiceadtfee'])), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoiceadtfee']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoicesubtotal']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: dollarSG(r['invoicesubtotalsgd']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(komisiPacker), t: 's', s: {border: borderStyleTop} }, 
            { v: rupiah(komisiDriver), t: 's', s: {border: borderStyleTop} }, 
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(komisiPacker)-parseInt(komisiDriver)), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
        ],
        [
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: dateFormatCustom(r['invoicedate'],0), t: 's', s: {border: borderStyleMid} },
            { v: dateFormatCustom(r['created_at'],3), t: 's', s: {border: borderStyleMid} },
            invoicePaidDate(invData),
            { v: dateFormatCustom(r['shipping_created_at'],0), t: 's', s: {border: borderStyleMid} },
            { v: r['shipping_number'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleMid} },
            customerType(r['jumlahkirim']),
            { v: r['cons_phone'], t: 's', s: {border: borderStyleMid} },
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "Total "+r['totalinvoice']+" Service", t: 's', s: {border: borderStyleMid} },
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} },  
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            packerBy(r['packing_created_by']),
            mismassDriverBy(driverByData),
            { v: "", t: 's', s: {border: borderStyleMid} }, 
        ],
        [
            { v: "", t: 's', s: {border: borderStyleBot} }, 
            { f: `HYPERLINK("${location.origin}/p/${r['mismass_invoice_link']}", "Link Invoice")`, t: 's', s: {border: borderStyleBot} },
            { v: "By : "+r['created_by'], t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: dateFormatCustom(r['shipping_created_at'],0), t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: r['cons_district']+", "+r['cons_city'], t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            packerAt(r['packing_created_by'],r['packing_created_at']),
            mismassDriverAt(driverAtData),
            { v: "", t: 's', s: {border: borderStyleBot} }
        ]);

        let mergeRow = [{ s: { r: mergeNum, c: 0 }, e: { r: (mergeNum+2), c: 0 } },
            { s: { r: mergeNum, c: 8 }, e: { r: (mergeNum+2), c: 8 } },
            { s: { r: mergeNum, c: 10 }, e: { r: (mergeNum+2), c: 10 } },
            { s: { r: mergeNum, c: 11 }, e: { r: (mergeNum+2), c: 11 } },
            { s: { r: mergeNum, c: 12 }, e: { r: (mergeNum+2), c: 12 } },
            { s: { r: mergeNum, c: 13 }, e: { r: (mergeNum+2), c: 13 } },
            { s: { r: mergeNum, c: 14 }, e: { r: (mergeNum+2), c: 14 } },
            { s: { r: mergeNum, c: 15 }, e: { r: (mergeNum+2), c: 15 } },
            { s: { r: mergeNum, c: 16 }, e: { r: (mergeNum+2), c: 16 } },
            { s: { r: mergeNum, c: 17 }, e: { r: (mergeNum+2), c: 17 } },
            { s: { r: mergeNum, c: 18 }, e: { r: (mergeNum+2), c: 18 } },
            { s: { r: mergeNum, c: 19 }, e: { r: (mergeNum+2), c: 19 } },
            { s: { r: mergeNum, c: 20 }, e: { r: (mergeNum+2), c: 20 } },
            { s: { r: mergeNum, c: 21 }, e: { r: (mergeNum+2), c: 21 } },
            { s: { r: mergeNum, c: 22 }, e: { r: (mergeNum+2), c: 22 } },
            { s: { r: mergeNum, c: 25 }, e: { r: (mergeNum+2), c: 25 } },];

        mergeRow.forEach(d => {
            merge.push(d);
        });

        mergeNum+=3;
        num++;

    });

    loaderStatus.innerText = "Finishing Create Data Export";
    console.log("Finishing Create Data Export");
    
    const ws = XLSX.utils.aoa_to_sheet(ws_data);

    // let cell = ws['A14:W14']; // Get a specific cell object
    // if (!cell) cell = {}; // Initialize style object if it doesn't exist
    // cell.s = {
    //     border:{
    //         top: { style: "thin", color: { rgb: "FF000000" } },
    //         bottom: { style: "thin", color: { rgb: "FF000000" } },
    //         left: { style: "thin", color: { rgb: "FF000000" } },
    //         right: { style: "thin", color: { rgb: "FF000000" } }
    //     }
    // };

    //Merge Cell
    ws['!merges'] = merge;
    
    if(!ws['A1']) ws['A1'] = {}; // Ensure cell A1 exists
    ws['A1'].s = {
        font: {
            name: 'Calibri', // Optional: specify font name
            sz: 18,           // Set font size to 24 points
            bold: true,       // Optional: make it bold
            color: { rgb: "000000" } // Optional: set font color
        }
    };

    if(!ws['A13']) ws['A13'] = {}; // Ensure cell A13 exists
    ws['A13'].s = {
        font: {
            name: 'Calibri', // Optional: specify font name
            sz: 18,           // Set font size to 24 points
            bold: true,       // Optional: make it bold
            color: { rgb: "000000" } // Optional: set font color
        }
    };

    // Object.keys(ws).forEach(cell => {
    // 	if (cell[0] === "!") return; // skip meta
    // 	if (cell.match(/^E\d+$/)) {  
    // 	if (!ws[cell].s) ws[cell].s = {};
    // 	ws[cell].s.alignment = { wrapText: true };
    // 	}
    // });

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, dataTitle+".xlsx");

    loaderStatus.innerText = "Create Export Data Success";
    console.log("Create Export Data Success");
}

function nonAllBodyColumnIND(rows,ws_data,merge,dataTitle){
    const loaderStatus = document.getElementsByClassName("loaderStatus")[0];

    let num = 1,
        invData, komisiDriver, komisiPacker,
        mergeNum = 14,
        dataLength = rows.length;

    rows.forEach((r, idx) => {
        loaderStatus.innerText = "Exporting "+(num)+" / "+dataLength;
        console.log("Exporting "+(num)+" / "+dataLength);

        komisiPacker = packerCheck(r['komisipackerbyberat'],r['packing_created_by']);
        komisiDriver = mismassDriverCheck(r['komisidriverbyberat'],r['forwarder_id'],r['track_status_id'],r['shipping_status']);
        invData = {
            "bank_name" : r['bank_name'],
            "invoice_status" : r['invoice_status'],
            "payment_success_at" : r['payment_success_at'],
            "payment_success_auto_at" : r['payment_success_auto_at'],
            "shipping_created_at" : r['shipping_created_at']
        };

        driverByData = {
            "forwarder_id" : r['forwarder_id'],
            "forwarder_name" : r['forwarder_name'],
            "track_status_id" : r['track_status_id'],
            "shipping_status" : r['shipping_status'],
            "shipping_success_by" : r['shipping_success_by'],
            "shipping_updated_by" : r['shipping_updated_by'],
        }

        driverAtData = {
            "forwarder_id" : r['forwarder_id'],
            "track_status_id" : r['track_status_id'],
            "shipping_status" : r['shipping_status'],
            "shipping_success_at" : r['shipping_success_at'],
            "shipping_updated_at" : r['shipping_updated_at'],
        }

        ws_data.push([
            { v: num, t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}}, 
            { v: r['mismass_invoice_id'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop}},
            { v: dateFormatCustom(r['created_at'],2), t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            invoiceStatus(r['invoice_status']),
            { v: r['shipping_created_by'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            showForwarder(r['forwarder_id'],r['forwarder_name']),
            { v: r['reffullname'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            { v: r['cons_first_name']+" "+r['cons_middle_name']+" "+r['cons_last_name'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleTop} },
            { v: r['service_name'], t: 's', s: {border:borderStyleTop}},
            { v: rupiah(r['service_price_per']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: parseFloat(r['invoiceweight']).toFixed(2), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: pembulatan(r['invoiceweight']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: r['invoiceitem'], t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: r['invoicecbm'].toFixed(2), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: parseFloat(r['invoicecbm'].toFixed(2))*100, t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(r['invoiceadtfee'])-parseInt(r['invoiceadditional'])+parseInt(r['invoicediscount'])), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoiceadditional']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoicediscount']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(r['invoiceadtfee'])), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoiceadtfee']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(r['invoicesubtotal']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: dollarSG(r['invoicesubtotalsgd']), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
            { v: rupiah(komisiPacker), t: 's', s: {border: borderStyleTop} }, 
            { v: rupiah(komisiDriver), t: 's', s: {border: borderStyleTop} }, 
            { v: rupiah(parseInt(r['invoicesubtotal'])-parseInt(komisiPacker)-parseInt(komisiDriver)), t: 's', s: {alignment:{vertical:"center",horizontal:"center"},border:borderStyle}},
        ],
        [
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: dateFormatCustom(r['invoicedate'],0), t: 's', s: {border: borderStyleMid} },
            { v: dateFormatCustom(r['created_at'],3), t: 's', s: {border: borderStyleMid} },
            invoicePaidDate(invData),
            { v: dateFormatCustom(r['shipping_created_at'],0), t: 's', s: {border: borderStyleMid} },
            { v: r['shipping_number'], t: 's', s: {font: {bold: true, color: {rgb: '000000'}},border:borderStyleMid} },
            customerType(r['jumlahkirim']),
            { v: r['cons_phone'], t: 's', s: {border: borderStyleMid} },
            { v: "Total "+r['totalinvoice']+" Service", t: 's', s: {border: borderStyleMid} },
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} },  
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            { v: "", t: 's', s: {border: borderStyleMid} }, 
            packerBy(r['packing_created_by']),
            mismassDriverBy(driverByData),
            { v: "", t: 's', s: {border: borderStyleMid} }, 
        ],
        [
            { v: "", t: 's', s: {border: borderStyleBot} }, 
            { f: `HYPERLINK("${location.origin}/p/${r['mismass_invoice_link']}", "Link Invoice")`, t: 's', s: {border: borderStyleBot} },
            { v: "By : "+r['created_by'], t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: dateFormatCustom(r['shipping_created_at'],0), t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: r['cons_district']+", "+r['cons_city'], t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            { v: "", t: 's', s: {border: borderStyleBot} },
            packerAt(r['packing_created_by'],r['packing_created_at']),
            mismassDriverAt(driverAtData),
            { v: "", t: 's', s: {border: borderStyleBot} }
        ]);

        let mergeRow = [{ s: { r: mergeNum, c: 0 }, e: { r: (mergeNum+2), c: 0 } },
            { s: { r: mergeNum, c: 9 }, e: { r: (mergeNum+2), c: 9 } },
            { s: { r: mergeNum, c: 10 }, e: { r: (mergeNum+2), c: 10 } },
            { s: { r: mergeNum, c: 11 }, e: { r: (mergeNum+2), c: 11 } },
            { s: { r: mergeNum, c: 12 }, e: { r: (mergeNum+2), c: 12 } },
            { s: { r: mergeNum, c: 13 }, e: { r: (mergeNum+2), c: 13 } },
            { s: { r: mergeNum, c: 14 }, e: { r: (mergeNum+2), c: 14 } },
            { s: { r: mergeNum, c: 15 }, e: { r: (mergeNum+2), c: 15 } },
            { s: { r: mergeNum, c: 16 }, e: { r: (mergeNum+2), c: 16 } },
            { s: { r: mergeNum, c: 17 }, e: { r: (mergeNum+2), c: 17 } },
            { s: { r: mergeNum, c: 18 }, e: { r: (mergeNum+2), c: 18 } },
            { s: { r: mergeNum, c: 19 }, e: { r: (mergeNum+2), c: 19 } },
            { s: { r: mergeNum, c: 20 }, e: { r: (mergeNum+2), c: 20 } },
            { s: { r: mergeNum, c: 21 }, e: { r: (mergeNum+2), c: 21 } },
            { s: { r: mergeNum, c: 24 }, e: { r: (mergeNum+2), c: 24 } },];

        mergeRow.forEach(d => {
            merge.push(d);
        });

        mergeNum+=3;
        num++;

    });

    loaderStatus.innerText = "Finishing Create Data Export";
    console.log("Finishing Create Data Export");
    
    const ws = XLSX.utils.aoa_to_sheet(ws_data);

    // let cell = ws['A14:W14']; // Get a specific cell object
    // if (!cell) cell = {}; // Initialize style object if it doesn't exist
    // cell.s = {
    //     border:{
    //         top: { style: "thin", color: { rgb: "FF000000" } },
    //         bottom: { style: "thin", color: { rgb: "FF000000" } },
    //         left: { style: "thin", color: { rgb: "FF000000" } },
    //         right: { style: "thin", color: { rgb: "FF000000" } }
    //     }
    // };

    //Merge Cell
    ws['!merges'] = merge;
    
    if(!ws['A1']) ws['A1'] = {}; // Ensure cell A1 exists
    ws['A1'].s = {
        font: {
            name: 'Calibri', // Optional: specify font name
            sz: 18,           // Set font size to 24 points
            bold: true,       // Optional: make it bold
            color: { rgb: "000000" } // Optional: set font color
        }
    };

    if(!ws['A13']) ws['A13'] = {}; // Ensure cell A13 exists
    ws['A13'].s = {
        font: {
            name: 'Calibri', // Optional: specify font name
            sz: 18,           // Set font size to 24 points
            bold: true,       // Optional: make it bold
            color: { rgb: "000000" } // Optional: set font color
        }
    };

    // Object.keys(ws).forEach(cell => {
    // 	if (cell[0] === "!") return; // skip meta
    // 	if (cell.match(/^E\d+$/)) {  
    // 	if (!ws[cell].s) ws[cell].s = {};
    // 	ws[cell].s.alignment = { wrapText: true };
    // 	}
    // });

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, dataTitle+".xlsx");

    loaderStatus.innerText = "Create Export Data Success";
    console.log("Create Export Data Success");
}