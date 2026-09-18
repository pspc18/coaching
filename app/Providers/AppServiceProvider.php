<?php

namespace App\Providers;

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
        if (class_exists(\App\Helpers\Helper::class) && !class_exists('App\Helpers\helper', false)) {
            class_alias(\App\Helpers\Helper::class, 'App\Helpers\helper');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Ensure critical environment variables remain available even when config is cached via php artisan optimize
        $imageShow = config('app.image_show_path') ?: env('IMAGE_SHOW_PATH');
        if ($imageShow) {
            putenv('IMAGE_SHOW_PATH=' . $imageShow);
            $_ENV['IMAGE_SHOW_PATH'] = $imageShow;
            $_SERVER['IMAGE_SHOW_PATH'] = $imageShow;
        }

        $imageUpload = config('app.image_upload_path') ?: env('IMAGE_UPLOAD_PATH');
        if ($imageUpload) {
            putenv('IMAGE_UPLOAD_PATH=' . $imageUpload);
            $_ENV['IMAGE_UPLOAD_PATH'] = $imageUpload;
            $_SERVER['IMAGE_UPLOAD_PATH'] = $imageUpload;
        }

        $tokenNo = config('app.software_token_no') ?: env('SOFTWARE_TOKEN_NO');
        if ($tokenNo) {
            putenv('SOFTWARE_TOKEN_NO=' . $tokenNo);
            $_ENV['SOFTWARE_TOKEN_NO'] = $tokenNo;
            $_SERVER['SOFTWARE_TOKEN_NO'] = $tokenNo;
        }
    }
}
