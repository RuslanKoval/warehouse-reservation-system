<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;

class InventoryController extends Controller
{
    public function movements($sku)
    {
        return InventoryMovement::where('sku', $sku)->latest()->get();
    }
}
