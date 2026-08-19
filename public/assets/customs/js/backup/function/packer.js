function packerCheck(a,b){
    return b==""?0:a;
}

function packerAt(v,t){
    if(v!=""){
        return { v: dateFormatCustom(t,0), t: 's', s: {border:borderStyleBot} };
    }

    return { v: '-', t: 's', s: {border:borderStyleBot} };
}

function packerBy(v){
    if(v!=""){
        return { v: 'Checked By '+v, t: 's', s: {font: {bold: true, color: {rgb: '008000'}},border:borderStyleMid} };
    }

    return { v: '-', t: 's', s: {border:borderStyleMid} };
}