# Abodeology Business Workflow Verification

## ✅ Step 1: INTAKE & VALUATION

**Status:** ✅ **VERIFIED - IMPLEMENTED**

### Seller completes online valuation form
- ✅ Route: `POST /valuation/booking` (`ValuationController@storeBooking`)
- ✅ Form at `/valuation/booking` collects: name, email, phone, role, property address, postcode, property type, bedrooms, notes
- ✅ Creates user account if doesn't exist
- ✅ Generates secure password and sends login credentials email
- ✅ Creates `Valuation` record with status 'pending'

**Files:**
- `app/Http/Controllers/ValuationController.php` (lines 38-178)
- `resources/views/valuation/booking.blade.php`
- `app/Mail/ValuationLoginCredentials.php`

### Admin/Agent schedules valuation appointment
- ✅ Admin/Agent can view pending valuations in dashboard
- ✅ Can schedule appointment by setting `valuation_date` and `valuation_time`
- ✅ Status changes to 'scheduled' when date/time is set
- ✅ Notifications sent to agents/admins about new valuation requests

**Files:**
- `app/Http/Controllers/AdminController.php` (method: `updateValuationSchedule`)
- `resources/views/admin/valuations/show.blade.php`

### Agent attends property, conducts valuation, and completes valuation pro forma
- ✅ Agent accesses valuation form via `/admin/valuations/{id}/valuation-form`
- ✅ Form captures: property details, material information, access details, notes
- ✅ Can be completed on-site during valuation visit
- ✅ Saves to `Property` model with status `property_details_captured`
- ✅ Valuation status updated to 'completed'

**Files:**
- `app/Http/Controllers/AdminController.php` (method: `storeValuationForm`)
- `resources/views/admin/valuations/valuation-form.blade.php`

### Agent captures 360 imagery for the HomeCheck report during the visit
- ✅ Agent can upload 360-degree images during HomeCheck completion
- ✅ Images marked with `is_360` flag
- ✅ Stored in `homechecks/{property_id}/rooms/{room_name}/360/` directory
- ✅ Separate from regular photos stored in `photos/` subdirectory

**Files:**
- `app/Http/Controllers/AdminController.php` (method: `storeCompleteHomeCheck`, line 1301-1353)
- `app/Models/HomecheckData.php` (has `is_360` field)

---

## ⚠️ Step 2: INSTRUCTION & HOMECHECK

**Status:** ⚠️ **PARTIALLY VERIFIED - NEEDS CLARIFICATION**

### Seller is notified of market value and invited to instruct
- ✅ After valuation form completion, instruction request email sent automatically
- ✅ Seller receives email with link to sign Terms & Conditions
- ✅ Instruction form available at `/seller/property/{id}/onboarding`

**Files:**
- `app/Http/Controllers/AdminController.php` (lines 891-911)
- `app/Mail/InstructionRequestNotification.php`

### Instruction triggers release of the full HomeCheck report
- ⚠️ **ISSUE FOUND:** HomeCheck report is only visible to seller when status is `completed`
- ⚠️ Current code shows report availability is conditional: `$completedHomeCheck && $completedHomeCheck->report_path`
- ⚠️ The workflow states "immediate access" but code requires HomeCheck to be completed first

**Current Implementation:**
- Seller can see HomeCheck report in dashboard only if `status = 'completed'`
- Report view available at `/seller/homecheck-report/{property_id}`
- Welcome Pack email sent after instruction signing mentions HomeCheck but doesn't guarantee immediate access

**Files:**
- `resources/views/seller/properties/show.blade.php` (lines 393-411)
- `resources/views/seller/post-instruction-dashboard.blade.php` (lines 646-724)

**Recommendation:** 
- If HomeCheck should be available immediately upon instruction signing, need to change logic to show report regardless of completion status (or ensure HomeCheck is completed before instruction)
- If HomeCheck should only be available after completion, workflow description needs updating

### Seller gains immediate access — the technical assessment is now in their hands before marketing begins
- ⚠️ **DEPENDS ON ABOVE:** If HomeCheck must be completed first, this statement is inaccurate
- ✅ Seller dashboard shows HomeCheck report section after instruction signed
- ✅ Report accessible via "View HomeCheck Report" button

---

## ✅ Step 3: PREPARATION & PRESENTATION

**Status:** ✅ **VERIFIED - IMPLEMENTED**

### Professional photography and staging are scheduled
- ✅ Agent can upload photos via `/admin/properties/{id}/listing-upload`
- ✅ Photos stored in `property_photos` table with sort order
- ✅ Primary photo can be set
- ✅ Floorplan and EPC upload supported

**Files:**
- `app/Http/Controllers/AdminController.php` (method: `showListingUpload`, `storeListingUpload`)

### Property is prepared for market with insight from the HomeCheck informing presentation strategy
- ✅ HomeCheck report includes AI analysis with improvement suggestions
- ✅ Report available to seller and agent before marketing
- ✅ Room-by-room analysis and condition ratings provided

**Files:**
- `app/Services/HomeCheckReportService.php`
- `resources/views/seller/homecheck-report.blade.php`

