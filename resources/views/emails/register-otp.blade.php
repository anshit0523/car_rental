<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px;">
    <div style="max-width: 500px; margin: auto; background: white; padding: 25px; border-radius: 12px;">
        <h2 style="color: #ff6a00;">Dumaguete EZE Car Rental</h2>

        <p>Hello,</p>

        <p>Your registration OTP is:</p>

        <h1 style="letter-spacing: 8px; color: #ff6a00;">
            {{ $otp }}
        </h1>

        <p>This OTP will expire in 10 minutes.</p>

        <p>If you did not request this, please ignore this email.</p>

        <br>

        <p>Thank you,<br>Dumaguete EZE Car Rental</p>
    </div>
</body>
</html>