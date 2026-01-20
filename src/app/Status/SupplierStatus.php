<?php

namespace App\Status;

class SupplierStatus
{
    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';
    const STATUS_DELAYED = 'delayed';

    public static function getRandomStatus()
    {
        $rand = rand(1, 3);
        switch ($rand) {
            case 1: return self::STATUS_OK;
            case 2: return self::STATUS_OK;
            case 3: return self::STATUS_OK;
        }
    }
}
