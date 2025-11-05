<?php

namespace App\Providers;

use App\Models\Manga;
use App\Policies\MangaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Manga::class => MangaPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
