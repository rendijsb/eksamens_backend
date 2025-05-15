<?php

namespace App\Mail;

use App\Models\Orders\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Maksājums saņemts - Pasūtījums #' . $this->order->getOrderNumber())
            ->view('emails.payment-confirmation')
            ->with([
                'order' => $this->order,
                'customerName' => $this->order->getCustomerName(),
            ]);
    }
}
