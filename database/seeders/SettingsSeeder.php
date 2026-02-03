<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Branding Settings
            [
                'key' => 'logo_url',
                'value' => asset('media/abodeology-logo.png'),
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Company logo URL used in email templates',
                'is_public' => false,
            ],
            [
                'key' => 'company_name',
                'value' => 'Abodeology',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Company name',
                'is_public' => true,
            ],
            [
                'key' => 'company_email',
                'value' => 'support@abodeology.co.uk',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Company support email address',
                'is_public' => true,
            ],
            [
                'key' => 'company_phone',
                'value' => '',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Company phone number',
                'is_public' => true,
            ],
            [
                'key' => 'company_address',
                'value' => '',
                'type' => 'text',
                'group' => 'branding',
                'description' => 'Company physical address',
                'is_public' => true,
            ],
            
            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Abodeology',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default sender name for emails',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@abodeology.co.uk',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default sender email address',
                'is_public' => false,
            ],
            [
                'key' => 'email_reply_to',
                'value' => 'support@abodeology.co.uk',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Reply-to email address',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Settings::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}


