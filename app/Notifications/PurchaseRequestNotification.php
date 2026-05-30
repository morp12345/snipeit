<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Notifications\Notification;

class PurchaseRequestNotification extends Notification
{
    /**
     * @param  string  $type  'pending' | 'approved' | 'rejected'
     */
    public function __construct(
        public PurchaseRequest $purchaseRequest,
        public string $type,
    ) {}

    /**
     * Delivery channel — database only for now.
     * Add 'mail' or 'broadcast' here when ready to expand.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Payload stored in the notifications.data column (JSON).
     *
     * Keys follow the convention used by Snipe-IT's bell-icon
     * notification reader: title, message, url, type.
     */
    public function toArray(object $notifiable): array
    {
        $pr = $this->purchaseRequest;

        $message = match ($this->type) {
            'pending'  => "A new purchase request \"{$pr->title}\" is pending your approval.",
            'approved' => "Your purchase request \"{$pr->title}\" has been approved.",
            'rejected' => "Your purchase request \"{$pr->title}\" has been rejected.",
            default    => "Update on purchase request \"{$pr->title}\".",
        };

        return [
            'title'   => $this->notificationTitle(),
            'message' => $message,
            'url'     => route('purchase-requests.show', $pr->id),
            'type'    => $this->type,
        ];
    }

    private function notificationTitle(): string
    {
        return match ($this->type) {
            'pending'  => 'Purchase Request Pending Approval',
            'approved' => 'Purchase Request Approved',
            'rejected' => 'Purchase Request Rejected',
            default    => 'Purchase Request Update',
        };
    }
}
