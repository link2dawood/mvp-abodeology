<?php

namespace Database\Seeders;

use App\Models\EmailWidget;
use Illuminate\Database\Seeder;

class EmailWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgets = [
            // ============================================
            // LAYOUT WIDGETS
            // ============================================
            [
                'key' => 'layout_one_column',
                'name' => 'One Column Container',
                'category' => 'Layout',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 20px;">
      {{content}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Single column container for email content',
            ],
            [
                'key' => 'layout_two_column',
                'name' => 'Two Column Container',
                'category' => 'Layout',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td width="50%" style="padding: 20px; vertical-align: top;">
      {{content_left}}
    </td>
    <td width="50%" style="padding: 20px; vertical-align: top;">
      {{content_right}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Two column layout for side-by-side content',
            ],
            [
                'key' => 'layout_spacer',
                'name' => 'Spacer',
                'category' => 'Layout',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td height="30" style="height: 30px; line-height: 30px;">&nbsp;</td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Add vertical spacing between sections',
            ],
            [
                'key' => 'layout_divider',
                'name' => 'Divider Line',
                'category' => 'Layout',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td style="padding: 20px 0;">
      <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 0;">
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Horizontal divider line',
            ],

            // ============================================
            // MEDIA WIDGETS
            // ============================================
            [
                'key' => 'media_logo_header',
                'name' => 'Logo Header',
                'category' => 'Media',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #0F0F0F; padding: 20px; text-align: center; border-radius: 8px;">
      <img src="{{logo_url}}" alt="Abodeology Logo" style="width: 160px; height: auto; object-fit: contain; max-width: 100%; display: block; margin: 0 auto;">
    </td>
  </tr>
</table>',
                'locked' => true,
                'description' => 'Abodeology logo header with black background',
            ],
            [
                'key' => 'media_image',
                'name' => 'Image',
                'category' => 'Media',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 20px; text-align: center;">
      <img src="{{image_url}}" alt="{{image_alt}}" style="max-width: 100%; height: auto; border-radius: 8px;">
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Single image widget',
            ],

            // ============================================
            // CONTENT WIDGETS
            // ============================================
            [
                'key' => 'content_heading',
                'name' => 'Heading',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 20px 20px 10px 20px;">
      <h2 style="color: #2CB8B4; margin: 0; font-size: 28px; font-weight: 600;">{{heading_text}}</h2>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Main heading with teal color',
            ],
            [
                'key' => 'content_text_block',
                'name' => 'Text Block',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 10px 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      {{text_content}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Basic text paragraph',
            ],
            [
                'key' => 'content_button',
                'name' => 'Button',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 20px; text-align: center;">
      <a href="{{button_url}}" style="background: #2CB8B4; color: #FFFFFF; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; font-size: 14px;">{{button_text}}</a>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Call-to-action button',
            ],
            [
                'key' => 'content_info_box',
                'name' => 'Info Box',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #E8F4F3; padding: 15px 20px; margin: 20px 0; border-radius: 4px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      {{info_content}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Information box with light teal background',
            ],
            [
                'key' => 'content_warning_box',
                'name' => 'Warning Box',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #fff3cd; padding: 15px 20px; margin: 20px 0; border-radius: 4px; border-left: 4px solid #ffc107; font-family: Arial, sans-serif; line-height: 1.6; color: #856404; font-size: 14px;">
      {{warning_content}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Warning/alert box with yellow background',
            ],
            [
                'key' => 'content_details_box',
                'name' => 'Details Box',
                'category' => 'Content',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">{{details_title}}</h3>
      {{details_content}}
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Details box with grey background and title',
            ],

            // ============================================
            // SYSTEM WIDGETS (From Existing Emails)
            // ============================================
            [
                'key' => 'system_offer_summary',
                'name' => 'Offer Summary',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">Offer Details</h3>
      <p style="margin: 8px 0;"><strong>Offer Amount:</strong> £{{offer_amount}}</p>
      <p style="margin: 8px 0;"><strong>Asking Price:</strong> £{{asking_price}}</p>
      <p style="margin: 8px 0;"><strong>Buyer:</strong> {{buyer_name}}</p>
      <p style="margin: 8px 0;"><strong>Funding Type:</strong> {{funding_type}}</p>
      <p style="margin: 8px 0;"><strong>Offer Date:</strong> {{offer_date}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Offer details summary box',
            ],
            [
                'key' => 'system_property_details',
                'name' => 'Property Details',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">Property Details</h3>
      <p style="margin: 8px 0;"><strong>Address:</strong> {{property_address}}</p>
      <p style="margin: 8px 0;"><strong>Postcode:</strong> {{property_postcode}}</p>
      <p style="margin: 8px 0;"><strong>Asking Price:</strong> £{{asking_price}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Property information box',
            ],
            [
                'key' => 'system_viewing_details',
                'name' => 'Viewing Details',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">Viewing Details</h3>
      <p style="margin: 8px 0;"><strong>Date:</strong> {{viewing_date}}</p>
      <p style="margin: 8px 0;"><strong>Time:</strong> {{viewing_time}}</p>
      <p style="margin: 8px 0;"><strong>Property Address:</strong> {{property_address}}</p>
      <p style="margin: 8px 0;"><strong>Buyer:</strong> {{buyer_name}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Viewing appointment details',
            ],
            [
                'key' => 'system_credentials_box',
                'name' => 'Login Credentials Box',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; border: 2px solid #2CB8B4; border-radius: 8px; padding: 20px; margin: 25px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <p style="margin: 0 0 10px 0;"><strong>Email:</strong> <span style="font-family: \'Courier New\', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{user_email}}</span></p>
      <p style="margin: 0;"><strong>Password:</strong> <span style="font-family: \'Courier New\', monospace; font-size: 16px; font-weight: bold; color: #2CB8B4;">{{user_password}}</span></p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Login credentials display box',
            ],
            [
                'key' => 'system_sale_details',
                'name' => 'Sale Details',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">Sale Details</h3>
      <p style="margin: 8px 0;"><strong>Property Address:</strong> {{property_address}}</p>
      <p style="margin: 8px 0;"><strong>Postcode:</strong> {{property_postcode}}</p>
      <p style="margin: 8px 0;"><strong>Agreed Sale Price:</strong> £{{sale_price}}</p>
      <p style="margin: 8px 0;"><strong>Seller:</strong> {{seller_name}}</p>
      <p style="margin: 8px 0;"><strong>Buyer:</strong> {{buyer_name}}</p>
      <p style="margin: 8px 0;"><strong>Accepted Date:</strong> {{accepted_date}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Memorandum of Sale details',
            ],
            [
                'key' => 'system_discussion_request',
                'name' => 'Discussion Request Details',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #F4F4F4; padding: 20px; border-radius: 8px; margin: 20px 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px; font-weight: 600;">Discussion Request Details</h3>
      <p style="margin: 8px 0;"><strong>Preferred Contact Method:</strong> {{contact_method}}</p>
      <p style="margin: 8px 0;"><strong>Urgency:</strong> {{urgency}}</p>
      <p style="margin: 8px 0;"><strong>Discussion Points:</strong> {{discussion_points}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Offer discussion request information',
            ],
            [
                'key' => 'system_seller_contact',
                'name' => 'Seller Contact Information',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="background: #fff3cd; padding: 15px 20px; margin: 20px 0; border-radius: 4px; border-left: 4px solid #ffc107; font-family: Arial, sans-serif; line-height: 1.6; color: #856404; font-size: 14px;">
      <p style="margin: 0 0 5px 0;"><strong>Seller Contact Information:</strong></p>
      <p style="margin: 5px 0;">Name: {{seller_name}}</p>
      <p style="margin: 5px 0;">Email: {{seller_email}}</p>
      <p style="margin: 5px 0;">Phone: {{seller_phone}}</p>
    </td>
  </tr>
</table>',
                'locked' => false,
                'description' => 'Seller contact details box',
            ],

            // ============================================
            // SYSTEM LOCKED WIDGETS
            // ============================================
            [
                'key' => 'system_footer',
                'name' => 'Email Footer',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <p>Best regards,<br><strong>The Abodeology Team</strong></p>
      <hr style="border: none; border-top: 1px solid #EAEAEA; margin: 30px 0;">
      <p style="font-size: 12px; color: #666; text-align: center;">
        © {{year}} Abodeology®. All rights reserved.
      </p>
    </td>
  </tr>
</table>',
                'locked' => true,
                'description' => 'Standard email footer with copyright',
            ],
            [
                'key' => 'system_support_link',
                'name' => 'Support Contact',
                'category' => 'System',
                'html' => '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
  <tr>
    <td style="padding: 10px 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; font-size: 14px;">
      <p>If you have any questions or need assistance, please don\'t hesitate to contact us at <a href="mailto:support@abodeology.co.uk" style="color: #2CB8B4; text-decoration: none;">support@abodeology.co.uk</a>.</p>
    </td>
  </tr>
</table>',
                'locked' => true,
                'description' => 'Support contact information',
            ],
        ];

        foreach ($widgets as $widget) {
            EmailWidget::updateOrCreate(
                ['key' => $widget['key']],
                $widget
            );
        }

        $this->command->info('Email widgets seeded successfully!');
    }
}
