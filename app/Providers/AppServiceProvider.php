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

        // Defensive polyfill for mb_strimwidth if mbstring extension is absent or incomplete
        if (!function_exists('mb_strimwidth')) {
            function mb_strimwidth($string, $start, $width, $trimmarker = '', $encoding = null) {
                if (function_exists('mb_substr') && function_exists('mb_strwidth')) {
                    $encoding = $encoding ?: mb_internal_encoding();
                    if (mb_strwidth($string, $encoding) <= $width) {
                        return $string;
                    }
                    $markerWidth = mb_strwidth($trimmarker, $encoding);
                    $width -= $markerWidth;
                    $trimmed = mb_substr($string, $start, $width, $encoding);
                    while (mb_strwidth($trimmed, $encoding) > $width) {
                        $trimmed = mb_substr($trimmed, 0, -1, $encoding);
                    }
                    return $trimmed . $trimmarker;
                }

                if (strlen($string) <= $width) {
                    return $string;
                }
                $markerLen = strlen($trimmarker);
                $width = max(0, $width - $markerLen);
                return substr($string, $start, $width) . $trimmarker;
            }
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
    }
}
