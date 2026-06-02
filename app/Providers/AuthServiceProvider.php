<?php

namespace App\Providers;

use App\Models\Manga;
use App\Models\Pret;
use App\Models\Tome;
use App\Policies\MangaPolicy;
use App\Policies\PretPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Manga::class => MangaPolicy::class,
        Pret::class => PretPolicy::class,
        Tome::class => PretPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
