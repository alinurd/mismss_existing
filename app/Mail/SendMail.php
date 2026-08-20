<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = "email.index";

        if($this->emailData['modes']=="CINV"){
            $view = "email.create-invoice";
        }
        
        if($this->emailData['modes']=="EINV"){
            $view = "email.edit-invoice";
        }

        if($this->emailData['modes']=="CSHI"){
            $view = "email.create-shipment";
        }

        return new Content(
            view: $view,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if($this->emailData['modes']=="WEB" && env('TNC_ENABLED', false)){
            return [
                Attachment::fromPath(public_path('T&C_Mismass.pdf'))
                    ->as('Syarat & Ketentuan Mismass.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
