# Complete Seller Testing Plan

## Table of Contents
1. [Phase 1: Valuation Booking & Account Creation](#phase-1)
2. [Phase 2: Seller Account Setup](#phase-2)
3. [Phase 3: Agent Actions (Context)](#phase-3)
4. [Phase 4: Terms & Conditions Signing](#phase-4)
5. [Phase 5: AML Document Upload](#phase-5)
6. [Phase 6: Post-AML Actions](#phase-6)
7. [Phase 7: Property Listing & Marketing](#phase-7)
8. [Phase 8: Offer Management](#phase-8)
9. [Phase 9: Viewings Management](#phase-9)
10. [Phase 10: Homecheck & Room Upload](#phase-10)
11. [Phase 11: Sales Progression](#phase-11)
12. [Phase 12: Error Cases & Edge Cases](#phase-12)
13. [Phase 13: Security & Permissions](#phase-13)
14. [Phase 14: Email Notifications](#phase-14)
15. [Phase 15: UI/UX & Visual Checks](#phase-15)
16. [Testing Checklist](#testing-checklist)
17. [Quick Reference URLs](#quick-reference-urls)

---

## Phase 1: Valuation Booking & Account Creation {#phase-1}

### Test Case 1.1: Book Valuation (New Seller)
**URL:** `/valuation/booking`

**Preconditions:**
- User is not logged in
- Email address is new (not in system)

**Test Steps:**
1. Navigate to `/valuation/booking`
2. Fill in the valuation booking form:
   - Name: "John Seller"
   - Email: "john.seller@test.com"
   - Phone: "07123456789"
   - Select role: **Seller** (or Both)
   - Property Address: "123 Test Street"
   - Postcode: "SW1A 1AA"
   - Preferred Date: Future date
   - Preferred Time: Valid time slot
   - Notes: "Test valuation booking" (optional)
3. **⚠️ Verify:** Yellow warning box is visible with ID reminder
4. Submit the form

**Expected Results:**
- ✅ Success page/confirmation message displayed
- ✅ Login credentials email received in inbox
- ✅ Email contains:
  - Login credentials (email + password)
  - Link to seller dashboard
  - "What's Next?" section explaining next steps

---

### Test Case 1.2: Book Valuation (Existing Email)
**URL:** `/valuation/booking`

**Preconditions:**
- Email already exists in system

**Test Steps:**
1. Use existing email address
2. Fill form with same email but different property
3. Submit form

**Expected Results:**
- ✅ Success page/confirmation message displayed
- ✅ Login credentials email received (if applicable)
- ✅ Can login with existing account credentials

---

### Test Case 1.3: Book Valuation - Validation Errors
**Test Steps:**
1. Submit form with empty required fields
2. Submit with invalid email format
3. Submit with invalid phone format
4. Submit with past date
5. Submit with invalid postcode format

**Expected Results:**
- ✅ Validation errors displayed for each invalid field
- ✅ Form not submitted
- ✅ Error messages are clear and helpful
- ✅ Can correct errors and resubmit

---

## Phase 2: Seller Account Setup {#phase-2}

### Test Case 2.1: Receive Login Credentials Email
**Action:** Check email inbox

**Expected Email Contents:**
- ✅ Subject: "Your Abodeology Login Credentials"
- ✅ Login email address
- ✅ Generated password (clearly displayed)
- ✅ Link to seller dashboard (`/seller/dashboard`)
- ✅ "What's Next?" section explaining:
  - How to access dashboard
  - What to expect next
  - Contact information

---

### Test Case 2.2: Login to Seller Dashboard
**URL:** `/login`

**Test Steps:**
1. Navigate to `/login`
2. Enter email from credentials email
3. Enter password from credentials email
4. Click "Login"

**Expected Results:**
- ✅ Successful login
- ✅ Redirected to `/seller/dashboard`
- ✅ No error messages
- ✅ Session created

---

### Test Case 2.3: Login with Invalid Credentials
**Test Steps:**
1. Enter correct email, wrong password
2. Enter wrong email, any password
3. Leave fields empty

**Expected Results:**
- ✅ Error message: "Invalid credentials"
- ✅ Not redirected to dashboard
- ✅ Remains on login page

---

### Test Case 2.4: View Empty Dashboard (No Properties Yet)
**URL:** `/seller/dashboard`

**Preconditions:**
- Seller logged in
- No properties exist yet

**Expected Results:**
- ✅ Dashboard loads successfully
- ✅ Shows message: "No properties yet" or similar
- ✅ Shows valuation booking information if exists
- ✅ Navigation menu visible
- ✅ No errors

---

## Phase 3: Agent Actions (Context) {#phase-3}

### Test Case 3.1: Agent Completes Valuation Form ⚙️
**Note:** This is an agent action, but seller should verify results

**What Agent Does:**
1. Agent logs into admin dashboard
2. Views valuation in "Today's Appointments"
3. Clicks "Start Valuation Form"
4. Fills in:
   - Property details (pre-filled with booking info)
   - Material information
   - Access notes
   - **⚠️ ID Visual Check confirmation** (required checkbox)
   - ID check notes
   - Pricing notes
5. Submits form

**Expected Results (Seller Perspective):**
- ✅ Property appears in seller dashboard
- ✅ Property status badge shows: "Property Details Captured" (Teal/Blue color)
- ✅ Property details visible in dashboard
- ✅ Can view property information

---

### Test Case 3.2: Agent Requests Instruction ⚙️
**What Agent Does:**
1. Agent views property in admin dashboard
2. Clicks "Request Instruction" button
3. Chooses one of:
   - **Option A:** "Sign Up Now" → Triggers immediate digital T&C form
   - **Option B:** "Sign Up Later" → Sends post-valuation email

**Expected Results:**
- ✅ Seller receives email notification
- ✅ Email contains link to instruction form
- ✅ Dashboard shows "Sign Terms & Conditions" button/prompt
- ✅ Can access instruction form from email link or dashboard

---

## Phase 4: Terms & Conditions Signing {#phase-4}

### Test Case 4.1: Access Instruction Form from Email
**URL:** `/seller/property/{id}/instruct` or `/seller/instruct/{id}`

**Test Steps:**
1. Click link from instruction request email
2. Verify form loads

**Expected Results:**
- ✅ Form loads successfully
- ✅ Pre-filled with:
  - Seller name (read-only)
  - Property address (read-only)
  - Fee percentage (read-only)
- ✅ Form fields visible:
  - Seller 1 signature (text input)
  - Seller 1 date (date picker)
   - Seller 2 details (if applicable)
  - Required declarations (checkboxes)

---

### Test Case 4.2: Access Instruction Form from Dashboard
**Test Steps:**
1. Navigate to `/seller/dashboard`
2. Click "Sign Terms & Conditions" button on property card
3. Verify form loads

**Expected Results:**
- ✅ Same as Test Case 4.1

---

### Test Case 4.3: Sign T&C Form (Single Seller)
**Test Steps:**
1. Fill in:
   - Seller 1 signature: "John Seller"
   - Seller 1 date: Today's date
   - Check all required declarations
2. Submit form

**Expected Results:**
- ✅ Form submits successfully
- ✅ **Property status badge shows "Awaiting AML" (Yellow color)** - NOT "Signed"
- ✅ Success message displayed
- ✅ Seller receives Welcome Pack email
- ✅ Redirected to property details page or dashboard
- ✅ Can see AML upload prompt in dashboard

---

### Test Case 4.4: Sign T&C Form (Two Sellers)
**Test Steps:**
1. Fill in Seller 1 details
2. Fill in Seller 2 details:
   - Seller 2 name
   - Seller 2 signature
   - Seller 2 date
3. Check all required declarations
4. Submit form

**Expected Results:**
- ✅ Both seller details saved
- ✅ Same results as Test Case 4.3

---

### Test Case 4.5: T&C Form Validation Errors
**Test Steps:**
1. Submit without signature
2. Submit without date
3. Submit without checking required declarations
4. Submit with future date

**Expected Results:**
- ✅ Validation errors displayed
- ✅ Form not submitted
- ✅ Property status remains unchanged
- ✅ No email sent

---

### Test Case 4.6: Welcome Pack Email Verification
**Action:** Check email inbox after T&C signing

**Expected Email Contents:**
- ✅ Subject: "Welcome to Abodeology - Your Property Journey Begins"
- ✅ **⚠️ Yellow warning box** about AML document upload requirement
- ✅ Clear instructions:
  - What documents needed (Photo ID + Proof of Address)
  - File format requirements (JPEG, PNG, PDF)
  - File size limit (5MB)
  - Proof of address must be dated within last 3 months
- ✅ Link to seller dashboard
- ✅ Link to AML upload page
- ✅ Next steps guidance

---

## Phase 5: AML Document Upload {#phase-5}

### Test Case 5.1: View Dashboard - AML Prompt
**URL:** `/seller/dashboard` or `/seller/properties/{id}`

**Preconditions:**
- T&C signed
- Property status: `awaiting_aml`

**Expected Results:**
- ✅ **Yellow warning box** visible with heading "⚠️ Action Required: Upload AML Documents"
- ✅ Message: "Your Terms & Conditions have been signed successfully!"
- ✅ Clear instruction: "To proceed, please upload your AML documents"
- ✅ Button: "Upload AML Documents Now"
- ✅ Property status badge shows: **"Awaiting AML"** (yellow background #ffc107)
- ✅ Warning box appears in:
  - Dashboard property card
  - Property details page

---

### Test Case 5.2: Access AML Upload Page
**URL:** `/seller/property/{id}/aml-upload`

**Test Steps:**
1. Click "Upload AML Documents Now" button
2. Verify page loads

**Expected Results:**
- ✅ AML upload form loads
- ✅ Form shows:
  - Property details
  - Two file upload fields:
    - Photo ID (required)
    - Proof of Address (required)
  - File type requirements displayed
  - File size limit displayed (5MB)
  - Submit button

---

### Test Case 5.3: Upload Valid AML Documents
**Test Steps:**
1. Upload Photo ID:
   - File type: JPEG, PNG, or PDF
   - File size: < 5MB
   - Example: passport.jpg
2. Upload Proof of Address:
   - File type: JPEG, PNG, or PDF
   - File size: < 5MB
   - Dated within last 3 months
   - Example: utility_bill.pdf
3. Submit form

**Expected Results:**
- ✅ Files uploaded successfully
- ✅ Success message: "AML documents uploaded successfully! Your documents are being reviewed..."
- ✅ **Property status badge automatically changes from "Awaiting AML" (Yellow) to "Signed" (Green)**
- ✅ Redirected to property details page
- ✅ Yellow warning box disappears
- ✅ Can see uploaded documents are saved (if viewing is available)

---

### Test Case 5.4: Upload AML - Invalid File Types
**Test Steps:**
1. Upload .docx file as Photo ID
2. Upload .txt file as Proof of Address
3. Try to submit

**Expected Results:**
- ✅ Validation error displayed
- ✅ Error message: "Photo ID must be JPEG, PNG, or PDF"
- ✅ Error message: "Proof of Address must be JPEG, PNG, or PDF"
- ✅ Form not submitted
- ✅ Property status remains `awaiting_aml`

---

### Test Case 5.5: Upload AML - Files Too Large
**Test Steps:**
1. Upload file > 5MB as Photo ID
2. Upload file > 5MB as Proof of Address
3. Try to submit

**Expected Results:**
- ✅ Validation error displayed
- ✅ Error message: "File size must be less than 5MB"
- ✅ Form not submitted
- ✅ Property status remains `awaiting_aml`

---

### Test Case 5.6: Upload AML - Missing Files
**Test Steps:**
1. Leave Photo ID empty
2. Leave Proof of Address empty
3. Try to submit

**Expected Results:**
- ✅ Validation error displayed
- ✅ Error message: "Photo ID is required"
- ✅ Error message: "Proof of Address is required"
- ✅ Form not submitted

---

### Test Case 5.7: Access AML Upload Before T&C Signing
**Preconditions:**
- Property status: `property_details_captured` (not `awaiting_aml`)

**Test Steps:**
1. Try to access `/seller/property/{id}/aml-upload` directly
2. Or try to click AML upload button (if visible)

**Expected Results:**
- ✅ Redirected with error message
- ✅ Error: "You can only upload AML documents after signing the instruction."
- ✅ Redirected to property details page

---

### Test Case 5.8: Re-upload AML Documents (Update)
**Preconditions:**
- AML documents already uploaded
- Property status: `signed`

**Test Steps:**
1. Access AML upload page
2. Upload new documents
3. Submit

**Expected Results:**
- ✅ New documents uploaded successfully
- ✅ Success message displayed
- ✅ Property status remains "Signed" (Green)
- ✅ Updated documents are saved

---

## Phase 6: Post-AML Actions {#phase-6}

### Test Case 6.1: Provide Solicitor Details
**URL:** `/seller/property/{id}/solicitor-details`

**Preconditions:**
- Property status: `signed` or later

**Test Steps:**
1. Navigate to property details page
2. Click "Provide Solicitor Details" button (if visible)
3. Fill in form:
   - Solicitor Name: "Jane Solicitor"
   - Solicitor Firm: "ABC Legal Services"
   - Solicitor Email: "jane@abclegal.com"
   - Solicitor Phone: "02012345678"
4. Submit form

**Expected Results:**
- ✅ Solicitor details saved successfully
- ✅ Success message: "Solicitor details saved successfully"
- ✅ Redirected to property details page
- ✅ Solicitor details visible in property view
- ✅ Can see all entered information displayed correctly

---

### Test Case 6.2: Update Solicitor Details
**Test Steps:**
1. Access solicitor details form (already has data)
2. Update email address
3. Submit

**Expected Results:**
- ✅ Details updated successfully
- ✅ Old data replaced with new data
- ✅ Success message displayed

---

### Test Case 6.3: Solicitor Details Validation
**Test Steps:**
1. Submit with invalid email format
2. Submit with empty required fields
3. Submit with invalid phone format

**Expected Results:**
- ✅ Validation errors displayed
- ✅ Form not submitted
- ✅ Data not saved

---

## Phase 7: Property Listing & Marketing {#phase-7}

### Test Case 7.1: View Property Details
**URL:** `/seller/properties/{id}`

**Expected Results:**
- ✅ Property information displayed:
  - Address, postcode
  - Property type, bedrooms, bathrooms
  - Status badge (correct color)
  - Material information
  - Access notes
  - Pricing information
- ✅ Action buttons based on status:
  - "Sign Terms & Conditions" (if not signed)
  - "Upload AML Documents" (if awaiting_aml)
  - "Provide Solicitor Details" (if signed)
- ✅ Next steps guidance visible
- ✅ Valuation information (if exists)

---

### Test Case 7.2: View All Properties
**URL:** `/seller/properties`

**Expected Results:**
- ✅ List of all seller's properties displayed
- ✅ Each property shows:
  - Address
  - Status badge
  - Created date
  - Link to details
- ✅ Properties ordered by most recent first
- ✅ Empty state message if no properties

---

### Test Case 7.3: Property Status Transitions
**Verify status flow:**
```
draft → property_details_captured → awaiting_aml → signed → live → sstc → sold
```

**Test Steps:**
1. Verify each status transition
2. Check status badge colors:
   - `draft` → Gray
   - `property_details_captured` → Teal/Blue
   - `awaiting_aml` → Yellow (#ffc107)
   - `signed` → Green (#28a745)
   - `live` → Blue
   - `sstc` → Orange
   - `sold` → Dark Green

**Expected Results:**
- ✅ Status badges display correct text and colors
- ✅ Status transitions happen at correct times
- ✅ UI updates reflect status changes

---

## Phase 8: Offer Management {#phase-8}

### Test Case 8.1: Receive Offer Notification
**Preconditions:**
- Property status: `live`
- Buyer has submitted offer

**Expected Results:**
- ✅ Seller receives `NewOfferNotification` email
- ✅ Email contains:
  - Offer amount
  - Buyer name
  - Property address
  - Link to view offer
- ✅ Dashboard shows offer alert/notification
- ✅ Property card shows "New Offer" badge or indicator
- ✅ Offers section in dashboard shows pending offer

---

### Test Case 8.2: View Offer Details
**URL:** `/seller/offer/{id}/decision`

**Test Steps:**
1. Click on offer notification or "View Offer" button
2. Verify offer details page loads

**Expected Results:**
- ✅ Offer details displayed:
  - Offer amount
  - Buyer information
  - Offer date
  - Buyer's conditions/notes
  - Property details
- ✅ Action buttons visible:
  - "Accept Offer"
  - "Decline Offer"
  - "Counter Offer"
- ✅ Offer status: `pending`

---

### Test Case 8.3: Accept Offer
**Test Steps:**
1. Navigate to offer decision page
2. Click "Accept Offer"
3. Optionally add notes/comments
4. Confirm acceptance

**Expected Results:**
- ✅ Offer accepted successfully
- ✅ **Property status badge changes to "SSTC" (Sold Subject to Contract) - Orange color**
- ✅ Success message displayed
- ✅ Buyer receives notification email
- ✅ Memorandum of Sale generated automatically
- ✅ Memorandum sent to solicitors via email
- ✅ Redirected to property details page
- ✅ Can view sales progression information

---

### Test Case 8.4: Decline Offer
**Test Steps:**
1. Navigate to offer decision page
2. Click "Decline Offer"
3. Optionally add notes/comments
4. Confirm decline

**Expected Results:**
- ✅ Offer declined successfully
- ✅ Property status remains unchanged (still "Live")
- ✅ Buyer receives notification email
- ✅ Success message displayed
- ✅ Property remains available for other offers

---

### Test Case 8.5: Counter Offer
**Test Steps:**
1. Navigate to offer decision page
2. Click "Counter Offer"
3. Enter counter offer amount
4. Add notes/comments
5. Submit

**Expected Results:**
- ✅ Counter offer submitted successfully
- ✅ Buyer receives notification
- ✅ Property status remains "Live"
- ✅ Success message displayed
- ✅ Counter offer visible in offer history

---

### Test Case 8.6: Multiple Offers Handling
**Preconditions:**
- Property has multiple pending offers

**Expected Results:**
- ✅ All offers visible in dashboard
- ✅ Seller can view each offer separately
- ✅ Accepting one offer should:
  - Decline or mark other offers appropriately
  - Update property status to `sstc`
- ✅ Only one offer can be accepted

---

### Test Case 8.7: Offer Already Responded To
**Preconditions:**
- Offer status is not `pending` (already accepted/declined)

**Test Steps:**
1. Try to respond to non-pending offer

**Expected Results:**
- ✅ Error message: "This offer has already been responded to."
- ✅ Cannot change decision
- ✅ Redirected back

---

### Test Case 8.8: Unauthorized Offer Access
**Test Steps:**
1. Try to access offer for property not owned by seller
2. Use direct URL with different seller's offer ID

**Expected Results:**
- ✅ Access denied
- ✅ Error message: "You do not have permission to respond to this offer."
- ✅ Redirected to dashboard

---

## Phase 9: Viewings Management {#phase-9}

### Test Case 9.1: View Upcoming Viewings
**URL:** `/seller/dashboard`

**Preconditions:**
- Property has scheduled viewings

**Expected Results:**
- ✅ "Upcoming Viewings" section visible in dashboard
- ✅ Viewings listed with:
  - Date and time
  - Buyer name
  - Property address
  - Viewing status
- ✅ Viewings ordered by date (soonest first)
- ✅ Only future viewings shown (not past)

---

### Test Case 9.2: Viewing Details
**Test Steps:**
1. Click on viewing from dashboard
2. Or navigate to viewing details page

**Expected Results:**
- ✅ Viewing information displayed:
  - Date and time
  - Buyer information
  - Property address
  - Viewing status
  - Special instructions/notes
- ✅ Viewing feedback (if available)

---

### Test Case 9.3: Viewing Status Updates
**Expected Results:**
- ✅ Viewing statuses visible:
  - `scheduled`
  - `confirmed`
  - `completed`
  - `cancelled`
- ✅ Status updates reflected in dashboard
- ✅ Cancelled viewings not shown in "Upcoming Viewings"

---

## Phase 10: Homecheck & Room Upload {#phase-10}

### Test Case 10.1: Access Homecheck Upload Page
**URL:** `/seller/property/{id}/homecheck`

**Preconditions:**
- Property exists
- Homecheck scheduled by agent

**Expected Results:**
- ✅ Homecheck upload form loads
- ✅ Form shows:
  - Property details
  - Room upload fields
  - Instructions
- ✅ Submit button visible

---

### Test Case 10.2: Upload Room Images
**Test Steps:**
1. Upload images for each room
2. Add room descriptions (if applicable)
3. Submit form

**Expected Results:**
- ✅ Images uploaded successfully
- ✅ Room data saved
- ✅ Success message displayed
- ✅ Images visible in property details
- ✅ Redirected appropriately

---

### Test Case 10.3: Homecheck Validation
**Test Steps:**
1. Submit without required images
2. Upload invalid file types
3. Upload files too large

**Expected Results:**
- ✅ Validation errors displayed
- ✅ Form not submitted
- ✅ Data not saved

---

## Phase 11: Sales Progression {#phase-11}

### Test Case 11.1: View Sales Progression (After Offer Accepted)
**Preconditions:**
- Offer accepted
- Property status: `sstc`

**Expected Results:**
- ✅ Sales progression information visible
- ✅ Memorandum of Sale accessible
- ✅ Progress tracking visible:
  - Solicitor details
  - Buyer information
  - Key dates
  - Status updates

---

### Test Case 11.2: Memorandum of Sale
**Preconditions:**
- Offer accepted

**Expected Results:**
- ✅ Memorandum generated automatically
- ✅ Memorandum contains:
  - Property details
  - Buyer and seller information
  - Offer amount
  - Solicitor details
  - Date
- ✅ Memorandum downloadable/viewable
- ✅ Memorandum sent to solicitors via email

---

## Phase 12: Error Cases & Edge Cases {#phase-12}

### Test Case 12.1: Access Non-Existent Property
**URL:** `/seller/properties/99999`

**Expected Results:**
- ✅ 404 error or "Property not found" message
- ✅ Redirected to properties list or dashboard

---

### Test Case 12.2: Access Another Seller's Property
**Test Steps:**
1. Try to access property owned by different seller
2. Use direct URL with different seller's property ID

**Expected Results:**
- ✅ Access denied
- ✅ Error message: "You do not have permission to view this property."
- ✅ Redirected to dashboard

---

### Test Case 12.3: Session Expiry
**Test Steps:**
1. Wait for session to expire
2. Try to access seller dashboard

**Expected Results:**
- ✅ Redirected to login page
- ✅ Error message: "Session expired. Please login again."
- ✅ After login, redirected back to intended page (if applicable)

---

### Test Case 12.4: Concurrent Status Updates
**Test Steps:**
1. Open two browser tabs
2. In tab 1: Sign T&C
3. In tab 2: Try to upload AML before tab 1 completes

**Expected Results:**
- ✅ System handles concurrent requests correctly
- ✅ No data corruption
- ✅ Status updates correctly
- ✅ Appropriate error messages if conflicts occur

---

### Test Case 12.5: Large File Uploads
**Test Steps:**
1. Try to upload file just under 5MB limit
2. Try to upload file exactly at 5MB limit
3. Try to upload file just over 5MB limit

**Expected Results:**
- ✅ Files under/at limit accepted
- ✅ Files over limit rejected with error
- ✅ Server handles large files without timeout

---

### Test Case 12.6: Special Characters in Forms
**Test Steps:**
1. Enter special characters in all text fields:
   - Names: "O'Brien-Smith"
   - Address: "123 St. John's Road"
   - Email: "test+tag@example.com"
   - Phone: "+44 20 1234 5678"
2. Submit forms

**Expected Results:**
- ✅ Special characters handled correctly
- ✅ Data saved and displayed correctly
- ✅ No errors or security issues
- ✅ Information appears correctly in dashboard and emails

---

### Test Case 12.7: Network Interruption During Upload
**Test Steps:**
1. Start uploading AML documents
2. Simulate network interruption
3. Retry upload

**Expected Results:**
- ✅ Partial uploads handled gracefully
- ✅ Can retry upload
- ✅ No corrupted data
- ✅ Clear error messages

---

## Phase 13: Security & Permissions {#phase-13}

### Test Case 13.1: Unauthorized Dashboard Access
**Preconditions:**
- User with role other than `seller` or `both`

**Test Steps:**
1. Try to access `/seller/dashboard` directly

**Expected Results:**
- ✅ Access denied
- ✅ Redirected to appropriate dashboard for user role
- ✅ Error message displayed

---

### Test Case 13.2: Direct URL Access Without Login
**Test Steps:**
1. Logout
2. Try to access seller routes directly:
   - `/seller/dashboard`
   - `/seller/properties/1`
   - `/seller/property/1/instruct`

**Expected Results:**
- ✅ Redirected to login page
- ✅ After login, redirected to intended page (if valid)

---

### Test Case 13.3: Form Security Protection
**Test Steps:**
1. Try to submit forms from external sources
2. Try to submit forms with invalid session

**Expected Results:**
- ✅ Form submission rejected if not from valid session
- ✅ Appropriate error message displayed
- ✅ No data saved
- ✅ System protected from unauthorized form submissions

---

### Test Case 13.4: Malicious Input Protection
**Test Steps:**
1. Enter special characters and unusual text in form fields:
   - Names with quotes: `O'Brien`
   - Text with special symbols: `Test & Co.`
   - Long text strings
2. Submit forms

**Expected Results:**
- ✅ Input handled safely
- ✅ No security issues
- ✅ Data saved correctly
- ✅ System continues to function normally

---

### Test Case 13.5: XSS Protection
**Test Steps:**
1. Enter XSS scripts in form fields:
   - `<script>alert('XSS')</script>`
   - `<img src=x onerror=alert('XSS')>`
2. Submit forms
3. View submitted data

**Expected Results:**
- ✅ Scripts handled safely
- ✅ No scripts executed in browser
- ✅ Data displayed as text only
- ✅ No security issues

---

### Test Case 13.6: File Upload Security
**Test Steps:**
1. Try to upload executable files (.exe, .php, .sh)
2. Try to upload files with malicious names
3. Try to upload files with double extensions (.jpg.php)

**Expected Results:**
- ✅ Executable files rejected
- ✅ File types validated
- ✅ File names sanitized
- ✅ Only allowed file types accepted

---

## Phase 14: Email Notifications {#phase-14}

### Test Case 14.1: Valuation Login Credentials Email
**Trigger:** After booking valuation

**Verify:**
- ✅ Email sent to correct address
- ✅ Email format correct
- ✅ Links work correctly
- ✅ Password is secure (not plain text in email body if sensitive)

---

### Test Case 14.2: Instruction Request Email
**Trigger:** Agent requests instruction

**Verify:**
- ✅ Email sent
- ✅ Contains link to instruction form
- ✅ Instructions clear
- ✅ Property details included

---

### Test Case 14.3: Welcome Pack Email
**Trigger:** After T&C signing

**Verify:**
- ✅ Email sent
- ✅ Yellow warning box about AML visible
- ✅ AML instructions clear
- ✅ Links work
- ✅ Next steps explained

---

### Test Case 14.4: New Offer Notification Email
**Trigger:** Buyer submits offer

**Verify:**
- ✅ Email sent
- ✅ Offer details included
- ✅ Link to view/respond to offer
- ✅ Property details included

---

### Test Case 14.5: Offer Decision Notification Email
**Trigger:** Seller accepts/declines/counters offer

**Verify:**
- ✅ Email sent to buyer
- ✅ Decision clearly stated
- ✅ Seller comments included (if any)
- ✅ Next steps explained

---

### Test Case 14.6: Memorandum of Sale Email
**Trigger:** Offer accepted

**Verify:**
- ✅ Email sent to seller's solicitor
- ✅ Email sent to buyer's solicitor
- ✅ Memorandum attached or linked
- ✅ All details correct

---

## Phase 15: UI/UX & Visual Checks {#phase-15}

### Test Case 15.1: Dashboard Layout
**Verify:**
- ✅ Navigation menu visible and functional
- ✅ Property cards display correctly
- ✅ Status badges visible with correct colors
- ✅ Action buttons visible and clickable
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ No layout breaks

---

### Test Case 15.2: Status Badge Colors
**Verify:**
- ✅ `draft` → Gray
- ✅ `property_details_captured` → Teal/Blue
- ✅ `awaiting_aml` → Yellow (#ffc107)
- ✅ `signed` → Green (#28a745)
- ✅ `live` → Blue
- ✅ `sstc` → Orange
- ✅ `sold` → Dark Green

---

### Test Case 15.3: Warning Boxes
**Verify:**
- ✅ Yellow warning box in valuation booking form (ID reminder)
- ✅ Yellow warning box after T&C signing (AML prompt)
- ✅ Warning boxes have correct styling
- ✅ Icons visible (⚠️)
- ✅ Text readable and clear

---

### Test Case 15.4: Form Validation Messages
**Verify:**
- ✅ Validation errors displayed clearly
- ✅ Error messages helpful and specific
- ✅ Errors appear near relevant fields
- ✅ Errors clear after correction

---

### Test Case 15.5: Success Messages
**Verify:**
- ✅ Success messages displayed after actions
- ✅ Messages clear and informative
- ✅ Messages disappear after timeout or can be dismissed
- ✅ Messages don't block UI

---

### Test Case 15.6: Loading States
**Verify:**
- ✅ Loading indicators during form submissions
- ✅ Loading indicators during file uploads
- ✅ Buttons disabled during processing
- ✅ No double submissions possible

---

### Test Case 15.7: Mobile Responsiveness
**Test on:**
- Mobile phone (320px - 768px)
- Tablet (768px - 1024px)
- Desktop (1024px+)

**Verify:**
- ✅ Forms usable on mobile
- ✅ File uploads work on mobile
- ✅ Navigation accessible
- ✅ Text readable
- ✅ Buttons clickable
- ✅ No horizontal scrolling

---

## Testing Checklist {#testing-checklist}

### ✅ Critical Path (Must Test)
- [ ] Book valuation as seller
- [ ] Receive login credentials email
- [ ] Login to seller dashboard
- [ ] View property after agent completes valuation form
- [ ] Receive instruction request email
- [ ] Sign T&C form
- [ ] **Verify status is `awaiting_aml` (NOT `signed`)**
- [ ] Receive Welcome Pack email (check AML warning)
- [ ] See AML upload prompt in dashboard
- [ ] Upload AML documents (ID + Proof of Address)
- [ ] **Verify status changes to `signed` after upload**
- [ ] Provide solicitor details
- [ ] Verify all fields saved correctly

### ✅ Offer Management
- [ ] Receive offer notification email
- [ ] View offer details
- [ ] Accept offer
- [ ] Decline offer
- [ ] Counter offer
- [ ] Verify property status changes to `sstc` after acceptance
- [ ] Verify Memorandum of Sale generated
- [ ] Verify sales progression created

### ✅ Error Handling
- [ ] Try to upload AML before signing T&C → Should show error
- [ ] Upload invalid file types → Should show validation error
- [ ] Upload files over 5MB → Should show size error
- [ ] Access AML upload page directly without proper status → Should redirect with error
- [ ] Access another seller's property → Should deny access
- [ ] Submit forms with invalid data → Should show validation errors

### ✅ Security
- [ ] Unauthorized role access → Should deny
- [ ] Direct URL access without login → Should redirect to login
- [ ] Form security protection → Should reject unauthorized submissions
- [ ] Malicious input attempts → Should be handled safely
- [ ] XSS attempts → Should be escaped
- [ ] File upload security → Should reject executables

### ✅ Email Notifications
- [ ] Valuation login credentials email
- [ ] Instruction request email
- [ ] Welcome Pack email (with AML warning)
- [ ] New offer notification email
- [ ] Offer decision notification email
- [ ] Memorandum of Sale email

### ✅ Visual Checks
- [ ] Yellow warning box visible in valuation booking form (ID reminder)
- [ ] Yellow warning box visible after T&C signing (AML prompt)
- [ ] Status badges display correctly with right colors
- [ ] Email formatting correct (Welcome Pack with AML section)
- [ ] Forms display correctly
- [ ] Mobile responsiveness

### ✅ Edge Cases
- [ ] Book valuation with existing email
- [ ] Multiple properties for same seller
- [ ] Multiple offers on same property
- [ ] Concurrent status updates
- [ ] Large file uploads (near 5MB limit)
- [ ] Special characters in forms
- [ ] Network interruption during upload

---

## Quick Reference URLs {#quick-reference-urls}

### Public Routes
```
/valuation/booking          → Book valuation
/login                      → Login page
```

### Seller Dashboard Routes
```
/seller/dashboard           → Main dashboard
/seller/properties          → List all properties
/seller/properties/{id}     → Property details
/seller/properties/create   → Create new property (if allowed)
```

### Seller Action Routes
```
/seller/property/{id}/instruct          → Sign T&C
/seller/property/{id}/aml-upload         → Upload AML documents
/seller/property/{id}/solicitor-details  → Provide solicitor details
/seller/property/{id}/homecheck         → Upload homecheck/room images
/seller/offer/{id}/decision             → View/respond to offer
```

### Fallback Routes
```
/seller/instruct            → General instruction form (fallback)
```

---

---

## Notes for Testing

1. **ID Visual Check:** The agent must check the ID visual check box in the Valuation Form during the valuation appointment.

2. **AML Timing:** AML documents are NOT collected at valuation—only visual ID check. Documents are uploaded after T&C signing via the seller dashboard.

3. **Status Progression:** The critical status change is "Awaiting AML" (Yellow) → "Signed" (Green) which happens automatically when AML documents are uploaded.

4. **Email Testing:** Check all emails for proper formatting, correct links, and clear instructions. Pay special attention to the Welcome Pack email which should include AML document requirements.

5. **Agent Actions Required:** Some testing steps require agent actions (completing valuation form, requesting instruction). Coordinate with your agent or admin team to progress through these steps.

6. **Test Data:** Use test email addresses that you can access. Consider using a dedicated test email account for testing purposes.

7. **Browser Testing:** Test on multiple browsers (Chrome, Firefox, Safari, Edge) to ensure compatibility.

8. **Device Testing:** Test on mobile phones, tablets, and desktop computers to verify responsive design.

9. **Performance:** Monitor page load times, especially for file uploads and dashboard with multiple properties. Report any slow loading times.

10. **Accessibility:** Verify that all forms are usable with keyboard navigation and that text is readable with good color contrast.

11. **Status Badge Colors:** Verify that status badges display the correct colors:
    - Draft → Gray
    - Property Details Captured → Teal/Blue
    - Awaiting AML → Yellow
    - Signed → Green
    - Live → Blue
    - SSTC → Orange
    - Sold → Dark Green

12. **File Upload Limits:** Remember that AML documents must be:
    - File types: JPEG, PNG, or PDF only
    - File size: Maximum 5MB per file
    - Proof of Address: Must be dated within last 3 months

---

**Last Updated:** 2025-01-XX
**Version:** 2.0 (Complete Testing Plan)
