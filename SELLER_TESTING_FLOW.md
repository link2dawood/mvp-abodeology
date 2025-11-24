# Seller Testing Flow - Step-by-Step Guide

## Complete Seller Journey Testing

### **Phase 1: Valuation Booking & Account Creation**

#### Step 1: Book Valuation (Public Page)
**URL:** `/valuation/booking`

**Actions:**
1. Fill in the valuation booking form:
   - Name, Email, Phone
   - Select role: **Seller** (or Both)
   - Property Address, Postcode
   - Preferred Date/Time
   - Notes (optional)
2. **⚠️ Notice:** Should see a yellow warning box reminding to bring Photo ID to valuation
3. Submit the form

**Expected Result:**
- Success page shown
- System automatically creates a user account (if new email)
- System sends login credentials email (`ValuationLoginCredentials`)
- Valuation record created with status `pending`

---

### **Phase 2: Seller Account Setup**

#### Step 2: Receive & Access Login Credentials
**Action:** Check email inbox for login credentials email

**Expected Email Contents:**
- Login credentials (email + generated password)
- Link to seller dashboard
- "What's Next?" section explaining seller dashboard access

#### Step 3: Login to Seller Dashboard
**URL:** `/login`

**Actions:**
1. Use the credentials from the email
2. Login

**Expected Result:**
- Redirected to `/seller/dashboard`
- Should see property(ies) listed (if property exists)
- Status may be `draft` or `property_details_captured` (depending on agent actions)

---

### **Phase 3: Agent Actions (For Testing Context)**

#### Step 4: Agent Completes Valuation Form ⚙️
**Note:** This is an agent action, but seller should know it's happening

**What Agent Does:**
1. Agent views valuation in admin dashboard
2. Agent clicks "Start Valuation Form"
3. Agent fills in:
   - Property details (pre-filled with booking info)
   - Material information
   - Access notes
   - **⚠️ ID Visual Check confirmation** (required checkbox)
   - ID check notes
   - Pricing notes
4. Agent submits form

**Expected Result:**
- Property created/updated with status `property_details_captured`
- Valuation status becomes `completed`

**Seller Perspective:**
- Seller can see property in dashboard
- Status shows "Property Details Captured"
- May see "Waiting for instruction request from your agent..."

---

#### Step 5: Agent Requests Instruction ⚙️
**Note:** Agent action, but triggers next step for seller

**What Agent Does:**
1. Agent views property in admin dashboard
2. Agent clicks "Request Instruction" button
3. Agent can choose:
   - **Option A:** "Sign Up Now" → Triggers immediate digital T&C form
   - **Option B:** "Sign Up Later" → Sends post-valuation email with "Instruct Abodeology" button

**Expected Result:**
- `PropertyInstruction` record created with status `pending`
- Seller receives email (`InstructionRequestNotification` or `PostValuationEmail`)

---

### **Phase 4: T&C Signing**

#### Step 6: Sign Terms & Conditions
**URL:** `/seller/instruct/{property_id}` (from email link or dashboard)

**Actions:**
1. Seller clicks link from email OR
2. Seller sees "Sign Terms & Conditions" button in property details page
3. Form should be pre-filled with:
   - Seller name
   - Property details
   - Fee percentage
4. Seller fills in:
   - Seller 1 signature (typed)
   - Seller 1 date
   - Seller 2 details (if applicable)
   - Check required declarations
5. Submit the form

**Expected Result:**
- ✅ Property status changes to **`awaiting_aml`** (NOT `signed` yet)
- ✅ `PropertyInstruction` status becomes `signed`
- ✅ Seller receives `WelcomePack` email
- ✅ Welcome Pack email contains:
   - ⚠️ **Yellow warning box** about AML document upload requirement
   - Clear instructions about what documents to upload
   - Link to seller dashboard

---

### **Phase 5: AML Document Upload**

#### Step 7: View Dashboard - AML Prompt
**URL:** `/seller/dashboard` or `/seller/properties/{id}`

**Expected Result:**
- ✅ Should see **yellow warning box** with heading "⚠️ Action Required: Upload AML Documents"
- ✅ Message: "Your Terms & Conditions have been signed successfully!"
- ✅ Clear instruction: "To proceed, please upload your AML documents"
- ✅ Button: "Upload AML Documents Now"
- ✅ Property status badge shows: **"Awaiting AML"** (yellow background)

#### Step 8: Upload AML Documents
**URL:** `/seller/aml/upload/{property_id}`

**Actions:**
1. Click "Upload AML Documents Now" button
2. Upload two files:
   - **Photo ID:** Passport, Driving License, or National ID (JPEG, PNG, PDF, max 5MB)
   - **Proof of Address:** Utility bill, Bank statement, or Council tax bill (dated within last 3 months, JPEG, PNG, PDF, max 5MB)
3. Submit form

