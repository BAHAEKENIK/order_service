<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactReply extends Mailable
{
    use Queueable, SerializesModels;

    public $replySubject;
    public $replyContent;
    public $recipientName;

    public function __construct(string $replySubject, string $replyContent, string $recipientName)
    {
        $this->replySubject = $replySubject;
        $this->replyContent = $replyContent;
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->replySubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.contact-reply',
            with: [
                'content' => $this->replyContent,
                'recipientName' => $this->recipientName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
