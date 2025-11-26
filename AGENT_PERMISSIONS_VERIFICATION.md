# Agent Permissions & Capabilities Verification

## ✅ Agent Capabilities (What Agents CAN Do)

### 1. View and Manage Only Their Assigned Properties and Clients
- ✅ **Dashboard**: Agents have a separate dashboard (`/admin/agent/dashboard`) that only shows:
  - Their assigned properties
  - Valuations for their assigned properties
  - Offers on their assigned properties
  - Viewings for their assigned properties
  - Sales for their assigned properties
  - Today's appointments for their assigned properties

- ✅ **Property Access**: Agents can only view properties they are assigned to via `PropertyInstruction.requested_by`
  - Method: `getAgentPropertyIds($agentId)` filters by `PropertyInstruction.requested_by`
  - All property-related methods check agent access before allowing view/edit
  - Location: `app/Http/Controllers/AdminController.php`

- ✅ **Client Access**: Agents can only see clients (sellers/buyers) related to their assigned properties
  - They cannot access the general user list (`/admin/users`)
  - They can view client information through property relationships

### 2. Upload Valuations, Photos, Floorplans, and Marketing Data
- ✅ **Valuation Forms**: Agents can complete valuation forms for their assigned properties
  - Route: `/admin/valuations/{id}/valuation-form`
  - Includes: Property details, material information, pricing notes, ID visual check
  - Location: `app/Http/Controllers/AdminController.php::storeValuationForm()`

- ✅ **Property Photos**: Agents can upload photos for their assigned properties
  - Route: `/admin/properties/{id}/listing-upload`
  - Includes: Multiple photos, floorplan, EPC
  - Location: `app/Http/Controllers/AdminController.php::storeListingUpload()`

- ✅ **HomeCheck Images**: Agents can upload 360° images and photos for HomeCheck
  - Route: `/admin/properties/{id}/complete-homecheck`
  - Includes: Room-by-room uploads, 360° images, regular photos
  - Location: `app/Http/Controllers/AdminController.php::storeCompleteHomeCheck()`

### 3. Update Progress Notes, Viewing Logs, and Offer Details
- ✅ **Progress Notes**: Agents can add notes through:
  - Valuation form (`agent_notes` field)
  - Property show page (through various forms)
  - Location: `resources/views/admin/valuations/valuation-form.blade.php`

- ✅ **Viewing Logs**: Agents can view and manage viewings for their assigned properties
  - Viewings are filtered by agent's assigned properties
  - Agents can see viewing feedback and status
  - Location: Agent dashboard and property show pages

- ✅ **Offer Details**: Agents can view and manage offers for their assigned properties
  - Offers are filtered by agent's assigned properties
  - Agents can see offer status, amounts, and buyer information
  - Location: Agent dashboard and property show pages

### 4. Conduct AML Checks for Their Own Clients
- ✅ **AML Checks**: Agents can view and verify AML checks for their assigned clients
  - Route: `/admin/aml-checks/{id}`
  - Filtered to only show AML checks for sellers of their assigned properties
  - Agents can verify/reject AML checks
  - Location: `app/Http/Controllers/AdminController.php::showAmlCheck()`, `verifyAmlCheck()`

### 5. Access Personal KPIs, Activity Logs, and Conversion Metrics
- ✅ **Agent Dashboard KPIs**: Shows:
  - My Properties count
  - Live Listings count
  - Pending Valuations count
  - Pending Offers count
  - Upcoming Viewings count
  - Sales Progressing count
  - Location: `resources/views/admin/agent-dashboard.blade.php`

- ✅ **Activity Logs**: Agents can see:
  - Recent valuations
  - Recent offers
  - Upcoming viewings
  - Recent sales
  - All filtered to their assigned properties only

## ❌ Agent Restrictions (What Agents CANNOT Do)

### 1. No Access to Other Agents' Properties
- ✅ **Enforced**: All property access methods check `getAgentPropertyIds()`
- ✅ **Redirect**: If agent tries to access another agent's property, they are redirected with error message
- ✅ **Location**: All methods in `AdminController` that access properties check agent ownership

### 2. No Access to Admin-Level Settings
- ✅ **Navigation**: "Users" and "Settings" links are hidden from agents in navigation
- ✅ **Route Protection**: `/admin/users` route is protected with `role.web:admin` middleware
- ✅ **Redirect**: If agent tries to access `/admin/users`, they are redirected to agent dashboard with error message
- ✅ **Location**: 
  - `routes/web.php` (route protection)
  - `resources/views/layouts/admin.blade.php` (navigation hiding)
  - `app/Http/Controllers/AdminController.php::users()` (redirect logic)

### 3. Cannot Change System Configuration or General User Data
- ✅ **No Settings Route**: No settings page exists, and Settings link is hidden from agents
- ✅ **No User Management**: Agents cannot access user management page
- ✅ **No User Editing**: Agents cannot edit user accounts directly
- ✅ **Profile Access**: Agents can only edit their own profile (via `/profile/edit`)

### 4. Cannot Access Admin Dashboard
- ✅ **Redirect**: If agent tries to access `/admin/dashboard`, they are redirected to `/admin/agent/dashboard`
- ✅ **Location**: `app/Http/Controllers/AdminController.php::dashboard()`

## 🔒 Security Implementation Details

### Route Protection
```php
// Admin-only routes
Route::middleware(['role.web:admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
});

// Admin and Agent routes (with agent filtering)
Route::middleware(['auth', 'role.web:admin,agent'])->prefix('admin')->name('admin.')->group(function () {
    // All other routes with agent filtering in controllers
});
```

### Agent Property Filtering
```php
private function getAgentPropertyIds($agentId): array
{
    return \App\Models\PropertyInstruction::where('requested_by', $agentId)
        ->pluck('property_id')
        ->toArray();
}
```

### Access Check Pattern
```php
if ($user->role === 'agent') {
    $agentPropertyIds = $this->getAgentPropertyIds($user->id);
    if (!in_array($property->id, $agentPropertyIds)) {
        return redirect()->route('admin.properties.index')
            ->with('error', 'You do not have permission to view this property.');
    }
}
```

## 📋 Verification Checklist

- [x] Agents can only see their assigned properties
- [x] Agents can upload valuations, photos, floorplans
- [x] Agents can update progress notes
- [x] Agents can view/manage viewing logs
- [x] Agents can view/manage offer details
- [x] Agents can conduct AML checks for their clients
- [x] Agents can access personal KPIs
- [x] Agents cannot access other agents' properties
- [x] Agents cannot access admin dashboard
- [x] Agents cannot access user management
- [x] Agents cannot access system settings
- [x] Agents cannot change system configuration
- [x] Navigation links are properly hidden
- [x] Routes are properly protected
- [x] All controller methods check agent permissions

## 🎯 Summary

**Agents have full access to their assigned pipeline** (properties, clients, valuations, offers, viewings, AML checks) but **zero access to admin-level features** (user management, system settings, other agents' data, admin dashboard).

All restrictions are enforced at:
1. **Route level** (middleware)
2. **Controller level** (permission checks)
3. **View level** (conditional rendering)

The system ensures agents can perform all required tasks for their assigned properties while maintaining strict separation from admin functions and other agents' data.

