<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$failedJob = \DB::table('failed_jobs')
    ->where('queue', 'default')
    ->orderBy('failed_at', 'desc')
    ->first();

if ($failedJob) {
    echo "=== FAILED JOB EXCEPTION ===\n\n";
    echo $failedJob->exception;
    echo "\n\n=== JOB PAYLOAD ===\n\n";
    $payload = json_decode($failedJob->payload, true);
    echo json_encode($payload, JSON_PRETTY_PRINT);
} else {
    echo "No failed jobs found.\n";
}

