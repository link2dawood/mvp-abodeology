<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rightmove RTDF Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Rightmove Real-Time Data Feed (RTDF) integration.
    | These settings are used when generating RTDF files for property listings.
    |
    */

    'branch_id' => env('RIGHTMOVE_BRANCH_ID', 'ABODE001'),
    'agent_name' => env('RIGHTMOVE_AGENT_NAME', 'Abodeology'),
    'agent_phone' => env('RIGHTMOVE_AGENT_PHONE', ''),
    'agent_email' => env('RIGHTMOVE_AGENT_EMAIL', 'support@abodeology.co.uk'),

    /*
    |--------------------------------------------------------------------------
    | FTP Configuration
    |--------------------------------------------------------------------------
    |
    | FTP settings for uploading RTDF files to Rightmove's server.
    | These credentials will be provided by Rightmove during onboarding.
    |
    */

    'ftp' => [
        'host' => env('RIGHTMOVE_FTP_HOST', 'ftp.rightmove.co.uk'),
        'username' => env('RIGHTMOVE_FTP_USERNAME', ''),
        'password' => env('RIGHTMOVE_FTP_PASSWORD', ''),
        'port' => env('RIGHTMOVE_FTP_PORT', 21),
        'passive' => env('RIGHTMOVE_FTP_PASSIVE', true),
        'remote_path' => env('RIGHTMOVE_FTP_REMOTE_PATH', '/feeds/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RTDF File Settings
    |--------------------------------------------------------------------------
    |
    | Settings for RTDF file generation and storage.
    |
    */

    'file' => [
        'directory' => env('RIGHTMOVE_FILE_DIRECTORY', 'feeds'),
        'prefix' => env('RIGHTMOVE_FILE_PREFIX', 'property_'),
        'extension' => env('RIGHTMOVE_FILE_EXTENSION', '.txt'),
    ],
];

