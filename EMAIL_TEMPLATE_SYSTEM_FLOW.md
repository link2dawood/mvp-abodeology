# Email Template System - Flow & Working Explanation

## Overview

The Abodeology platform uses a flexible, customizable email template system that allows administrators to manage email content and styling without requiring code changes. All emails follow a consistent brand design while allowing customization of subject lines and body content.

---

## System Architecture

### Components

1. **Email Mailable Classes** (`app/Mail/`)
   - Each email type has a dedicated Mailable class
   - Handles email logic, data preparation, and template selection
   - Examples: `NewOfferNotification`, `OfferDiscussionRequest`, `MemorandumOfSale`

2. **Email Template Service** (`app/Services/EmailTemplateService.php`)
   - Core service that manages template retrieval and rendering
   - Handles variable replacement in templates
   - Manages template assignments to email actions

3. **Email Templates** (`app/Models/EmailTemplate.php`)
   - Database-stored templates with subject and body
   - Supports HTML content with variable placeholders
   - Can be activated/deactivated and assigned to specific actions

4. **Email Views** (`resources/views/emails/`)
   - Default Blade templates with consistent styling
   - Used when no custom template override exists
   - All follow the same design pattern

5. **Email Actions** (`app/Constants/EmailActions.php`)
   - Constants defining all email types in the system
   - Used to link templates to specific email scenarios

---

## Email Flow Process

