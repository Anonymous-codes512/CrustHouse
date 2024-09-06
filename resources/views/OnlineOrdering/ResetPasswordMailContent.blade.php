<div style="
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    width: 50vw; 
    max-width: 600px; 
    background-color: #f9f9f9; 
    padding: 20px; 
    border-radius: 10px; 
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); 
    font-family: Arial, sans-serif; 
    color: #333;">
    
    <p style="font-size: 16px; line-height: 1.6;">
        Dear <strong>{{ $users->name }}</strong>, <br/>
        We received a request to reset your password for your Crust House account. If you did not request a password reset, 
        please ignore this email. Otherwise, you can reset your password using the link below: <br>
        
        <a href="{{ route('customerResetPasswordPage', $user->email) }}" style="
            display: inline-block; 
            padding: 10px 20px; 
            margin: 10px 0; 
            background-color: #4CAF50; 
            color: white; 
            cursor: pointer;
            text-decoration: none; 
            border-radius: 5px;">
            <strong>Reset Password</strong>
        </a><br>
        
        If you have any questions or encounter any issues, feel free to reach out to us. Our support team is here to assist you.<br />
        Best regards,<br />
        Crust House Team
    </p>
</div>
