function dateFormatCustom(v,m){
    //Mode
    //0 = 4-Okt-2025
    //1 = 4-Okt-2025 16:32:00
    //2 = 4 Oktober 2025
    //3 = 16:32:00

    if(v=="0000-00-00 00:00:00"){
        return "-";
    }

    if(m==0){
        return moment(v).format("DD-MMM-YYYY");
    }

    if(m==1){
        return moment(v).format("DD-MMM-YYYY HH:mm:ss");
    }

    if(m==2){
        return moment(v).format("DD MMMM YYYY");
    }
    
    if(m==3){
        return moment(v).format("HH:mm:ss");
    }
}