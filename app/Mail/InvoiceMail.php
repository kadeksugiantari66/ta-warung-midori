<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Nota/struk pesanan yang dikirim ke email pelanggan setelah pembayaran lunas.
 */
class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        $this->order->load(['orderItems.product', 'table', 'payment']);

        return $this->subject('Nota Pesanan #'.$this->order->queue_number.' - Warung Midori')
            ->view('emails.invoice');
    }
}
