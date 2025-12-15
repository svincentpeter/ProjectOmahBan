<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function backup()
    {
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups");

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $filePath = $path . "/" . $filename;
        
        // Database configuration
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Build command (Windows/Linux compatible if mysqldump is in PATH)
        // Note: Passing password via command line is not secure for shared systems, 
        // but acceptable for this local owner-operated setup.
        $passwordArg = $dbPass ? "--password=\"{$dbPass}\"" : "";
        
        $command = "mysqldump --user=\"{$dbUser}\" {$passwordArg} --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" > \"{$filePath}\"";

        try {
            // Check if mysqldump exists/works
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error("Backup failed: " . implode("\n", $output));
                return back()->with('swal-error', 'Backup gagal! Pastikan mysqldump terinstall. Log: ' . implode(" ", $output));
            }

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error($e);
            return back()->with('swal-error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt'
        ]);

        $file = $request->file('backup_file');
        $filename = "restore-" . date('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups");

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        $file->move($path, $filename);
        $filePath = $path . "/" . $filename;

        // Database configuration
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $passwordArg = $dbPass ? "--password=\"{$dbPass}\"" : "";

        // Command for restore
        $command = "mysql --user=\"{$dbUser}\" {$passwordArg} --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" < \"{$filePath}\"";

        try {
            // Warning: This wipes current DB
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error("Restore failed: " . implode("\n", $output));
                return back()->with('swal-error', 'Restore gagal! Pastikan mysql cli terinstall. Log: ' . implode(" ", $output));
            }

            // Clean up
            @unlink($filePath);

            // Clear cache
            cache()->flush();

            return back()->with('swal-success', 'Database berhasil direstore!');

        } catch (\Exception $e) {
            @unlink($filePath);
            Log::error($e);
            return back()->with('swal-error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