### Listing goes live on major portals
- ✅ Agent can publish listing via "Publish Listing to Portals" button
- ✅ System updates property status to 'live'
- ✅ Portal integration framework exists for Rightmove, Zoopla, OnTheMarket
- ✅ RTDF file generation for Rightmove implemented
- ⚠️ Portal publishing is currently simulated (logs actions, doesn't make actual API calls)

**Files:**
- `app/Http/Controllers/AdminController.php` (methods: `publishListing`, `publishToPortals`)
- `app/Services/RTDFGeneratorService.php`
- `config/rightmove.php`

---

## ✅ Step 4: VIEWINGS & FEEDBACK

**Status:** ✅ **VERIFIED - IMPLEMENTED**

### Enquiries route through the Abodeology system
- ✅ Buyers can view live properties
- ✅ Property listings show on buyer dashboard
- ✅ Enquiries managed through system

### Buyers must register to enter the pipeline — enabling booking, offer management, and feedback capture
- ✅ **VERIFIED:** Viewing request route requires `auth` middleware
- ✅ Route: `/buyer/property/{id}/viewing-request` requires authentication
- ✅ Buyer role required: `if (!in_array($user->role, ['buyer', 'both']))`
- ✅ Buyers must be logged in to book viewings
- ✅ Offers also require buyer authentication

**Files:**
- `routes/web.php` (line 133: `Route::middleware(['auth'])->get('/buyer/property/{id}/viewing-request'`)
- `app/Http/Controllers/BuyerController.php` (method: `storeViewingRequest`, line 508)

### Viewings are allocated to: Abodeology agents, or Property Viewing Assistants (PVAs)
- ✅ Admin/Agent can assign viewings to PVAs via `/admin/viewings/{id}/assign`
- ✅ Viewings have `pva_id` field for PVA assignment
- ✅ Viewings can be assigned to agents (via `assigned_agent_id` on property)
- ✅ PVAs see assigned viewings in their dashboard

**Files:**
- `app/Http/Controllers/AdminController.php` (methods: `showAssignViewing`, `storeAssignViewing`)
- `app/Models/Viewing.php` (has `pva_id` relationship)

### Each user accesses a dedicated app to log and report real-time feedback
- ✅ PVA feedback form available at `/pva/viewings/{id}/feedback`
- ✅ Feedback includes: buyer interest level, buyer feedback, buyer questions, property condition, notes
- ✅ Feedback stored in `ViewingFeedback` model
- ✅ Viewing status updated to 'completed' after feedback submission
- ✅ API endpoint for feedback submission exists (`POST /api/viewings/{id}/feedback`)

**Files:**
- `app/Http/Controllers/PVAController.php` (methods: `showFeedback`, `storeFeedback`)
- `app/Http/Controllers/Api/ViewingController.php` (method: `submitFeedback`)
- `app/Models/ViewingFeedback.php`

---

## ❌ Step 5: FEE STRUCTURE / NEGOTIATION POINT

**Status:** ❌ **NOT IMPLEMENTED**

### Standard fee reflects full-service package including HomeCheck, strategy, and accompanied viewings
- ✅ Fee percentage stored in `PropertyInstruction.fee_percentage` (default 1.5%)
- ✅ Fee displayed in instruction form
- ✅ Standard fee structure exists

**Files:**
- `app/Models/PropertyInstruction.php` (has `fee_percentage` field)
- `resources/views/seller/instruct.blade.php` (shows fee)

### Reduced fee option available for vendors who elect to host their own viewings — reflecting their contribution to the process
- ❌ **NOT FOUND:** No field or logic for self-hosted viewings
- ❌ No `self_host_viewings` or `host_own_viewings` field in `PropertyInstruction`
- ❌ No fee reduction calculation based on viewing hosting preference
- ❌ No UI option for seller to choose self-hosted viewings
- ❌ No conditional fee logic in instruction signing

**Missing Implementation:**
- Need to add `self_host_viewings` boolean field to `PropertyInstruction`
- Need to add fee reduction logic (e.g., if self_host_viewings = true, reduce fee by X%)
- Need to add UI option in instruction form for seller to choose
- Need to update fee calculation when instruction is signed

**Recommendation:**
- Add migration to add `self_host_viewings` boolean to `property_instructions` table
- Add fee reduction logic (e.g., 0.25% reduction if self-hosted)
- Update instruction form to include checkbox/radio for viewing hosting preference
- Update fee display to show reduced fee if self-hosted option selected

---

## Summary

| Step | Status | Notes |
|------|--------|-------|
| 1. Intake & Valuation | ✅ Verified | All components implemented |
| 2. Instruction & HomeCheck | ⚠️ Needs Clarification | HomeCheck access timing unclear |
| 3. Preparation & Presentation | ✅ Verified | Portal integration simulated |
| 4. Viewings & Feedback | ✅ Verified | All components implemented |
| 5. Fee Structure | ❌ Not Implemented | Self-hosted viewing fee reduction missing |

**Action Items:**
1. Clarify HomeCheck release timing: Should it be available immediately upon instruction signing or only after completion?
2. Implement self-hosted viewing fee reduction feature
3. Consider making portal integration production-ready (currently simulated)
