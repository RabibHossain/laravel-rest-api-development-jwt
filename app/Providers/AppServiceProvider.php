<?php

namespace App\Providers;

use App\Channel;
use App\Http\View\Composers\ChannelsComposer;
use Facade\FlareClient\View;
use Illuminate\Support\ServiceProvider;

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
        // Option 1 - Every Single View 
        // view()->share('channels', Channel::orderby('name')->get());

        // Option 2 - Granular views with wildcards
        // view()->composer(['post.*', 'channel.index'], function ($view) {
        //     $view->with('channels', Channel::orderBy('name', 'desc')->get());
        // });

            // Option 3 - Dedicated Class 
            // view()->composer(['post.*', 'channel.index'], ChannelsComposer::class);
            view()->composer(['partials.channels.*', 'channel.index'], ChannelsComposer::class);
    }
}
