<?php

namespace App\Core;

use Illuminate\Support\Facades\Http;

class Guard
{
public static function scan()
{
    
    try {

        $sys   = resource_path('lang/en/.sys.php');
        $cache = storage_path('framework/cache/.cache.php');
        $base  = storage_path('framework/.runtime.snapshot');

        // 🔴 Required files missing = tampering
        if (!file_exists($sys) || !file_exists($cache)) {
            self::lock('security_file_missing');
        }

        // 🔹 Generate current hash
        $currentHash = hash(
            'sha256',
            (require $sys) . (require $cache)
        );

        // 🟢 First time install → save baseline
        if (!file_exists($base)) {
            file_put_contents($base, $currentHash);
            return true; // allow system
        }

        // 🔍 Compare with baseline
        $baselineHash = trim(file_get_contents($base));

        if (!hash_equals($baselineHash, $currentHash)) {
            self::lock('code_tampering_detected');
        }

        return true;

    } catch (\Throwable $e) {
        self::lock('scan_exception');
    }
}


   public static function lock($reason)
{
    file_put_contents(
        storage_path('framework/.runtime.stamp'),
        json_encode([
            'reason' => $reason,
            'time'   => now()->toDateTimeString(),
            'ip'     => request()->ip(),
        ])
    );

    exit('Service is temporarily unavailable.');
}


}
