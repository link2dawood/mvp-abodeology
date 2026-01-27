<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KeapService;
use App\Models\User;
use App\Models\KeapEventLog;
use Illuminate\Support\Facades\Log;

class TestKeapIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keap:test {event?} {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Keap integration by triggering events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keapService = new KeapService();
        
        // Check if Keap is enabled
        if (!config('keap.enabled')) {
            $this->error('Keap integration is DISABLED. Set KEAP_ENABLED=true in .env');
            return 1;
        }
        
        if (empty(config('keap.api_key'))) {
            $this->error('Keap API key is not configured. Set KEAP_API_KEY in .env');
            return 1;
        }
        
        $this->info('✓ Keap is enabled');
        $this->info('✓ API Key is configured');
        $this->info('✓ API URL: ' . config('keap.api_url'));
        $this->newLine();
        
        $eventType = $this->argument('event');
        
        if (!$eventType) {
            return $this->showMenu($keapService);
        }
        
        return $this->testEvent($keapService, $eventType);
    }
    
    protected function showMenu($keapService)
    {
        $this->info('Available Keap Events to Test:');
        $this->newLine();
        
        $events = [
            '1' => ['name' => 'seller_onboarded', 'description' => 'Test seller registration'],
            '2' => ['name' => 'buyer_registered', 'description' => 'Test buyer registration'],
            '3' => ['name' => 'offer_submitted', 'description' => 'Test offer submission (requires offer ID)'],
            '4' => ['name' => 'offer_accepted', 'description' => 'Test offer acceptance (requires offer ID)'],
            '5' => ['name' => 'aml_uploaded', 'description' => 'Test AML upload (requires AML check ID)'],
            '6' => ['name' => 'pva_feedback_submitted', 'description' => 'Test PVA feedback (requires feedback ID)'],
            '7' => ['name' => 'show_logs', 'description' => 'Show recent Keap event logs'],
        ];
        
        foreach ($events as $key => $event) {
            $this->line("  {$key}. {$event['name']} - {$event['description']}");
        }
        
        $this->newLine();
        $choice = $this->choice('Select an event to test', array_keys($events));
        
        if ($choice === '7') {
            return $this->showLogs();
        }
        
        $selectedEvent = $events[$choice]['name'];
        
        // For events that need database records, show instructions
        if (in_array($selectedEvent, ['offer_submitted', 'offer_accepted', 'aml_uploaded', 'pva_feedback_submitted'])) {
            $this->warn("Note: {$selectedEvent} requires existing database records.");
            $this->info("You can test this by performing the actual action in your application.");
            $this->info("Or use: php artisan keap:test {$selectedEvent} --user-id=<id>");
            return 0;
        }
        
        return $this->testEvent($keapService, $selectedEvent);
    }
    
    protected function testEvent($keapService, $eventType)
    {
        $userId = $this->option('user-id');
        
        switch ($eventType) {
            case 'seller_onboarded':
            case 'buyer_registered':
                if ($userId) {
                    $user = User::find($userId);
                    if (!$user) {
                        $this->error("User with ID {$userId} not found");
                        return 1;
                    }
                } else {
                    // Create a test user
                    $user = User::create([
                        'name' => 'Test ' . ucfirst(str_replace('_', ' ', $eventType)),
                        'email' => 'test-keap-' . time() . '@example.com',
                        'password' => bcrypt('password'),
                        'role' => strpos($eventType, 'seller') !== false ? 'seller' : 'buyer',
                        'phone' => '1234567890',
                    ]);
                    $this->info("Created test user: {$user->email} (ID: {$user->id})");
                }
                
                if ($eventType === 'seller_onboarded') {
                    $result = $keapService->triggerSellerOnboarded($user);
                } else {
                    $result = $keapService->triggerBuyerRegistered($user);
                }
                break;
                
            default:
                $this->error("Event type '{$eventType}' requires existing records. Perform the action in your application instead.");
                return 1;
        }
        
        $this->newLine();
        if ($result) {
            $this->info('✓ Event triggered successfully!');
        } else {
            $this->error('✗ Event failed to trigger');
        }
        
        $this->showLastLog();
        
        return $result ? 0 : 1;
    }
    
    protected function showLogs()
    {
        $this->info('Recent Keap Event Logs:');
        $this->newLine();
        
        $logs = KeapEventLog::orderBy('created_at', 'desc')->limit(10)->get();
        
        if ($logs->isEmpty()) {
            $this->warn('No logs found. Events haven\'t been triggered yet.');
            return 0;
        }
        
        $headers = ['ID', 'Event Type', 'Status', 'Created At', 'Error'];
        $rows = [];
        
        foreach ($logs as $log) {
            $rows[] = [
                $log->id,
                $log->event_type,
                $this->formatStatus($log->status),
                $log->created_at->format('Y-m-d H:i:s'),
                $log->error_message ?? '-',
            ];
        }
        
        $this->table($headers, $rows);
        
        $logId = $this->ask('Enter log ID to view details (or press Enter to skip)', null);
        
        if ($logId) {
            $log = KeapEventLog::find($logId);
            if ($log) {
                $this->showLogDetails($log);
            } else {
                $this->error("Log with ID {$logId} not found");
            }
        }
        
        return 0;
    }
    
    protected function showLastLog()
    {
        $log = KeapEventLog::latest()->first();
        if ($log) {
            $this->newLine();
            $this->info('Event Log Details:');
            $this->line("  ID: {$log->id}");
            $this->line("  Event Type: {$log->event_type}");
            $this->line("  Status: " . $this->formatStatus($log->status));
            $this->line("  Created At: {$log->created_at->format('Y-m-d H:i:s')}");
            
            if ($log->error_message) {
                $this->error("  Error: {$log->error_message}");
            }
            
            if ($log->response) {
                $this->info("  Response: " . json_encode($log->response, JSON_PRETTY_PRINT));
            }
        }
    }
    
    protected function showLogDetails($log)
    {
        $this->newLine();
        $this->info("Log #{$log->id} Details:");
        $this->line("  Event Type: {$log->event_type}");
        $this->line("  Status: " . $this->formatStatus($log->status));
        $this->line("  Created At: {$log->created_at->format('Y-m-d H:i:s')}");
        $this->line("  Retry Count: {$log->retry_count}");
        
        if ($log->payload) {
            $this->newLine();
            $this->info('  Payload:');
            $this->line(json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        
        if ($log->response) {
            $this->newLine();
            $this->info('  Response:');
            $this->line(json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        
        if ($log->error_message) {
            $this->newLine();
            $this->error('  Error Message:');
            $this->line($log->error_message);
        }
    }
    
    protected function formatStatus($status)
    {
        $colors = [
            'success' => 'green',
            'failed' => 'red',
            'pending' => 'yellow',
            'skipped' => 'cyan',
        ];
        
        $color = $colors[$status] ?? 'white';
        return "<fg={$color}>{$status}</>";
    }
}








