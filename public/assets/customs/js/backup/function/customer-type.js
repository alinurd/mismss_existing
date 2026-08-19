function customerType(j){
    let v = { v: 'Customer : NC', t: 's', s: {font: {bold: true, color: {rgb: '008000'},border:borderStyleMid}} };
    if(j>1){
        v = { v: 'Customer : OC', t: 's', s: {font: {bold: true, color: {rgb: 'ff0000'}},border:borderStyleMid} };
    }

    return v;
}