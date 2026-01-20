<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ReserveInventoryJob;

class OrderCreatedListener
{
    public function handle(OrderCreated $event)
    {
        ReserveInventoryJob::dispatch($event->order);
    }
}
