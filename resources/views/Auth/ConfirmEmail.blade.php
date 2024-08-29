<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body>
    <h1>Hello {{ $user->name }}</h1>
    <p>Thank you for registering. Please confirm your email address by clicking the link below:</p>
    <a href="{{ $confirmationUrl }}">Confirm Email Address</a>
    <p>If you did not create an account, no further action is required.</p>
</body>
</html>
