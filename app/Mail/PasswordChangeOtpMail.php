<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangeOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public $user;

    public function __construct(string $otp, $user)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Change OTP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-change-otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}