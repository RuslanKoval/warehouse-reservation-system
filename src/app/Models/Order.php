<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @property string $sku
 * @property int $qty
 * @property string $status
 * @property string|null $supplier_ref
 * @property int $supplier_checks
 */
class Order extends Model
{
    const STATUS_FAILED = 'failed';
    const STATUS_RESERVED = 'reserved';
    const STATUS_AWAITING = 'awaiting_restock';

    protected $fillable = [
        'sku', 'qty', 'status', 'supplier_ref', 'supplier_checks'
    ];
}