### Step-by-Step Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. EVENT TRIGGERS EMAIL                                     │
│    (e.g., New offer received, Property status changed)      │
└────────────────────┬────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. MAILABLE CLASS CREATED                                   │
│    - Prepares data (offer, property, user, etc.)           │
│    - Calls EmailTemplateService                             │
└────────────────────┬────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. TEMPLATE LOOKUP                                          │
│    EmailTemplateService checks:                              │
│    a) Active template assignment for this action            │
│    b) Fallback to default Blade view template               │
└────────────────────┬────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. TEMPLATE RENDERING                                       │
│    IF Custom Template Found:                                │
│    - Replace variables ({{property.address}}, etc.)        │
│    - Render HTML with custom content                        │
│    ELSE:                                                     │
│    - Use default Blade view with consistent styling         │
└────────────────────┬────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. EMAIL SENT                                               │
│    - Subject line rendered                                  │
│    - HTML body with consistent styling                      │
│    - Sent via Laravel Mail system                           │
└─────────────────────────────────────────────────────────────┘
```

---

## Email Types & Triggers

### Available Email Actions

| Email Action | Trigger Event | Recipients |
|-------------|---------------|------------|
| `instruction_request` | Agent requests seller to sign T&C | Seller |
| `welcome_pack` | Instruction signed successfully | Seller |
| `post_valuation` | After valuation appointment completed | Seller |
| `valuation_request` | New valuation booking submitted | Agents/Admins |
| `valuation_login_credentials` | New user account created | New User |
| `viewing_request` | Buyer requests property viewing | Viewing Partners |
| `viewing_confirmed` | Viewing scheduled/confirmed | Buyer, Seller, PVA |
| `viewing_assigned` | Viewing assigned to PVA | PVA |
| `new_offer` | New offer submitted | Seller, Agents, Admins |
| `offer_decision` | Seller accepts/declines/counters offer | Buyer |
| `offer_amount_released` | Agent releases offer amount to seller | Seller |
| `offer_discussion_request` | Seller requests discussion with agent | Agent/Admin |
| `memorandum_of_sale` | Offer accepted, MoS generated | Seller, Buyer, Solicitors |
| `memorandum_pending_info` | Missing solicitor info for MoS | Seller/Buyer |
| `property_status_changed` | Property status updated (sold/withdrawn) | Interested Parties |
| `pva_created` | New PVA account created | PVA |

---

## Template Customization System

### How Templates Work

#### 1. **Default Templates (Blade Views)**
- Located in `resources/views/emails/`
- Always available as fallback
- Use consistent styling:
  - Logo header with black background (#0F0F0F)
  - Teal brand color (#2CB8B4) for headings and links
  - Light grey content boxes (#F4F4F4)
  - Consistent spacing and typography

#### 2. **Custom Templates (Database)**
- Created and managed via Admin Panel
- Stored in `email_templates` table
- Can override default templates
- Support variable placeholders

#### 3. **Template Assignment**
- Templates can be assigned to specific email actions
- Only one active template per action
- Assignments stored in `email_template_assignments` table

### Variable System

Templates support dynamic variables using `{{variable}}` syntax:

**Simple Variables:**
```
{{user.name}}
{{property.address}}
{{offer.offer_amount}}
```

**Nested Variables (Dot Notation):**
```
{{property.seller.name}}
{{offer.buyer.email}}
{{property.postcode}}
```

**Common Variables by Context:**

**Offer Emails:**
- `{{offer.offer_amount}}` - Offer amount
- `{{offer.buyer.name}}` - Buyer name
- `{{offer.status}}` - Offer status
- `{{property.address}}` - Property address
- `{{property.asking_price}}` - Asking price

**Property Emails:**
- `{{property.address}}` - Property address
- `{{property.postcode}}` - Postcode
- `{{property.seller.name}}` - Seller name
- `{{status}}` - Property status

**User Emails:**
- `{{user.name}}` - User name
- `{{user.email}}` - User email
- `{{password}}` - Temporary password (if applicable)

---

## Design Consistency

### Standard Email Design Elements

All emails follow a consistent design pattern:

#### Header Section
```
┌─────────────────────────────────────┐
│  [Black Background #0F0F0F]        │
│                                     │
│     [Abodeology Logo]               │
│                                     │
└─────────────────────────────────────┘
```

#### Content Structure
```
┌─────────────────────────────────────┐
│  Heading (Teal #2CB8B4)            │
│                                     │
│  Greeting text                      │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ Content Box (#F4F4F4)       │   │
│  │ - Details                    │   │
│  │ - Information                │   │
│  └─────────────────────────────┘   │
│                                     │
│  [Action Button - Teal #2CB8B4]    │
│                                     │
│  Footer text                        │
└─────────────────────────────────────┘
```

#### Color Palette
- **Primary Teal:** `#2CB8B4` - Headings, links, buttons
- **Black:** `#0F0F0F` - Header background
- **Light Grey:** `#F4F4F4` - Content boxes
- **White:** `#FFFFFF` - Button text, backgrounds
- **Dark Text:** `#1E1E1E` - Body text
- **Soft Grey:** `#F4F4F4` - Backgrounds

#### Typography
- **Font Family:** Arial, sans-serif
- **Line Height:** 1.6
- **Max Width:** 600px (responsive)
- **Padding:** 20px margins

---

## Admin Management Flow

### Creating a Custom Template

1. **Access Admin Panel**
   - Navigate to Email Templates section
   - Click "Create New Template"

2. **Configure Template**
   - **Name:** Descriptive name (e.g., "New Offer - Premium Template")
   - **Action:** Select email action (e.g., `new_offer`)
   - **Subject:** Email subject with variables (e.g., "New Offer - {{property.address}}")
   - **Body:** HTML content with variables
   - **Template Type:** Choose "Override" to replace default view
   - **Status:** Set to Active

3. **Assign Template**
   - Assign to specific email action
   - Only one active assignment per action

4. **Preview**
   - Use preview feature with sample data
   - Test variable replacement
   - Verify styling

### Template Priority

The system follows this priority order:

1. **Active Template Assignment** (Highest Priority)
   - Explicitly assigned template for the action
   - Must be active

2. **Active Template by Action**
   - Template directly linked to action
   - Must be active

3. **Default Blade View** (Fallback)
   - Always available
   - Consistent styling guaranteed

---

## Example: New Offer Email Flow

### Scenario
A buyer submits an offer for a property.

### Flow Details

1. **Event Trigger**
   ```php
   // In OfferController or similar
   Mail::to($seller->email)->send(
       new NewOfferNotification($offer, $property, $seller)
   );
   ```

2. **Mailable Class Processing**
   ```php
   // NewOfferNotification.php
   public function content(): Content
   {
       $templateService = app(EmailTemplateService::class);
       $data = ['offer' => $offer, 'property' => $property, 'recipient' => $seller];
       
       $template = $templateService->getTemplateForAction(
           EmailActions::NEW_OFFER, 
           $data
       );
       
       if ($template && $template->template_type === 'override') {
           return new Content(
               htmlString: $templateService->renderTemplate($template, $data)
           );
       }
       
       return new Content(
           view: 'emails.new-offer-notification',
           with: $data
       );
   }
   ```

3. **Template Rendering**
   - If custom template exists: Variables replaced, HTML rendered
   - If no custom template: Default Blade view used with data

4. **Email Sent**
   - Subject: "New Offer Received - {{property.address}}"
   - Body: Styled HTML with offer details
   - Recipient receives formatted email

---

## Benefits of This System

### For Administrators
- ✅ **No Code Changes Required** - Manage emails via admin panel
- ✅ **Quick Updates** - Change content without deployment
- ✅ **A/B Testing** - Create multiple templates and test
- ✅ **Consistent Branding** - All emails follow same design
- ✅ **Variable Support** - Dynamic content automatically populated

### For Developers
- ✅ **Maintainable** - Clear separation of concerns
- ✅ **Extensible** - Easy to add new email types
- ✅ **Fallback System** - Default templates always available
- ✅ **Type Safety** - Constants for email actions

### For Users
- ✅ **Professional Appearance** - Consistent, branded emails
- ✅ **Clear Information** - Well-structured content
- ✅ **Mobile Responsive** - Works on all devices
- ✅ **Accessible** - Proper HTML structure

---

## Technical Implementation Details

### Template Storage
- Templates stored in `email_templates` database table
- Fields: `id`, `name`, `action`, `subject`, `body`, `template_type`, `is_active`, `variables`
- Supports HTML content with inline CSS (email-safe)

### Variable Resolution
- Uses `{{variable}}` placeholder syntax
- Supports dot notation for nested data: `{{property.seller.name}}`
- Automatically resolves from provided data array
- Missing variables return empty string (graceful degradation)

### Rendering Process
1. Extract placeholders from template
2. Resolve values from data array
3. Replace placeholders with actual values
4. Return rendered HTML string

### Email Delivery
- Uses Laravel Mail system
- Supports queueing for better performance
- Error handling prevents email failures from breaking workflows
- Logs errors for debugging

---

## Maintenance & Best Practices

### Template Management
- Keep default templates updated with latest design
- Test templates with sample data before activation
- Document available variables for each email action
- Version control templates for rollback capability

### Design Guidelines
- Always use inline CSS (email clients don't support external stylesheets)
- Test across multiple email clients (Gmail, Outlook, Apple Mail)
- Keep email width under 600px for best compatibility
- Use web-safe fonts (Arial, Helvetica, sans-serif)
- Include alt text for images
- Ensure proper contrast for accessibility

### Variable Guidelines
- Use descriptive variable names
- Document all available variables per action
- Provide fallback values where appropriate
- Validate variables before rendering

---

## Troubleshooting

### Common Issues

**Email not using custom template:**
- Check template is active (`is_active = true`)
- Verify template assignment exists and is active
- Ensure `template_type = 'override'` for custom templates

**Variables not replacing:**
- Verify variable names match data keys
- Check variable syntax: `{{variable}}` not `{variable}` or `{{ variable }}`
- Ensure data is passed correctly to template service

**Styling issues:**
- Use inline CSS only
- Test in multiple email clients
- Check for conflicting styles
- Verify color codes are correct

**Email not sending:**
- Check mail configuration in `.env`
- Verify queue is running (if using queues)
- Check application logs for errors
- Ensure recipient email is valid

---

## Summary

The Abodeology email template system provides a flexible, maintainable solution for managing transactional emails. It combines:

- **Consistent Design** - All emails follow the same professional styling
- **Customization** - Administrators can customize content without code changes
- **Reliability** - Fallback system ensures emails always send
- **Flexibility** - Variable system supports dynamic content
- **Maintainability** - Clear structure and separation of concerns

This system ensures that all communications maintain brand consistency while allowing content customization to meet specific business needs.

