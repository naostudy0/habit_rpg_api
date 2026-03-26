<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationOtpMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $otp_code;
    public CarbonInterface $expires_at;

    public function __construct(string $otp_code, CarbonInterface $expires_at)
    {
        $this->otp_code = $otp_code;
        $this->expires_at = $expires_at;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '会員登録用ワンタイムパスワード',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_otp',
        );
    }

    /**
     * @return array<int, string>
     */
    public function attachments(): array
    {
        return [];
    }
}
