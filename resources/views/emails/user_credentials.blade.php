<!DOCTYPE html>
<html>

<head>
    <title>Your Account Credentials</title>
</head>

<body>
    <h1>Hello, {{ $user->name }}</h1>

    <p>Your account has been created successfully. Here are your credentials:</p>

    <p><strong>Username:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $plainPassword }}</p>

    <p>Please log in and change your password as soon as possible.</p>

    <br>
    <p>Regards,</p>
    <p>Your Company Team</p>
</body>

</html>
