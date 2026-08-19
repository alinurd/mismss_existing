function invoiceStatus(j){
    let v = { v: 'PAID', t: 's', s: {font: {bold: true, color: {rgb: '008000'}},border:borderStyleTop} };
    if(j=="UNPAID"){
        v = { v: 'UNPAID', t: 's', s: {font: {bold: true, color: {rgb: 'ff0000'}},border:borderStyleTop} };
    }

    return v;
}

function invoicePaidDate(d){
    if(d['invoice_status']=="UNPAID"){
        return { v: "", t: 's', s: {border:borderStyleMid}};
    }

    if(d['bank_name']!=""){
        if(d['payment_success_at']!="0000-00-00 00:00:00"){
            return { v: "Manual Paid : "+dateFormatCustom(d['payment_success_at'],1), t: 's', s: {border:borderStyleMid}};
        }

        return { v: "Manual Paid : "+dateFormatCustom(d['shipping_created_at'],1), t: 's', s: {border:borderStyleMid}};
    }

    if(d['payment_success_auto_at']!="0000-00-00 00:00:00"){
        return { v: "Auto Paid : "+dateFormatCustom(d['payment_success_auto_at'],1), t: 's', s: {border:borderStyleMid}};
    }

    if(d['payment_success_at']!="0000-00-00 00:00:00"){
        return { v: "Manual Paid : "+dateFormatCustom(d['payment_success_at'],1), t: 's', s: {border:borderStyleMid}};
    }

    return { v: "Manual Paid : -", t: 's', s: {border:borderStyleMid}};
}