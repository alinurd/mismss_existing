<!DOCTYPE html>
<html>
<body>
    <p>
        <div><strong>Revisi Invoice</strong></div>
        <br>
        <div>Halo, <strong>{{$emailData['fullName']}}</strong>. Below link is your copy of invoice :</div>
        <br>
        <div><a href="{{$emailData['invoiceLink']}}">{{$emailData['invoiceLink']}}</a></div>
        <br>
        <div>Invoice Date : <strong>{{$emailData['invoiceDate']}}</strong></div>
        <div>Invoice No. : <strong>{{$emailData['invoice']}}</strong></div>
        <div>Payment Status : <strong>{{$emailData['statusPay']}}</strong></div>
        <div>Customer : <strong>{{$emailData['custType']}}</strong></div>
        <div>Payment Link : <strong>{{$emailData['paymentLink']}}</strong></div>
        <br>
        <div>Please make payment at your earliest convenience before we deliver your parcel.</div>
        <br>
        <div>Thank You.</div>
        <div>www.mismasslogistic.com</div>
    </p>
</body>
</html>