<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TranslationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationRepository::class, function ($app) {
            return new TranslationRepository();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