**Expected Result:**
- ✅ Files uploaded successfully
- ✅ `AmlCheck` record created/updated with documents
- ✅ **Property status automatically changes from `awaiting_aml` to `signed`**
- ✅ Success message: "AML documents uploaded successfully! Your documents are being reviewed..."
- ✅ Redirected to property details page
- ✅ Status badge now shows **"Signed"** (green background)

---

### **Phase 6: Post-AML Actions**

#### Step 9: Provide Solicitor Details
**URL:** `/seller/solicitor/details/{property_id}`

**Expected Result:**
- ✅ Seller dashboard should show "Solicitor Details Required" prompt
- ✅ Button: "Provide Solicitor Details"

**Actions:**
1. Click "Provide Solicitor Details" button
2. Fill in:
   - Solicitor Name
   - Solicitor Firm
   - Solicitor Email
   - Solicitor Phone
3. Submit form

**Expected Result:**
- ✅ Solicitor details saved to property
- ✅ `solicitor_details_completed` flag set to `true`
- ✅ Success message shown
- ✅ Redirected to property details page

---

### **Phase 7: Ongoing Seller Dashboard Features**

#### Step 10: View Property Status
**URL:** `/seller/properties/{id}`

**Seller Should See:**
- ✅ Property details
- ✅ Current status badge
- ✅ Status-specific action buttons
- ✅ Next steps guidance

**Status Flow Summary:**
```
draft → property_details_captured → awaiting_aml → signed → (later: live, sstc, etc.)
```

#### Step 11: Receive Offers (Future Testing)
**Note:** This requires buyer actions

**When Offer is Received:**
- ✅ Seller receives `NewOfferNotification` email
- ✅ Property dashboard shows offer alert
- ✅ Seller can view offer details
- ✅ Seller can accept/decline/counter offer

**Expected Flow:**
1. Buyer submits offer
2. Seller receives notification
3. Seller views offer in dashboard
4. Seller makes decision (accept/decline/counter)
5. If accepted:
   - Property status → `sstc`
   - Memorandum of Sale generated
   - Sales progression begins

---

## **Testing Checklist**

### ✅ **Critical Path to Test:**

1. [ ] Book valuation as seller
2. [ ] Receive login credentials email
3. [ ] Login to seller dashboard
4. [ ] View property after agent completes valuation form
5. [ ] Receive instruction request email
6. [ ] Sign T&C form
7. [ ] **Verify status is `awaiting_aml` (NOT `signed`)**
8. [ ] Receive Welcome Pack email (check AML warning)
9. [ ] See AML upload prompt in dashboard
10. [ ] Upload AML documents (ID + Proof of Address)
11. [ ] **Verify status changes to `signed` after upload**
12. [ ] Provide solicitor details
13. [ ] Verify all fields saved correctly

### ✅ **Visual Checks:**

- [ ] Yellow warning box visible in valuation booking form (ID reminder)
- [ ] Yellow warning box visible after T&C signing (AML prompt)
- [ ] Status badges display correctly:
  - `property_details_captured` → Teal/Blue
  - `awaiting_aml` → Yellow (#ffc107)
  - `signed` → Green (#28a745)
- [ ] Email formatting correct (Welcome Pack with AML section)

### ✅ **Error Cases to Test:**

1. [ ] Try to upload AML before signing T&C → Should show error
2. [ ] Upload invalid file types → Should show validation error
3. [ ] Upload files over 5MB → Should show size error
4. [ ] Access AML upload page directly without proper status → Should redirect with error

---

## **Quick Testing URLs**

```
Public:
- /valuation/booking          → Book valuation
- /login                      → Login page

Seller Dashboard:
- /seller/dashboard           → Main dashboard
- /seller/properties/{id}     → Property details
- /seller/instruct/{id}       → Sign T&C
- /seller/aml/upload/{id}     → Upload AML documents
- /seller/solicitor/details/{id} → Provide solicitor details
```

---

## **Expected Database States**

### After T&C Signing:
```php
Property::status = 'awaiting_aml'
PropertyInstruction::status = 'signed'
PropertyInstruction::signed_at = [timestamp]
AmlCheck::verification_status = 'pending'
AmlCheck::id_document = null
AmlCheck::proof_of_address = null
```

### After AML Upload:
```php
Property::status = 'signed'  // ✅ Changed from awaiting_aml
AmlCheck::id_document = [file path]
AmlCheck::proof_of_address = [file path]
AmlCheck::verification_status = 'pending'  // (admin will verify later)
```

---

## **Notes for Testing**

1. **ID Visual Check:** The agent must check the ID visual check box in the Valuation Form. This is tracked in the `valuations` table.

2. **AML Timing:** AML documents are NOT collected at valuation—only visual ID check. Documents are uploaded after T&C signing.

3. **Status Progression:** The critical status change is `awaiting_aml` → `signed` which happens automatically when AML documents are uploaded.

4. **Email Testing:** Check both `InstructionRequestNotification` and `WelcomePack` emails for proper formatting and AML messaging.

5. **Agent Actions Required:** You'll need to simulate agent actions (complete valuation form, request instruction) to progress the seller flow.

---

**Good luck with testing! 🚀**

