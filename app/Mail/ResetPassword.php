<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $users;
 
    public function __construct($users)
    {
        $this->users = $users;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'Auth.ResetPasswordEmail',
            with: ['user' => $this->users]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
