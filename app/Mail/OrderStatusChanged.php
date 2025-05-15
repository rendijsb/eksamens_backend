<?php

namespace App\Mail;

use App\Models\Orders\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $previousStatus;

    public function __construct(Order $order, string $previousStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
    }

    public function build()
    {
        $statusNames = [
            'pending' => 'Gaida apstiprinājumu',
            'processing' => 'Tiek apstrādāts',
            'completed' => 'Pabeigts',
            'cancelled' => 'Atcelts',
            'failed' => 'Neizdevās'
        ];

        $currentStatus = $statusNames[$this->order->getStatus()->value] ?? $this->order->getStatus()->value;

        return $this->subject('Pasūtījuma statuss mainījies - #' . $this->order->getOrderNumber())
            ->view('emails.order-status-changed')
            ->with([
                'order' => $this->order,
                'currentStatus' => $currentStatus,
                'customerName' => $this->order->getCustomerName(),
            ]);
    }
}
