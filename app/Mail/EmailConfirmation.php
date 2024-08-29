<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $confirmationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $confirmationUrl)
    {
        $this->user = $user;
        $this->confirmationUrl = $confirmationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'auth.ConfirmEmail', // Update to match the view file location
            with: [
                'user' => $this->user,
                'confirmationUrl' => $this->confirmationUrl
            ]
        );
    }

    /**
     * Get the list of attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}
