<?php
/**
 * S3 Configuration Checker
 * 
 * Run this script to verify your S3 configuration:
 * php check-s3-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "S3 Configuration Checker\n";
echo "========================================\n\n";

// Check .env file exists
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ ERROR: .env file not found!\n";
    exit(1);
}

// Check default disk
$defaultDisk = config('filesystems.default');
echo "1. Default Filesystem Disk: " . $defaultDisk . "\n";
if ($defaultDisk === 's3') {
    echo "   ✅ S3 is set as default\n\n";
} else {
    echo "   ⚠️  S3 is NOT set as default (current: {$defaultDisk})\n";
    echo "   Set FILESYSTEM_DISK=s3 in .env to use S3\n\n";
}

// Check S3 configuration
echo "2. S3 Configuration:\n";
$s3Config = config('filesystems.disks.s3');

$checks = [
    'Region' => $s3Config['region'] ?? null,
    'Bucket' => $s3Config['bucket'] ?? null,
    'Access Key ID' => $s3Config['key'] ? 'Set ✓' : 'Missing ✗',
    'Secret Access Key' => $s3Config['secret'] ? 'Set ✓' : 'Missing ✗',
    'URL' => $s3Config['url'] ?? null,
];

foreach ($checks as $key => $value) {
    $status = $value ? '✅' : '❌';
    echo "   {$status} {$key}: " . ($value ?: 'Not set') . "\n";
}

echo "\n";

// Test S3 connection if configured
if ($defaultDisk === 's3' && $s3Config['key'] && $s3Config['secret']) {
    echo "3. Testing S3 Connection:\n";
    try {
        $testFile = 'config-test-' . time() . '.txt';
        Storage::disk('s3')->put($testFile, 'Configuration test file');
        
        if (Storage::disk('s3')->exists($testFile)) {
            echo "   ✅ Upload: SUCCESS\n";
            Storage::disk('s3')->delete($testFile);
            echo "   ✅ Delete: SUCCESS\n";
            echo "   ✅ S3 Connection: WORKING\n";
        } else {
            echo "   ❌ Upload: FAILED (file not found after upload)\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Connection: FAILED\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "\n   Common issues:\n";
        echo "   - Check AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY in .env\n";
        echo "   - Verify IAM user has S3 permissions\n";
        echo "   - Check bucket name and region are correct\n";
    }
} else {
    echo "3. S3 Connection Test: SKIPPED\n";
    echo "   (S3 not configured or credentials missing)\n";
}

echo "\n========================================\n";
echo "Check complete!\n";
echo "========================================\n";

