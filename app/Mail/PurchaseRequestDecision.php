<?php

namespace App\Mail;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestDecision extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PurchaseRequest $pr,
        public string $decision,
    ) {}

    public function envelope(): Envelope
    {
        $decisionLabel = ucfirst($this->decision); // 'Approved' or 'Rejected'

        return new Envelope(
            subject: "Your Purchase Request has been {$decisionLabel}: {$this->pr->title}",
        );
    }

    public function content(): Content
    {
        $poUrl = ($this->decision === 'approved' && $this->pr->purchaseOrder)
            ? route('purchase-requests.show', $this->pr->id)
            : null;

        return new Content(
            markdown: 'emails.pr-decision',
            with: [
                'pr'       => $this->pr,
                'decision' => $this->decision,
                'approver' => $this->pr->approvedBy,
                'poUrl'    => $poUrl,
            ],
        );
    }
}
