<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Keap (Infusionsoft) Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Keap CRM automation triggers.
    | These settings control the integration with Keap API.
    |
    */

    'enabled' => env('KEAP_ENABLED', false),
    'api_url' => env('KEAP_API_URL', 'https://api.infusionsoft.com/crm/rest/v1'),
    'api_key' => env('KEAP_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Keap Tag IDs
    |--------------------------------------------------------------------------
    |
    | Map event types to Keap tag IDs for contact tagging.
    | These IDs are configured in your Keap account.
    |
    */

    'tags' => [
        'offer_submitted' => env('KEAP_TAG_OFFER_SUBMITTED', null),
        'offer_accepted' => env('KEAP_TAG_OFFER_ACCEPTED', null),
        'aml_uploaded' => env('KEAP_TAG_AML_UPLOADED', null),
        'pva_feedback_submitted' => env('KEAP_TAG_PVA_FEEDBACK_SUBMITTED', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for retrying failed Keap API calls.
    |
    */

    'retry' => [
        'enabled' => env('KEAP_RETRY_ENABLED', true),
        'max_attempts' => env('KEAP_RETRY_MAX_ATTEMPTS', 3),
        'delay_seconds' => env('KEAP_RETRY_DELAY_SECONDS', 60),
    ],
];

