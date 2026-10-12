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

        // Proactively require PaymentController, PhonePeService and OnlinePayment to guarantee loading on Linux servers
        $paymentController = app_path('Http/Controllers/student_login/PaymentController.php');
        if (file_exists($paymentController)) {
            require_once $paymentController;
        }
        $phonePeService = app_path('Services/PhonePeService.php');
        if (file_exists($phonePeService)) {
            require_once $phonePeService;
        }
        $onlinePayment = app_path('Models/OnlinePayment.php');
        if (file_exists($onlinePayment)) {
            require_once $onlinePayment;
        }

        // Bi-directional class aliases and container bindings for App vs app casing
        if (class_exists(\App\Http\Controllers\student_login\PaymentController::class, false)) {
            if (!class_exists('app\Http\Controllers\student_login\PaymentController', false)) {
                class_alias(\App\Http\Controllers\student_login\PaymentController::class, 'app\Http\Controllers\student_login\PaymentController');
            }
            $this->app->bind('app\Http\Controllers\student_login\PaymentController', function ($app) {
                return $app->make(\App\Http\Controllers\student_login\PaymentController::class);
            });
        }

        if (class_exists(\App\Services\PhonePeService::class, false)) {
            if (!class_exists('app\Services\PhonePeService', false)) {
                class_alias(\App\Services\PhonePeService::class, 'app\Services\PhonePeService');
            }
            $this->app->bind('app\Services\PhonePeService', function ($app) {
                return $app->make(\App\Services\PhonePeService::class);
            });
        }

        if (class_exists(\App\Models\OnlinePayment::class, false)) {
            if (!class_exists('app\Models\OnlinePayment', false)) {
                class_alias(\App\Models\OnlinePayment::class, 'app\Models\OnlinePayment');
            }
            $this->app->bind('app\Models\OnlinePayment', function ($app) {
                return $app->make(\App\Models\OnlinePayment::class);
            });
        }

        // Load defensive polyfills for multibyte functions if mbstring extension is absent or incomplete
        $polyfill = base_path('bootstrap/polyfill.php');
        if (file_exists($polyfill)) {
            require_once $polyfill;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Pagination\Paginator::useBootstrap();

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

        // Auto-sync legacy school name and logo in settings table if not yet updated on server
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $legacySetting = \Illuminate\Support\Facades\DB::table('settings')
                    ->where('name', 'like', '%Arise Education Point%')
                    ->first();
                if ($legacySetting) {
                    \Illuminate\Support\Facades\DB::table('settings')->where('id', $legacySetting->id)->update([
                        'name' => 'Rusoft Education Point',
                        'left_logo' => 'rusoft_logo.png',
                        'updated_at' => now(),
                    ]);
                    \Illuminate\Support\Facades\Cache::forget('app_setting_' . ($legacySetting->branch_id ?: 1));
                    \Illuminate\Support\Facades\Cache::forget('app_setting_1');
                }
            }
        } catch (\Throwable $e) {}
    }
}
