<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expiry Reminder</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>

    <p>We wanted to remind you that your subscription will expire on {{ @$buffer_days }}.</p>

    <p>If you wish to continue using our services, please renew your subscription before the expiry date.</p>

    <p>Thank you for being a valued customer!</p>

    <p>Best regards,<br>Your Company Name</p>
</body>
</html>
