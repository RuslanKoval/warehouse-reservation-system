<?php

namespace App\Providers;

use App\Status\SupplierStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::fake([
            'https://supplier/reserve' => Http::response([
                'accepted' => true,
                'ref' => 'SUP-' . now()->timestamp,
            ]),

            'https://supplier/status/*' => Http::sequence()
                ->push(['status' => SupplierStatus::STATUS_OK])
                ->push(['status' => SupplierStatus::STATUS_FAIL])
                ->push(['status' => SupplierStatus::STATUS_DELAYED])
                ->push(['status' => SupplierStatus::STATUS_OK])
                ->push(['status' => SupplierStatus::STATUS_FAIL])
                ->push(['status' => SupplierStatus::STATUS_DELAYED])
                ->push(['status' => SupplierStatus::STATUS_OK])
                ->push(['status' => SupplierStatus::STATUS_FAIL])
                ->push(['status' => SupplierStatus::STATUS_DELAYED])
                ->push(['status' => SupplierStatus::STATUS_OK])
                ->push(['status' => SupplierStatus::STATUS_FAIL])
                ->push(['status' => SupplierStatus::STATUS_DELAYED])
                ->whenEmpty(Http::response([
                    'status' => SupplierStatus::STATUS_DELAYED
                ]))
        ]);
    }
}
