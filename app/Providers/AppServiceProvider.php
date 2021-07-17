<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\Genre;
use App\Models\CastMember;
use App\Observers\CategoryObserver;
use App\Observers\GenreObserver;
use App\Observers\CastMemberObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Category::observe(CategoryObserver::class);
        Genre::observe(GenreObserver::class);
        CastMember::observe(CastMemberObserver::class);
    }
}
