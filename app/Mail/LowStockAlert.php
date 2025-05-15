<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    public function build()
    {
        return $this->subject('⚠️ Brīdinājums par zemiem krājumiem - NetNest')
            ->view('emails.low-stock-alert')
            ->with([
                'products' => $this->products,
            ]);
    }
}
