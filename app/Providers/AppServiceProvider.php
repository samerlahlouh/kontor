<?php

namespace Educators\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $website_lang = app()->getLocale();
        $lang_file_ext = $website_lang=='ar'? '-rtl': '';
        View::share('lang_file_ext', $lang_file_ext);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
