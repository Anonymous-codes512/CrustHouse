<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; margin: 20px auto; max-width: 600px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; border: 1px solid #dddddd;">
                    <tr>
                        <td style="background-color: #ffbb00; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; color: white;">
                            <h1 style="margin: 0; font-size: 24px;">Confirm Your Email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #333; font-size: 20px;">Hello {{ $user->name }},</h2>
                            <p style="color: #555; font-size: 16px; line-height: 1.5;">
                                Thank you for registering. To complete your registration, please confirm your email address by clicking the button below.
                            </p>
                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 20px 0;">
                                <tr>
                                    <td align="center" bgcolor="#4CAF50" style="border-radius: 5px;">
                                        <a href="{{ $confirmationUrl }}" target="_blank" style="font-size: 16px; color: white; text-decoration: none; padding: 10px 20px; display: inline-block; background-color: #0069D9; border-radius: 5px;">
                                            Confirm Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="color: #555; font-size: 14px; line-height: 1.5;">
                                If you did not create an account, no further action is required.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #777; border-radius: 0 0 10px 10px;">
                            &copy; 2024 Crust House | All Rights Reserved
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
