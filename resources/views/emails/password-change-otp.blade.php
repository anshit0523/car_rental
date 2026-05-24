<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Change OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:30px;">
    <div style="max-width:600px; margin:auto; background:#ffffff; padding:30px; border-radius:12px;">
        <h2 style="color:#f97316;">Dumaguete EZE Car Rental</h2>

        <p>Hello {{ $user->name ?? 'User' }},</p>

        <p>Your OTP for changing your password is:</p>

        <h1 style="letter-spacing:6px; color:#111827;">{{ $otp }}</h1>

        <p>This OTP will expire in <strong>10 minutes</strong>.</p>

        <p style="color:#64748b;">
            If you did not request this password change, please ignore this email.
        </p>
    </div>
</body>
</html>