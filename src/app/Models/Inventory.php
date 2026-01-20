<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 *
 * @property string $sku
 * @property int $available_qty
 */
class Inventory extends Model
{
    protected $fillable = ['sku', 'available_qty'];
}
