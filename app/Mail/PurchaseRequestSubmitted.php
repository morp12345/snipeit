<?php

namespace App\Mail;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseRequest $purchaseRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchase Request Pending Approval: '.$this->purchaseRequest->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pr-submitted',
            with: [
                'pr'          => $this->purchaseRequest,
                'requester'   => $this->purchaseRequest->requestedBy,
                'approvalUrl' => route('purchase-requests.show', $this->purchaseRequest->id),
            ],
        );
    }
}
