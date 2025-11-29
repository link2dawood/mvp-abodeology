<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for automated backup strategies.
    |
    */

    'enabled' => env('BACKUP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Backup Storage
    |--------------------------------------------------------------------------
    |
    | Where backups should be stored. Options: local, s3, ftp
    |
    */

    'storage' => env('BACKUP_STORAGE', 'local'),

    'storage_path' => env('BACKUP_STORAGE_PATH', storage_path('app/backups')),

    /*
    |--------------------------------------------------------------------------
    | Database Backup
    |--------------------------------------------------------------------------
    |
    | Configuration for database backups.
    |
    */

    'database' => [
        'enabled' => env('BACKUP_DATABASE_ENABLED', true),
        'schedule' => env('BACKUP_DATABASE_SCHEDULE', 'daily'), // daily, weekly, monthly
        'retention_days' => env('BACKUP_DATABASE_RETENTION', 30),
        'compress' => env('BACKUP_DATABASE_COMPRESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Backup
    |--------------------------------------------------------------------------
    |
    | Configuration for file backups (uploads, storage, etc.).
    |
    */

    'files' => [
        'enabled' => env('BACKUP_FILES_ENABLED', true),
        'schedule' => env('BACKUP_FILES_SCHEDULE', 'weekly'),
        'retention_days' => env('BACKUP_FILES_RETENTION', 90),
        'directories' => [
            'storage/app/public',
            'storage/app/private',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | S3 Backup Configuration
    |--------------------------------------------------------------------------
    |
    | If using S3 for backup storage.
    |
    */

    's3' => [
        'bucket' => env('BACKUP_S3_BUCKET'),
        'region' => env('BACKUP_S3_REGION', 'us-east-1'),
        'path' => env('BACKUP_S3_PATH', 'backups'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Configure backup completion notifications.
    |
    */

    'notifications' => [
        'enabled' => env('BACKUP_NOTIFICATIONS_ENABLED', true),
        'email' => env('BACKUP_NOTIFICATION_EMAIL', 'admin@abodeology.co.uk'),
        'on_success' => env('BACKUP_NOTIFY_ON_SUCCESS', false),
        'on_failure' => env('BACKUP_NOTIFY_ON_FAILURE', true),
    ],
];

