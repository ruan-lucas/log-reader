<?php

namespace App\Providers;

use App\Repositories\ConsumerRepositoryInterface;
use App\Repositories\Eloquent\ConsumerRepository;
use App\Repositories\Eloquent\LogFileProcessRepository;
use App\Repositories\Eloquent\RequestRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\LogFileProcessRepositoryInterface;
use App\Repositories\RequestRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(ConsumerRepositoryInterface::class, ConsumerRepository::class);
        $this->app->bind(RequestRepositoryInterface::class, RequestRepository::class);
        $this->app->bind(LogFileProcessRepositoryInterface::class, LogFileProcessRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
