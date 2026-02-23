<?php

namespace Database\Seeders;

use App\Constants\EmailActions;
use App\Models\EmailTemplate;
use App\Models\EmailWidget;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for created_by
        $admin = User::where('role', 'admin')->first();
        $createdBy = $admin ? $admin->id : 1;

        // Get widgets by key for easy access
        $widgets = [];
        EmailWidget::all()->each(function($widget) use (&$widgets) {
            $widgets[$widget->key] = $widget->html;
        });

        $templates = [
            // VALUATION LOGIN CREDENTIALS
            [
                'name' => 'Valuation Login Credentials - Default',
                'action' => EmailActions::VALUATION_LOGIN_CREDENTIALS,
                'subject' => 'Welcome to Abodeology - Your Account Details',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Welcome to Abodeology!</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Thanks for booking your property valuation with Abodeology. We\'ve received your request and created your account so you can manage everything in one place.</p>' . "\n\n" .
                    ($widgets['valuation-details-box'] ?? '') . "\n\n" .
                    ($widgets['login-credentials'] ?? '') . "\n\n" .
                    '<p>You can log in at any time to view your request and any updates. For security, we recommend changing your password after your first login.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>If you have any questions before your valuation, just reply to this email — we\'re here to help.</p>' . "\n\n" .
                    '<p>Kind regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['abodeology-footer'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // VALUATION REQUEST
            [
                'name' => 'Valuation Request - Default',
                'action' => EmailActions::VALUATION_REQUEST,
                'subject' => 'Valuation Request Received',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Thank You for Your Valuation Request</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Thank you for requesting a property valuation with Abodeology. We have received your request and will be in touch shortly to arrange a convenient time for your valuation appointment.</p>' . "\n\n" .
                    ($widgets['valuation-details-box'] ?? '') . "\n\n" .
                    '<p>Our team will review your request and contact you within 24 hours to confirm the details.</p>' . "\n\n" .
                    '<p>If you have any questions in the meantime, please don\'t hesitate to contact us.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // POST VALUATION
            [
                'name' => 'Post Valuation - Default',
                'action' => EmailActions::POST_VALUATION,
                'subject' => 'Thank You for Your Valuation Appointment',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Thank You for Your Valuation</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Thank you for meeting with us today for your property valuation. We hope you found the appointment helpful and informative.</p>' . "\n\n" .
                    '<p>Your property details have been captured and we will be in touch shortly with next steps regarding your property listing.</p>' . "\n\n" .
                    '<p>If you have any questions, please don\'t hesitate to contact us.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // INSTRUCTION REQUEST
            [
                'name' => 'Instruction Request - Default',
                'action' => EmailActions::INSTRUCTION_REQUEST,
                'subject' => 'Terms & Conditions - Please Sign',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Terms & Conditions of Business</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Please review and sign the Terms & Conditions of Business for your property at {{property.address}}.</p>' . "\n\n" .
                    ($widgets['instruction-details-box'] ?? '') . "\n\n" .
                    ($widgets['warning-message'] ?? '') . "\n\n" .
                    '<p>Please click the link below to review and sign the Terms & Conditions:</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>If you have any questions about the terms, please contact us before signing.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // WELCOME PACK
            [
                'name' => 'Welcome Pack - Default',
                'action' => EmailActions::WELCOME_PACK,
                'subject' => 'Welcome to Abodeology - Your Welcome Pack',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Welcome to Abodeology!</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Congratulations! Your instruction has been signed successfully. We\'re excited to help you sell your property.</p>' . "\n\n" .
                    ($widgets['success-message'] ?? '') . "\n\n" .
                    '<h3 style="color: #2CB8B4;">What Happens Next?</h3>' . "\n\n" .
                    ($widgets['bullet-list'] ?? '') . "\n\n" .
                    '<p>Your dedicated agent will be in touch shortly to discuss the next steps in marketing your property.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // VIEWING REQUEST
            [
                'name' => 'Viewing Request - Default',
                'action' => EmailActions::VIEWING_REQUEST,
                'subject' => 'New Viewing Request for Your Property',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">New Viewing Request</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>You have received a new viewing request for your property.</p>' . "\n\n" .
                    ($widgets['viewing-details-box'] ?? '') . "\n\n" .
                    ($widgets['action-required'] ?? '') . "\n\n" .
                    '<p>Please log in to your dashboard to confirm or reschedule this viewing.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // VIEWING CONFIRMED
            [
                'name' => 'Viewing Confirmed - Default',
                'action' => EmailActions::VIEWING_CONFIRMED,
                'subject' => 'Viewing Confirmed - {{viewing.viewing_date}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Viewing Confirmed</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    ($widgets['success-message'] ?? '') . "\n\n" .
                    ($widgets['viewing-details-box'] ?? '') . "\n\n" .
                    '<p>We look forward to showing you the property. If you need to reschedule, please contact us as soon as possible.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // VIEWING ASSIGNED
            [
                'name' => 'Viewing Assigned - Default',
                'action' => EmailActions::VIEWING_ASSIGNED,
                'subject' => 'Viewing Assignment - {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Viewing Assignment</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>You have been assigned to conduct a viewing.</p>' . "\n\n" .
                    ($widgets['viewing-details-box'] ?? '') . "\n\n" .
                    '<p>Please ensure you have all necessary information and access details before the viewing.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // NEW OFFER
            [
                'name' => 'New Offer - Default',
                'action' => EmailActions::NEW_OFFER,
                'subject' => 'New Offer Received for {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">New Offer Received</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>You have received a new offer for your property.</p>' . "\n\n" .
                    ($widgets['offer-details-box'] ?? '') . "\n\n" .
                    ($widgets['action-required'] ?? '') . "\n\n" .
                    '<p>Please log in to your dashboard to review and respond to this offer.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // OFFER DECISION
            [
                'name' => 'Offer Decision - Default',
                'action' => EmailActions::OFFER_DECISION,
                'subject' => 'Offer Decision - {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Offer Decision</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>The seller has made a decision regarding your offer.</p>' . "\n\n" .
                    ($widgets['offer-details-box'] ?? '') . "\n\n" .
                    '<p>Please log in to your dashboard to view the decision and next steps.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // OFFER AMOUNT RELEASED
            [
                'name' => 'Offer Amount Released - Default',
                'action' => EmailActions::OFFER_AMOUNT_RELEASED,
                'subject' => 'Offer Amount Released - {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Offer Amount Released</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    ($widgets['success-message'] ?? '') . "\n\n" .
                    '<p>The offer amount of £{{offer.offer_amount}} has been released.</p>' . "\n\n" .
                    '<p>Please check your account for details.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // OFFER DISCUSSION REQUEST
            [
                'name' => 'Offer Discussion Request - Default',
                'action' => EmailActions::OFFER_DISCUSSION_REQUEST,
                'subject' => 'Discussion Request - Offer for {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Offer Discussion Request</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>The seller would like to discuss your offer.</p>' . "\n\n" .
                    ($widgets['offer-details-box'] ?? '') . "\n\n" .
                    ($widgets['action-required'] ?? '') . "\n\n" .
                    '<p>Please contact us to arrange a discussion.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // MEMORANDUM OF SALE
            [
                'name' => 'Memorandum of Sale - Default',
                'action' => EmailActions::MEMORANDUM_OF_SALE,
                'subject' => 'Memorandum of Sale - {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Memorandum of Sale</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    ($widgets['success-message'] ?? '') . "\n\n" .
                    ($widgets['property-details-box'] ?? '') . "\n\n" .
                    '<p>The memorandum of sale has been generated. Please review the details and contact us if you have any questions.</p>' . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // MEMORANDUM PENDING INFO
            [
                'name' => 'Memorandum Pending Info - Default',
                'action' => EmailActions::MEMORANDUM_PENDING_INFO,
                'subject' => 'Additional Information Required - Memorandum of Sale',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Additional Information Required</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    ($widgets['warning-message'] ?? '') . "\n\n" .
                    '<p>We need some additional information to complete the memorandum of sale.</p>' . "\n\n" .
                    ($widgets['action-required'] ?? '') . "\n\n" .
                    '<p>Please log in to your dashboard to provide the required information.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // PROPERTY STATUS CHANGED
            [
                'name' => 'Property Status Changed - Default',
                'action' => EmailActions::PROPERTY_STATUS_CHANGED,
                'subject' => 'Property Status Update - {{property.address}}',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Property Status Update</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>The status of your property has been updated.</p>' . "\n\n" .
                    ($widgets['property-details-box'] ?? '') . "\n\n" .
                    '<p>Please log in to your dashboard to view the updated status.</p>' . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],

            // PVA CREATED
            [
                'name' => 'PVA Created - Default',
                'action' => EmailActions::PVA_CREATED,
                'subject' => 'Welcome to Abodeology - PVA Account',
                'body' => ($widgets['abodeology-header'] ?? '') . "\n\n" .
                    '<h2 style="color: #2CB8B4;">Welcome to Abodeology as a Property Viewing Assistant!</h2>' . "\n\n" .
                    '<p>Hello {{recipient.name}},</p>' . "\n\n" .
                    '<p>Your Property Viewing Assistant (PVA) account has been created. You can now access the PVA dashboard to view and manage viewing assignments.</p>' . "\n\n" .
                    ($widgets['login-credentials'] ?? '') . "\n\n" .
                    ($widgets['security-note'] ?? '') . "\n\n" .
                    ($widgets['cta-primary'] ?? '') . "\n\n" .
                    '<p>Best regards,<br>The Abodeology Team</p>' . "\n\n" .
                    ($widgets['footer-support'] ?? ''),
                'template_type' => 'default',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        // Create or update templates
        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                [
                    'action' => $template['action'],
                    'template_type' => $template['template_type'],
                ],
                $template
            );
        }

        $this->command->info('✅ Email templates seeded successfully!');
        $this->command->info('   Total templates: ' . count($templates));
    }
}
