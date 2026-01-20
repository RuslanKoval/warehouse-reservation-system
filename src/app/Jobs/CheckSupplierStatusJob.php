<?php

namespace App\Jobs;

use App\Models\Order;
use App\Status\SupplierStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class CheckSupplierStatusJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Order $order) {}

    public function handle()
    {
        if ($this->order->supplier_checks >= 2) {
            $this->order->update(['status' => Order::STATUS_FAILED]);
            return;
        }

        $response = Http::get("https://supplier/status/{$this->order->supplier_ref}");

        if ($response['status'] === SupplierStatus::STATUS_OK) {
            $this->order->update(['status' => Order::STATUS_RESERVED]);
        }

        if ($response['status'] === SupplierStatus::STATUS_FAIL) {
            $this->order->update(['status' => Order::STATUS_FAILED]);
        }

        if ($response['status'] === SupplierStatus::STATUS_DELAYED) {
            $this->order->increment('supplier_checks');
            self::dispatch($this->order)->delay(15);
        }
    }
}
