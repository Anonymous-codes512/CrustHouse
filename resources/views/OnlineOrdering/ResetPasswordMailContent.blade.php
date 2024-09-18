<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh;">

    <div style="
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
        width: 90%; 
        max-width: 600px; 
        background-color: #ffffff; 
        padding: 20px; 
        border-radius: 10px; 
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); 
        color: #333;">

        <p style="font-size: 16px; line-height: 1.6; margin: 0;">
            Dear <strong>{{ $users->name }}</strong>,<br>
            We received a request to reset your password for your Crust House account. If you did not request a password reset, 
            please ignore this email. Otherwise, you can reset your password using the link below:
        </p>
        
        <a href="{{ route('customerResetPasswordPage', $user->email) }}" style="
            display: inline-block; 
            padding: 10px 20px; 
            margin: 20px 0; 
            background-color: #0069D9; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 16px; 
            font-weight: bold;">
            Reset Password
        </a>
        
        <p style="font-size: 16px; line-height: 1.6; margin: 0;">
            If you have any questions or encounter any issues, feel free to reach out to us. Our support team is here to assist you.<br>
            Best regards,<br>
            Crust House Team
        </p>
    </div>

</body>
</html>
