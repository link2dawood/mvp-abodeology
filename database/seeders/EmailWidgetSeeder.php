<?php

namespace Database\Seeders;

use App\Models\EmailWidget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgets = [
            // HEADER WIDGETS (From existing emails)
            [
                'name' => 'Abodeology Header',
                'key' => 'abodeology-header',
                'category' => 'Header',
                'description' => 'Standard Abodeology email header with logo (black background)',
                'html' => '<div style="background: #0F0F0F; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 8px;">
    <img src="{{logo_url}}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">
    <h1 style="color: #2CB8B4; margin: 0; display: none;">Abodeology®</h1>
</div>',
                'preview' => 'Black header with Abodeology logo',
                'is_active' => true,
                'sort_order' => 10,
            ],

            // FOOTER WIDGETS (From existing emails)
            [
                'name' => 'Abodeology Footer',
                'key' => 'abodeology-footer',
                'category' => 'Footer',
                'description' => 'Standard Abodeology email footer with copyright',
                'html' => '<hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
<p style="font-size: 12px; color: #666; text-align: center;">
    © {{year}} Abodeology®. All rights reserved.
</p>',
                'preview' => 'Simple copyright footer',
                'is_active' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'Footer with Support Link',
                'key' => 'footer-support',
                'category' => 'Footer',
                'description' => 'Footer with support email link',
                'html' => '<p>If you have any questions or need assistance, please don\'t hesitate to contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>
<p>Best regards,<br>
<strong>The Abodeology Team</strong></p>
<hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
<p style="font-size: 12px; color: #666; text-align: center;">
    © {{date(\'Y\')}} Abodeology®. All rights reserved.
</p>',
                'preview' => 'Footer with support contact',
                'is_active' => true,
                'sort_order' => 61,
            ],

            // LOGIN CREDENTIALS WIDGETS (From valuation-login-credentials.blade.php)
            [
                'name' => 'Login Credentials Box',
                'key' => 'login-credentials',
                'category' => 'System',
                'description' => 'Styled box displaying email and password credentials',
                'html' => '<h3 style="color: #2CB8B4;">Your Login Credentials</h3>
<div style="background: #F4F4F4; border: 2px solid #2CB8B4; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <p style="margin: 0 0 10px 0;"><strong>Email:</strong> <span style="font-family: \'Courier New\', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{user.email}}</span></p>
    <p style="margin: 0;"><strong>Password:</strong> <span style="font-family: \'Courier New\', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{password}}</span></p>
</div>',
                'preview' => 'Login credentials display box',
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'Security Note',
                'key' => 'security-note',
                'category' => 'System',
                'description' => 'Security warning about changing password',
                'html' => '<div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0;"><strong>⚠️ Security Note:</strong> Please change your password after logging in for the first time. You can do this from your account profile settings.</p>
</div>',
                'preview' => 'Security warning box',
                'is_active' => true,
                'sort_order' => 51,
            ],

            // CALL TO ACTION BUTTONS (From existing emails)
            [
                'name' => 'Primary CTA Button',
                'key' => 'cta-primary',
                'category' => 'Button',
                'description' => 'Standard Abodeology teal primary button',
                'html' => '<div style="text-align: center; margin: 30px 0;">
    <a href="{{url}}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">{{text}}</a>
</div>',
                'preview' => 'Teal primary button',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Large CTA Button',
                'key' => 'cta-large',
                'category' => 'Button',
                'description' => 'Large primary button for important actions',
                'html' => '<div style="text-align: center; margin: 30px 0;">
    <a href="{{url}}" style="background: #2CB8B4; color: #FFFFFF; padding: 15px 40px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; font-size: 16px;">{{text}}</a>
</div>',
                'preview' => 'Large teal button',
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'name' => 'Success Button',
                'key' => 'cta-success',
                'category' => 'Button',
                'description' => 'Green success/confirm button',
                'html' => '<div style="text-align: center; margin: 30px 0;">
    <a href="{{url}}" style="background: #28a745; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">{{text}}</a>
</div>',
                'preview' => 'Green success button',
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'name' => 'Warning Button',
                'key' => 'cta-warning',
                'category' => 'Button',
                'description' => 'Yellow warning button',
                'html' => '<div style="text-align: center; margin: 30px 0;">
    <a href="{{url}}" style="background: #ffc107; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">{{text}}</a>
</div>',
                'preview' => 'Yellow warning button',
                'is_active' => true,
                'sort_order' => 33,
            ],
            [
                'name' => 'Text Link Button',
                'key' => 'cta-text-link',
                'category' => 'Button',
                'description' => 'Simple text link styled as button',
                'html' => '<div style="text-align: center; margin-top: 20px;">
    <a href="{{url}}" style="color: #2CB8B4; text-decoration: none;">{{text}}</a>
</div>',
                'preview' => 'Text link button',
                'is_active' => true,
                'sort_order' => 34,
            ],

            // INFO BOXES (From existing emails)
            [
                'name' => 'Property Details Box',
                'key' => 'property-details-box',
                'category' => 'System',
                'description' => 'Gray box displaying property information',
                'html' => '<div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #2CB8B4;">Property Details</h3>
    <p><strong>Address:</strong> {{property.address}}</p>
    <p><strong>Postcode:</strong> {{property.postcode}}</p>
    <p><strong>Asking Price:</strong> £{{property.asking_price}}</p>
</div>',
                'preview' => 'Property information box',
                'is_active' => true,
                'sort_order' => 52,
            ],
            [
                'name' => 'Offer Details Box',
                'key' => 'offer-details-box',
                'category' => 'System',
                'description' => 'Gray box displaying offer information',
                'html' => '<div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #2CB8B4;">Offer Details</h3>
    <p><strong>Offer Amount:</strong> £{{offer.offer_amount}}</p>
    <p><strong>Asking Price:</strong> £{{property.asking_price}}</p>
    <p><strong>Buyer:</strong> {{buyer.name}}</p>
    <p><strong>Funding Type:</strong> {{offer.funding_type}}</p>
    <p><strong>Offer Date:</strong> {{offer.created_at}}</p>
</div>',
                'preview' => 'Offer information box',
                'is_active' => true,
                'sort_order' => 53,
            ],
            [
                'name' => 'Viewing Details Box',
                'key' => 'viewing-details-box',
                'category' => 'System',
                'description' => 'Gray box displaying viewing information',
                'html' => '<div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #2CB8B4;">Viewing Details</h3>
    <p><strong>Requested Date:</strong> {{viewing.viewing_date}}</p>
    <p><strong>Requested Time:</strong> {{viewing.viewing_time}}</p>
    <p><strong>Status:</strong> {{viewing.status}}</p>
    <p><strong>Property:</strong> {{property.address}}</p>
    <p><strong>Buyer:</strong> {{buyer.name}}</p>
</div>',
                'preview' => 'Viewing information box',
                'is_active' => true,
                'sort_order' => 54,
            ],
            [
                'name' => 'Valuation Details Box',
                'key' => 'valuation-details-box',
                'category' => 'System',
                'description' => 'Gray box displaying valuation information',
                'html' => '<div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #2CB8B4;">Valuation Request Details</h3>
    <p><strong>Property Address:</strong> {{valuation.property_address}}</p>
    <p><strong>Postcode:</strong> {{valuation.postcode}}</p>
    <p><strong>Preferred Date:</strong> {{valuation.valuation_date}}</p>
</div>',
                'preview' => 'Valuation information box',
                'is_active' => true,
                'sort_order' => 55,
            ],
            [
                'name' => 'Instruction Details Box',
                'key' => 'instruction-details-box',
                'category' => 'System',
                'description' => 'Gray box displaying instruction information',
                'html' => '<div style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #2CB8B4;">Instruction Details</h3>
    <p><strong>Signed Date:</strong> {{instruction.signed_at}}</p>
    <p><strong>Fee Percentage:</strong> {{instruction.fee_percentage}}%</p>
    <p><strong>Status:</strong> {{instruction.status}}</p>
</div>',
                'preview' => 'Instruction information box',
                'is_active' => true,
                'sort_order' => 56,
            ],

            // ACTION REQUIRED BOXES (From existing emails)
            [
                'name' => 'Action Required Box',
                'key' => 'action-required',
                'category' => 'System',
                'description' => 'Teal box for action required messages',
                'html' => '<div style="background: #E8F4F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0;"><strong>Action Required:</strong> {{message}}</p>
</div>',
                'preview' => 'Action required notice',
                'is_active' => true,
                'sort_order' => 57,
            ],
            [
                'name' => 'Success Message Box',
                'key' => 'success-message',
                'category' => 'System',
                'description' => 'Green success message box',
                'html' => '<div style="background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; font-weight: 600; color: #155724;">{{message}}</p>
</div>',
                'preview' => 'Success message box',
                'is_active' => true,
                'sort_order' => 58,
            ],
            [
                'name' => 'Warning Message Box',
                'key' => 'warning-message',
                'category' => 'System',
                'description' => 'Yellow warning message box',
                'html' => '<div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #856404;">⚠️ {{title}}</h3>
    <p style="color: #856404; margin: 10px 0;">{{message}}</p>
</div>',
                'preview' => 'Warning message box',
                'is_active' => true,
                'sort_order' => 59,
            ],
            [
                'name' => 'Error Message Box',
                'key' => 'error-message',
                'category' => 'System',
                'description' => 'Red error message box',
                'html' => '<div style="background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; color: #721c24;">{{message}}</p>
</div>',
                'preview' => 'Error message box',
                'is_active' => true,
                'sort_order' => 60,
            ],

            // TEXT WIDGETS
            [
                'name' => 'Heading 1 - Abodeology Style',
                'key' => 'heading-1-abodeology',
                'category' => 'Text',
                'description' => 'Main heading in Abodeology teal color',
                'html' => '<h2 style="color: #2CB8B4;">{{text}}</h2>',
                'preview' => 'Teal heading',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Heading 2 - Abodeology Style',
                'key' => 'heading-2-abodeology',
                'category' => 'Text',
                'description' => 'Sub heading in Abodeology teal color',
                'html' => '<h3 style="color: #2CB8B4;">{{text}}</h3>',
                'preview' => 'Teal sub-heading',
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'Paragraph',
                'key' => 'paragraph',
                'category' => 'Text',
                'description' => 'Standard paragraph text',
                'html' => '<p style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">{{text}}</p>',
                'preview' => 'Paragraph text',
                'is_active' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'Greeting',
                'key' => 'greeting',
                'category' => 'Text',
                'description' => 'Personalized greeting',
                'html' => '<p>Dear {{recipient.name}},</p>',
                'preview' => 'Greeting line',
                'is_active' => true,
                'sort_order' => 23,
            ],

            // LIST WIDGETS
            [
                'name' => 'Bullet List',
                'key' => 'bullet-list',
                'category' => 'Text',
                'description' => 'Unordered bullet list',
                'html' => '<ul style="margin: 10px 0; padding-left: 25px;">
    <li>{{item1}}</li>
    <li>{{item2}}</li>
    <li>{{item3}}</li>
</ul>',
                'preview' => 'Bullet list',
                'is_active' => true,
                'sort_order' => 24,
            ],
            [
                'name' => 'Numbered List',
                'key' => 'numbered-list',
                'category' => 'Text',
                'description' => 'Ordered numbered list',
                'html' => '<ol style="margin: 10px 0; padding-left: 25px;">
    <li>{{item1}}</li>
    <li>{{item2}}</li>
    <li>{{item3}}</li>
</ol>',
                'preview' => 'Numbered list',
                'is_active' => true,
                'sort_order' => 25,
            ],

            // LAYOUT WIDGETS
            [
                'name' => 'Email Container',
                'key' => 'email-container',
                'category' => 'Layout',
                'description' => 'Main email body container',
                'html' => '<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    {{content}}
</body>',
                'preview' => 'Email body container',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Section Spacer',
                'key' => 'section-spacer',
                'category' => 'Layout',
                'description' => 'Vertical spacing between sections',
                'html' => '<div style="margin: 20px 0;">&nbsp;</div>',
                'preview' => 'Section spacer',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Horizontal Divider',
                'key' => 'divider',
                'category' => 'Layout',
                'description' => 'Horizontal divider line',
                'html' => '<hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">',
                'preview' => 'Horizontal divider',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        // Delete widgets that are not in the current list
        $keys = array_column($widgets, 'key');
        EmailWidget::query()->whereNotIn('key', $keys)->delete();

        // Create or update widgets
        foreach ($widgets as $widget) {
            EmailWidget::updateOrCreate(
                ['key' => $widget['key']],
                $widget
            );
        }

        $this->command->info('✅ Email widgets seeded successfully!');
        $this->command->info('   Total widgets: ' . count($widgets));
    }
}
