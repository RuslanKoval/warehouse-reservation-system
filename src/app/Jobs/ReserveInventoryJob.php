<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReserveInventoryJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Order $order) {}

    public function handle()
    {
        DB::transaction(function () {
            $inventory = Inventory::where('sku', $this->order->sku)
                ->lockForUpdate()
                ->first();

            if ($inventory && $inventory->available_qty >= $this->order->qty) {
                $inventory->decrement('available_qty', $this->order->qty);

                InventoryMovement::create([
                    'sku' => $this->order->sku,
                    'qty' => -$this->order->qty,
                    'type' => 'reserve',
                    'order_id' => $this->order->id,
                ]);

                $this->order->update(['status' => Order::STATUS_RESERVED]);
                return;
            }

            $response = Http::post('https://supplier/reserve', [
                'sku' => $this->order->sku,
                'qty' => $this->order->qty,
            ]);

            $this->order->update([
                'status' => Order::STATUS_AWAITING,
                'supplier_ref' => $response['ref'],
            ]);

            CheckSupplierStatusJob::dispatch($this->order)->delay(15);
        });
    }
}
