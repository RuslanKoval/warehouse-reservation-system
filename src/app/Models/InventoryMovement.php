<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryMovement
 *
 * @property string $sku
 * @property int $qty
 * @property string $type
 * @property int $order_id
 */
class InventoryMovement extends Model
{
    protected $fillable = ['sku', 'qty', 'type', 'order_id'];
}
