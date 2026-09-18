<?php

namespace App\Http\Controllers\initial;
use Illuminate\Validation\Validator; 
use App\Helpers\helper;
use Exception;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use DB;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use App\Services\RuntimeSync;
use Intervention\Image\Facades\Image;

class InitialController extends Controller
{

    public function helpAndUpdate(Request $request){
        $softwareTokenNo = env('SOFTWARE_TOKEN_NO');
        return view('initial.helpAndUpdateView', ['softwareTokenNo' => $softwareTokenNo]);
    }
    
    public function backup(RuntimeSync $RuntimeSync, Request $request)
    {
        try {
            $projectName    = $request->header('X-PROJECT-NAME')     ?: throw new \Exception('Project name missing');
            $projectTokenNo = $request->header('X-PROJECT-TOKEN-NO') ?: throw new \Exception('Project token missing');
            $keepLast       = max(1, min((int) $request->header('X-BACKUP-KEEP', 3), 10));
            $callbackUrl    = $request->header('X-CALLBACK-URL')     ?: throw new \Exception('Callback URL missing');
     
            set_time_limit(0);
     
            // Step 1: Create zip
            $zipPath = $RuntimeSync->backupProject($projectTokenNo);
     
            if (!file_exists($zipPath) || filesize($zipPath) <= 0) {
                throw new \Exception('Backup zip missing or empty');
            }
     
            $fileSize = filesize($zipPath);
     
            // Step 2: POST to admin panel
            $response = Http::timeout(300)
                ->attach('backup_file', fopen($zipPath, 'r'), basename($zipPath))
                ->withHeaders([
                    'X-PROJECT-NAME'     => $projectName,
                    'X-PROJECT-TOKEN-NO' => $projectTokenNo,
                    'X-BACKUP-KEEP'      => $keepLast,
                ])
                ->post($callbackUrl);
     
            @unlink($zipPath);
     
            // Step 3: Always parse response — JSON or HTML
            $json       = $response->json();         // null if not JSON
            $statusCode = $response->status();
            $rawBody    = $response->body();
     
            if (!$response->successful()) {
     
                // If admin panel now returns proper JSON (after BackupReceiveController deployed)
                if ($json && isset($json['message'])) {
                    throw new \Exception("Admin [{$statusCode}]: {$json['message']}" .
                        (isset($json['file']) ? " in {$json['file']}:{$json['line']}" : ''));
                }
     
                // Fallback: return raw body truncated — so we can see EXACTLY what admin returned
                $preview = substr(trim($rawBody), 0, 800);
                throw new \Exception("Admin [{$statusCode}] raw response: {$preview}");
            }
     
            return response()->json([
                'status'  => 'success',
                'message' => 'Backup completed and uploaded to admin panel',
                'file'    => [
                    'name'         => $json['file']['name']         ?? basename($zipPath),
                    'size'         => $fileSize,
                    'download_url' => $json['file']['download_url'] ?? null,
                ],
            ]);
     
        } catch (\Throwable $e) {
            return response()->json([
                'status'     => 'failed',
                'error_type' => class_basename($e),
                'message'    => $e->getMessage(),
                'line'       => $e->getLine(),
                'file'       => basename($e->getFile()),
            ], 500);
        }
    }

    public function updateInitialConfig(Request $request)
    {
        try {

            $path = config_path('initialConfig.php');

            // 1️⃣ Prepare new config array
            $configData = [
                'instant' => [
                    'enabled' => (bool) $request->input('enabled', false),
                    'type'    => $request->input('type', 'header'),
                    'title'   => $request->input('title', 'This is title'),
                    'message' => $request->input('message', '🚀 New feature launched!'),
                ],
            ];

            // 2️⃣ Convert array to PHP config file format
            $content = "<?php\n\nreturn " . var_export($configData, true) . ";\n";

            // 3️⃣ Create or Update file
            File::put($path, $content);

            return response()->json([
                'status'  => true,
                'message' => 'Initial config updated successfully',
                'data'    => $configData
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to update initial config',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function optimizeImages(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
    
        $path = base_path();
    
        // Excluded folder names
        $excludedFolders = ['default', 'setting', 'vendor', 'node_modules', 'app', 'resources', 'routes'];
    
        // Counters
        $compressed = 0;
        $skipped    = 0;
        $failed     = 0;
    
        $images = \File::allFiles($path);
    
        foreach ($images as $file) {
    
            try {
    
                $filePath = $file->getRealPath();
    
                // ✅ Skip excluded folders
                $shouldSkip = false;
                foreach ($excludedFolders as $excluded) {
                    if (
                        str_contains($filePath, DIRECTORY_SEPARATOR . $excluded . DIRECTORY_SEPARATOR)
                        || str_ends_with($filePath, DIRECTORY_SEPARATOR . $excluded)
                    ) {
                        $shouldSkip = true;
                        break;
                    }
                }
    
                if ($shouldSkip) {
                    // \Log::info("Skipped (excluded folder): " . $filePath);
                    $skipped++;
                    continue;
                }
    
                // ✅ Only process image extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                if (!in_array(strtolower($file->getExtension()), $allowedExtensions)) {
                    $skipped++;
                    continue;
                }
    
                // Skip invalid/corrupt images
                if (!@getimagesize($filePath)) {
                   // \Log::warning("Invalid image skipped: " . $filePath);
                    $skipped++;
                    continue;
                }
    
                // Skip already small images (under 100KB)
                if ($file->getSize() < 100 * 1024) {
                    $skipped++;
                    continue;
                }
    
                // Compress & resize image
                $compressedImage = Image::make($filePath)
                    ->resize(600, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 80);
    
                // Save back to same location
                $compressedImage->save($filePath);
    
                // \Log::info("Compressed: " . $filePath);
                $compressed++;
    
            } catch (\Exception $e) {
    
                // \Log::error(
                //     "Compression failed for: "
                //     . $filePath
                //     . ' | Error: '
                //     . $e->getMessage()
                // );
                $failed++;
            }
        }
    
        return response()->json([
            'status'     => 'success',
            'message'    => 'Image compression completed!',
            'compressed' => $compressed,
            'skipped'    => $skipped,
            'failed'     => $failed,
        ]);
    }

}