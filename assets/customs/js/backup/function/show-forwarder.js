function showForwarder(j,n){

    if(j==""){
        return "-";
    }

    if(j=="PICK-UP"){
        return "PICKUP SENDIRI";
    }

    if(j=="MISMASS"){
        return j;
    }

    return n;
}