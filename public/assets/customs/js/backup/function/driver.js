function mismassDriverCheck(a,b,c,d){
    if(b!="MISMASS"){
        return 0;
    }

    if(c!=22&&c!=13&&!d.includes("SUKSES")){
        return 0;
    }

    return a;
}

function mismassDriverBy(dt){
    if(dt['forwarder_id']!="MISMASS"){
        return { v: '-', t: 's', s: {border:borderStyleMid} };
    }

    if(dt['track_status_id']!=22&&dt['track_status_id']!=13&&!dt['shipping_status'].includes("SUKSES")){
        return { v: 'On Proses By '+dt['shipping_updated_by'], t: 's', s: {font: {bold: true, color: {rgb: 'ff0000'}},border:borderStyleMid} };
    }

    return { v: 'Selesai By '+dt['shipping_success_by'], t: 's', s: {font: {bold: true, color: {rgb: '008000'}},border:borderStyleMid} };
}

function mismassDriverAt(dt){
    if(dt['forwarder_id']!="MISMASS"){
        return { v: '-', t: 's', s: {border:borderStyleBot} };
    }

    if(dt['track_status_id']!=22&&dt['track_status_id']!=13&&!dt['shipping_status'].includes("SUKSES")){
        return { v: dateFormatCustom(dt['shipping_updated_at'],1), t: 's', s: {border:borderStyleBot} };
    }

    if(dt['shipping_success_at']!="0000-00-00 00:00:00"){
        return { v: dateFormatCustom(dt['shipping_success_at'],1), t: 's', s: {border:borderStyleBot} };
    }

    return { v: dateFormatCustom(dt['shipping_updated_at'],1), t: 's', s: {border:borderStyleBot} };

}