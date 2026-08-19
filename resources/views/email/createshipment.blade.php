<!DOCTYPE html>
<html>
<body>
    <p>
        <div>Halo, <strong>{{$emailData['fullName']}}</strong></div>
        <br>
        <div>Your shipping details :</div>
        <div>Shipping Number : <strong>{{$emailData['shipmentNumber']}}</strong></div>
        <div>Shipper : <strong>{{$emailData['fullName']}}</strong></div>
        <br>
        <div>For tracking your shipment, please kindly check the link below.</div>
        <div><a href="https://www.mismasslogistic.com/tracking" target="_blank">https://www.mismasslogistic.com/tracking</a></div>
        <br>
        <div>Thank You</div>
        <div><strong>MISMASS LOGISTIC</strong></div>
        <div>www.mismasslogistic.com</div>
    </p>
</body>
</html>