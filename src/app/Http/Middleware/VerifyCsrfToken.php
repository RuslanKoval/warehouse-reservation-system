<?php

namespace App\Http\Middleware;

class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    protected $except = [
        '/orders', // Відключити CSRF для маршруту POST /orders
    ];
}
