<!DOCTYPE html>
<html>
<body>
    <p>
        <div>Halo, <strong>{{$emailData['name']}}</strong>. We have received your registration with detail below.</div>
        <br>
        <div>Full Name : <strong>{{$emailData['name']}}</strong></div>
        <div>Whatsapp No : <strong>{{$emailData['phone']}}</strong></div>
        <div>Email : <strong>{{$emailData['email']}}</strong></div>
        <div>Full Address : <strong>{{$emailData['address']}}</strong></div>
        <br>
        <div>Kindly wait for further confirmations.</div>
        <div>Thank You.</div>
        <br>
        <div>Send from website https://www.mismasslogistic.com</div>
    </p>
</body>
</html>