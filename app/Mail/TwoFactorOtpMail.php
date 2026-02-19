<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otp,
        public readonly ?User $loginUser = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【Dify Gateway】二段階認証コード');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.two-factor-otp');
    }
}
