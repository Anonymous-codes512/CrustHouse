<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh;">

    <div style="
        background-color: #ffffff; 
        padding: 30px; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        max-width: 500px; 
        width: 100%; 
        margin: 20px; 
        text-align: center; 
        color: #333;">

        <h2 style="color: #4CAF50; margin-top: 0;">Password Reset Request</h2>

        <p style="font-size: 16px; line-height: 1.5; margin: 20px 0;">
            Dear <strong>{{ $users->name }}</strong>,<br><br>
            We received a request to reset your password for your Tachyon account. If you did not request a password reset, 
            please ignore this email. Otherwise, you can reset your password using the button below:
        </p>

        <a href="{{ route('resetPasswordPage', $user->email) }}" style="
            display: inline-block; 
            padding: 12px 25px; 
            margin: 20px 0; 
            background-color: #007bff; 
            color: #ffffff; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer;">
            Reset Password
        </a>

        <p style="font-size: 16px; line-height: 1.5; margin: 20px 0;">
            If you have any questions or encounter any issues, feel free to reach out to us. Our support team is here to assist you.
        </p>

        <footer style="font-size: 14px; color: #777; margin-top: 20px;">
            <p style="margin: 0;">Best regards,<br>Tachyon Tech</p>
        </footer>
    </div>

</body>
</html>
