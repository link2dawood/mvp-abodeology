<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Uptime Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for uptime monitoring services and health checks.
    |
    */

    'enabled' => env('MONITORING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Health Check Endpoints
    |--------------------------------------------------------------------------
    |
    | Endpoints that can be used for health checks and uptime monitoring.
    |
    */

    'health_check_endpoints' => [
        '/up' => 'Laravel built-in health check',
        '/api/health' => 'API health check endpoint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Services
    |--------------------------------------------------------------------------
    |
    | Configure external monitoring services (e.g., UptimeRobot, Pingdom, etc.)
    | These should be configured to check the health endpoints above.
    |
    */

    'services' => [
        'uptimerobot' => [
            'enabled' => env('UPTIMEROBOT_ENABLED', false),
            'api_key' => env('UPTIMEROBOT_API_KEY'),
        ],
        'pingdom' => [
            'enabled' => env('PINGDOM_ENABLED', false),
            'api_key' => env('PINGDOM_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Configuration
    |--------------------------------------------------------------------------
    |
    | Configure alert recipients for downtime notifications.
    |
    */

    'alerts' => [
        'email' => env('MONITORING_ALERT_EMAIL', 'admin@abodeology.co.uk'),
        'slack' => env('MONITORING_SLACK_WEBHOOK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Check Intervals
    |--------------------------------------------------------------------------
    |
    | How often monitoring services should check endpoints (in minutes).
    |
    */

    'check_interval' => env('MONITORING_CHECK_INTERVAL', 5), // minutes
];

