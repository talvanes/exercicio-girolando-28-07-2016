<?php

namespace App\Events\Invoice;

use App\Entities\Invoice;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvoiceWasCreated extends Event
{
    use SerializesModels;

    protected $invoice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public function getInvoice()
    {
        return $this->invoice;
    }
}
