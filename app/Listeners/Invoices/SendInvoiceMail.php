<?php

namespace App\Listeners\Invoices;


use App\Events\Invoice\InvoiceWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InvoiceWasCreated  $event
     * @return void
     */
    public function handle(InvoiceWasCreated $event)
    {
        Mail::send('mails.invoice', ['invoice' => $event->getInvoice()], function($message) use($event){
            $message->from('contato@dildostore.com', 'Contato Dildostore');
            $message->to($event->getInvoice()->User->email, $event->getInvoice()->User->name);
            $message->subject('Nova fatura gerada');
        });
    }
}
