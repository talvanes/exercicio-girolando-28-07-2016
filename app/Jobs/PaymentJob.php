<?php

namespace App\Jobs;

use App\Entities\Invoice;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class PaymentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $invoice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoice = $this->invoice;
        Mail::send('mails.payment', ['invoice' => $this->invoice], function($message) use($invoice){
            $message->from('contato@dildostore.com', 'Contato Dildostore');
            $message->to($invoice->User->email, $invoice->User->name);
        });
    }
}
