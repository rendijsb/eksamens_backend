<?php

namespace App\Mail;

use App\Models\Orders\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Pasūtījuma apstiprinājums #' . $this->order->getOrderNumber())
            ->view('emails.order-confirmation')
            ->with([
                'order' => $this->order,
                'customerName' => $this->order->getCustomerName(),
                'orderNumber' => $this->order->getOrderNumber(),
                'totalAmount' => $this->order->getTotalAmount(),
            ]);
    }
}
