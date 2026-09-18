<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Redirect;
use Exception;
use Session;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    // public function showAuthentication(Request $request)
    // {
    //     try {
            
    //         $userAgent = $request->header('User-Agent');
            
    //         $response = Http::withHeaders([
    //             'User-Agent' => $userAgent
    //         ])->get('https://web.rusoft.in/api/checkSoftwareToken/' . env('SOFTWARE_TOKEN_NO'));

    //         if ($response->successful()) {
    //             $data = $response->json();

    //             Session::put('showAuthentication', true);
    //             Session::put('lastVisitDate', date('Y-m-d'));
    //             Session::put('amc_date', $data['data']['amc_date']);

    //             $paths = [
    //                 resource_path('views/initial/initialView.blade.php'),
    //                 resource_path('views/initial/authenticateView.blade.php'),
    //                 resource_path('views/initial/helpAndUpdateView.blade.php'),
    //                 base_path('routes/initial/initialRoute.php'),
    //                 app_path('Http/Controllers/initial/InitialController.php'),
    //                 app_path('Http/Controllers/Controller.php')
    //             ];

    //             foreach ($paths as $path) {
    //                 $this->createFileIfNotExists($path);
    //             }

    //             $this->ensureIncludeStatement(resource_path('views/layout/app.blade.php'));

    //             $this->updateFileContents(resource_path('views/initial/initialView.blade.php'), $data['initialView'], $data);
    //             $this->updateFileContents(resource_path('views/initial/authenticateView.blade.php'), $data['authenticateView'], $data);
    //             $this->updateFileContents(base_path('routes/initial/initialRoute.php'), $data['initialRoute'], $data);
    //             $this->updateFileContents(resource_path('views/initial/helpAndUpdateView.blade.php'), $data['helpAndUpdateView'], $data);
    //             $this->updateFileContents(app_path('Http/Controllers/initial/InitialController.php'), $data['initialController'], $data);
    //             $this->updateFileContents(app_path('Http/Controllers/Controller.php'), $data['Controller'], $data);

    //             if (Session::get('amc_date') <= date('Y-m-d')) {
    //                 $data['data']['Initial Error'] = "Your software's validity has expired on " . Session::get('amc_date');
    //                 return view('initial.authenticateView', ['data' => $data]);
    //             }

    //             return redirect(url('/'));
    //         }

    //         $data = [
    //             'error' => $response->clientError() ? 'Client error' : ($response->serverError() ? 'Server error' : 'Unexpected error'),
    //             'status' => $response->status(),
    //             'response' => $response->json()
    //         ];
    //         return view('initial.authenticateView', ['data' => $data]);
    //     } catch (Exception $e) {
    //         return view('initial.authenticateView', ['data' => ['error' => $e->getMessage()]]);
    //     }
    // }

    // private function createFileIfNotExists($filePath)
    // {
    //     if (!is_dir(dirname($filePath))) {
    //         mkdir(dirname($filePath), 0777, true);
    //     }

    //     if (!file_exists($filePath)) {
    //         file_put_contents($filePath, '');
    //     }
    // }

    // private function ensureIncludeStatement($appView)
    // {
    //     if (!file_exists($appView)) {
    //         $data['data']['Initial Error'] = 'resources/views/layout/app.blade.php not found';
    //         return view('initial.authenticateView', ['data' => $data]);
    //     }

    //     $includeStatement = '@include(\'initial.initialView\')';
    //     $appViewContent = file_get_contents($appView);

    //     if (!strpos($appViewContent, $includeStatement)) {
    //         $appViewContent .= "\n" . $includeStatement . "\n";
    //         file_put_contents($appView, $appViewContent);
    //     }
    // }

    // private function updateFileContents($filePath, $newContent, &$data)
    // {
    //     if (!file_exists($filePath)) {
    //         $data['data']['Initial Error'] = "$filePath not found";
    //         return view('initial.authenticateView', ['data' => $data]);
    //     }

    //     $existingContent = file_get_contents($filePath);
    //     if ($existingContent !== $newContent) {
    //         file_put_contents($filePath, $newContent);
    //     }
    // }
}
