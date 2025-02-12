<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $ticketNumber;

    public function __construct($event, $user, $ticketNumber)
    {
        $this->event = $event;
        $this->user = $user;
        $this->ticketNumber = $ticketNumber;
    }

    public function build()
    {
        return $this->subject('Comprobante de Pago - Inscripción Exitosa')
                    ->markdown('emails.receipt');
    }
}
