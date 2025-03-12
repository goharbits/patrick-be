

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>Hello,</p>
    <p>You requested a password reset. Click the link below to reset your password:</p>
    <a href="{{ url(config('app.frontend_url') . '/reset-password?token=' . $token ) }}">Reset Password</a>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you,</p>
    <p>Your Application Name</p>
</body>
</html>
