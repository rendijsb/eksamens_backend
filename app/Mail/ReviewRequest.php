<?php

namespace App\Mail;

use App\Models\Orders\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Kā jums patika jūsu pasūtījums? - NetNest')
            ->view('emails.review-request')
            ->with([
                'order' => $this->order,
                'customerName' => $this->order->getCustomerName(),
            ]);
    }
}
