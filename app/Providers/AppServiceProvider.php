<?php

namespace App\Providers;

use App\Listeners\LogAuthEvents;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::subscribe(LogAuthEvents::class);
    }
}
