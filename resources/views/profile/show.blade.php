@php
    $layout = 'layouts.app';
    if (in_array($user->role, ['admin', 'agent'])) {
        $layout = 'layouts.admin';
    } elseif ($user->role === 'seller' || ($user->role === 'both' && ($user->valuations()->exists() || $user->properties()->exists()))) {
        $layout = 'layouts.seller';
    } elseif ($user->role === 'buyer' || $user->role === 'both') {
        $layout = 'layouts.buyer';
    } elseif ($user->role === 'pva') {
        $layout = 'layouts.pva';
    }
@endphp
@extends($layout)

@section('title', 'My Profile')

@push('styles')
<style>
    .container {
        max-width: 800px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 28px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 20px;
        font-weight: 600;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--line-grey);
    }

    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--abodeology-teal);
    }

    .profile-info h3 {
        margin: 0 0 5px 0;
        font-size: 24px;
    }

    .profile-info p {
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
    }

    .info-value {
        color: var(--dark-text);
    }

    .btn {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 12px 20px;
        margin-bottom: 20px;
        color: #155724;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    /* ADMIN CAPABILITIES SECTION */
    .capabilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .capability-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid var(--abodeology-teal);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .capability-item:hover {
        transform: translateY(-2px);
        box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }

    .capability-item h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--abodeology-teal);
    }

    .capability-item p {
        margin: 0;
        font-size: 13px;
        color: #666;
        line-height: 1.5;
    }

    .quick-access {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }

    .quick-link {
        background: var(--white);
        border: 1px solid var(--line-grey);
        padding: 12px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--dark-text);
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-link:hover {
        background: var(--abodeology-teal);
        color: var(--white);
        border-color: var(--abodeology-teal);
    }

    .quick-link::before {
        content: "→";
        font-weight: bold;
    }

    .admin-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--abodeology-teal), #25A29F);
        color: var(--white);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 10px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .stat-box {
        background: linear-gradient(135deg, var(--abodeology-teal), #25A29F);
        color: var(--white);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-number {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .capabilities-grid {
            grid-template-columns: 1fr;
        }

        .quick-access {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>My Profile</h2>
    <p class="page-subtitle">Manage your account information and security settings.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <!-- PROFILE HEADER -->
    <div class="card">
        <div class="profile-header">
            <img src="{{ $user->avatar_url }}" alt="Profile Avatar" class="avatar">
            <div class="profile-info">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
                <p style="text-transform: capitalize;">
                    Role: {{ $user->role }}
                    @if($user->role === 'both')
                        <span style="background: linear-gradient(135deg, #FF8C00, #4CAF50); color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; margin-left: 8px; font-weight: 600;">
                            Dual-Role Client
                        </span>
                    @endif
                </p>
                @if($user->role === 'both')
                    <p style="color: #666; font-size: 13px; margin-top: 8px; font-style: italic;">
                        <strong>Dual-Role Client:</strong> You have access to both Buyer and Seller dashboards. Switch between them using the navigation menu.
                    </p>
                    <p style="color: #666; font-size: 13px; margin-top: 4px; font-style: italic;">
                        <strong>Shared AML Documents:</strong> AML documents are accessible across both roles - upload once and use for both buyer and seller activities.
                    </p>
                    <p style="color: #666; font-size: 13px; margin-top: 4px; font-style: italic;">
                        <strong>Separate Pipelines:</strong> Your buyer and seller pipelines remain permission-separated. Agents will view you as separate entries in each pipeline.
                    </p>
                @endif
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('profile.edit') }}" class="btn">Edit Profile</a>
            <a href="{{ route('profile.edit') }}#password" class="btn btn-secondary">Change Password</a>
        </div>
    </div>

    <!-- PERSONAL INFORMATION -->
    <div class="card">
        <h3>Personal Information</h3>
        <div class="info-row">
            <span class="info-label">Full Name:</span>
            <span class="info-value">{{ $user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        @if($user->phone)
        <div class="info-row">
            <span class="info-label">Phone Number:</span>
            <span class="info-value">{{ $user->phone }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Role:</span>
            <span class="info-value" style="text-transform: capitalize;">{{ $user->role }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Account Created:</span>
            <span class="info-value">{{ $user->created_at->format('F j, Y') }}</span>
        </div>
        @if($user->email_verified_at)
        <div class="info-row">
            <span class="info-label">Email Verified:</span>
            <span class="info-value" style="color: #28a745;">✓ Verified on {{ $user->email_verified_at->format('F j, Y') }}</span>
        </div>
        @else
        <div class="info-row">
            <span class="info-label">Email Verified:</span>
            <span class="info-value" style="color: #dc3545;">✗ Not verified</span>
        </div>
        @endif
    </div>

    @if($user->role === 'admin')
        <!-- ADMIN CAPABILITIES SECTION -->
        <div class="card" style="background: linear-gradient(135deg, #E8F4F3 0%, #F4F4F4 100%); border-left: 4px solid var(--abodeology-teal);">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">
                Admin Profile <span class="admin-badge">Super User</span>
            </h3>
            <p style="color: #666; margin-bottom: 20px;">The Admin profile has full system-wide access and control over the Abodeology platform.</p>

            @if($adminStats)
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['total_properties'] ?? 0 }}</div>
                        <div class="stat-label">Properties</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['total_users'] ?? 0 }}</div>
                        <div class="stat-label">Users</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['total_valuations'] ?? 0 }}</div>
                        <div class="stat-label">Valuations</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['pending_aml_checks'] ?? 0 }}</div>
                        <div class="stat-label">Pending AML</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['active_listings'] ?? 0 }}</div>
                        <div class="stat-label">Live Listings</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $adminStats['total_offers'] ?? 0 }}</div>
                        <div class="stat-label">Total Offers</div>
                    </div>
                </div>
            @endif

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Admin Capabilities</h4>
            <div class="capabilities-grid">
                <div class="capability-item">
                    <h4>Property Management</h4>
                    <p>Manage all properties, vendors, buyers, and listings. Override or edit any property data in the system.</p>
                </div>
                <div class="capability-item">
                    <h4>User Management</h4>
                    <p>Manage all users including buyers, sellers, and agents. Create, edit, or disable agent accounts.</p>
                </div>
                <div class="capability-item">
                    <h4>AML & Compliance</h4>
                    <p>Full access to AML documents and compliance tools. Review and verify all identity checks.</p>
                </div>
                <div class="capability-item">
                    <h4>Valuation Control</h4>
                    <p>Manage all valuation requests and appointments. Review and approve valuation forms.</p>
                </div>
                <div class="capability-item">
                    <h4>Reports & Analytics</h4>
                    <p>Access all reports, KPIs, and analytics. View system-wide statistics and performance metrics.</p>
                </div>
                <div class="capability-item">
                    <h4>Listing Approval</h4>
                    <p>Review and approve listings and changes before they go live on the market.</p>
                </div>
                <div class="capability-item">
                    <h4>Data Override</h4>
                    <p>Override or edit any data in the system with full administrative privileges.</p>
                </div>
                <div class="capability-item">
                    <h4>System Configuration</h4>
                    <p>Manage system settings, pricing, permissions, and configuration. Full platform control.</p>
                </div>
            </div>

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Quick Access</h4>
            <div class="quick-access">
                <a href="{{ route('admin.dashboard') }}" class="quick-link">Dashboard</a>
                <a href="{{ route('admin.properties.index') }}" class="quick-link">All Properties</a>
                <a href="{{ route('admin.users.index') }}" class="quick-link">All Users</a>
                <a href="{{ route('admin.valuations.index') }}" class="quick-link">Valuations</a>
                <a href="{{ route('admin.aml-checks.index') }}" class="quick-link">AML Checks</a>
                <a href="{{ route('admin.properties.index') }}" class="quick-link">Manage Listings</a>
            </div>
        </div>
    @elseif($user->role === 'agent')
        <!-- AGENT CAPABILITIES SECTION -->
        <div class="card" style="background: linear-gradient(135deg, #F0F8FF 0%, #F4F4F4 100%); border-left: 4px solid #4A90E2;">
            <h3 style="color: #4A90E2; margin-top: 0;">
                Agent Profile <span class="admin-badge" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">Agent</span>
            </h3>
            <p style="color: #666; margin-bottom: 20px;">The Agent profile is restricted to your assigned pipeline and responsibilities. Manage only your properties and clients.</p>

            @if($agentStats)
                <div class="stats-grid">
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['assigned_properties'] ?? 0 }}</div>
                        <div class="stat-label">My Properties</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['active_listings'] ?? 0 }}</div>
                        <div class="stat-label">Live Listings</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['completed_valuations'] ?? 0 }}</div>
                        <div class="stat-label">Valuations</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['pending_offers'] ?? 0 }}</div>
                        <div class="stat-label">Pending Offers</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['total_viewings'] ?? 0 }}</div>
                        <div class="stat-label">Viewings</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                        <div class="stat-number">{{ $agentStats['pending_aml_checks'] ?? 0 }}</div>
                        <div class="stat-label">Pending AML</div>
                    </div>
                </div>
            @endif

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Agent Capabilities</h4>
            <div class="capabilities-grid">
                <div class="capability-item" style="border-left-color: #4A90E2;">
                    <h4 style="color: #4A90E2;">Property Management</h4>
                    <p>View and manage only your assigned properties and clients. Upload valuations, photos, floorplans, and marketing data.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4A90E2;">
                    <h4 style="color: #4A90E2;">Progress Tracking</h4>
                    <p>Update progress notes, viewing logs, and offer details for your assigned properties.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4A90E2;">
                    <h4 style="color: #4A90E2;">AML Compliance</h4>
                    <p>Conduct AML checks for your own clients. Review and verify identity documents.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4A90E2;">
                    <h4 style="color: #4A90E2;">Personal KPIs</h4>
                    <p>Access your personal KPIs, activity logs, and conversion metrics for your pipeline.</p>
                </div>
                <div class="capability-item" style="border-left-color: #dc3545;">
                    <h4 style="color: #dc3545;">Restricted Access</h4>
                    <p>No access to other agents' properties or admin-level settings. Cannot change system configuration.</p>
                </div>
                <div class="capability-item" style="border-left-color: #dc3545;">
                    <h4 style="color: #dc3545;">Data Limitations</h4>
                    <p>Cannot view or edit general user data outside your assigned properties and clients.</p>
                </div>
            </div>

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Quick Access</h4>
            <div class="quick-access">
                <a href="{{ route('admin.dashboard') }}" class="quick-link">My Dashboard</a>
                <a href="{{ route('admin.properties.index') }}" class="quick-link">My Properties</a>
                <a href="{{ route('admin.valuations.index') }}" class="quick-link">Valuations</a>
                <a href="{{ route('admin.aml-checks.index') }}" class="quick-link">AML Checks</a>
            </div>
        </div>
    @elseif(in_array($user->role, ['seller', 'both']))
        <!-- SELLER PROFILE SECTION -->
        <div class="card" style="background: linear-gradient(135deg, #FFF4E6 0%, #F4F4F4 100%); border-left: 4px solid #FF8C00;">
            <h3 style="color: #FF8C00; margin-top: 0;">
                Seller Profile <span class="admin-badge" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">Seller</span>
            </h3>
            <p style="color: #666; margin-bottom: 20px;">The Seller Profile is for individuals selling a property. Manage your property listings, viewings, offers, and sale progression.</p>

            @if($sellerStats)
                <div class="stats-grid">
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['total_properties'] ?? 0 }}</div>
                        <div class="stat-label">My Properties</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['active_listings'] ?? 0 }}</div>
                        <div class="stat-label">Live Listings</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['completed_valuations'] ?? 0 }}</div>
                        <div class="stat-label">Valuations</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['pending_offers'] ?? 0 }}</div>
                        <div class="stat-label">Pending Offers</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['upcoming_viewings'] ?? 0 }}</div>
                        <div class="stat-label">Upcoming Viewings</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #FF8C00, #E67E00);">
                        <div class="stat-number">{{ $sellerStats['total_viewings'] ?? 0 }}</div>
                        <div class="stat-label">Total Viewings</div>
                    </div>
                </div>
            @endif

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Seller Capabilities</h4>
            <div class="capabilities-grid">
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Valuation & Onboarding</h4>
                    <p>Complete valuation request and seller onboarding forms. Provide property details and second seller information if applicable.</p>
                </div>
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Document Management</h4>
                    <p>Upload AML ID documents and property documents. Manage all required documentation for your property sale.</p>
                </div>
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Marketing Approval</h4>
                    <p>Approve marketing materials and listing content before your property goes live on the market.</p>
                </div>
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Viewing Management</h4>
                    <p>Track viewings, feedback, and manage viewing schedules for your properties.</p>
                </div>
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Offer Management</h4>
                    <p>Track offers and sale progression. Accept or decline offers through your dashboard.</p>
                </div>
                <div class="capability-item" style="border-left-color: #FF8C00;">
                    <h4 style="color: #FF8C00;">Sale Progression</h4>
                    <p>Monitor the progress of your property sale from listing to completion.</p>
                </div>
            </div>

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Quick Access</h4>
            <div class="quick-access">
                <a href="{{ route('seller.dashboard') }}" class="quick-link">My Dashboard</a>
                <a href="{{ route('seller.properties.index') }}" class="quick-link">My Properties</a>
                @if($sellerStats && $sellerStats['total_properties'] > 0)
                    @php
                        $firstProperty = \App\Models\Property::where('seller_id', $user->id)->first();
                    @endphp
                    @if($firstProperty)
                        <a href="{{ route('seller.aml.upload', $firstProperty->id) }}" class="quick-link">AML Documents</a>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if(in_array($user->role, ['buyer', 'both']))
        <!-- BUYER PROFILE SECTION -->
        <div class="card" style="background: linear-gradient(135deg, #E8F5E9 0%, #F4F4F4 100%); border-left: 4px solid #4CAF50;">
            <h3 style="color: #4CAF50; margin-top: 0;">
                Buyer Profile <span class="admin-badge" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">Buyer</span>
            </h3>
            <p style="color: #666; margin-bottom: 20px;">The Buyer Profile is for individuals viewing properties and making offers. Register interest, book viewings, and track your property search.</p>

            @if($buyerStats)
                <div class="stats-grid">
                    <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">
                        <div class="stat-number">{{ $buyerStats['total_offers'] ?? 0 }}</div>
                        <div class="stat-label">Total Offers</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">
                        <div class="stat-number">{{ $buyerStats['pending_offers'] ?? 0 }}</div>
                        <div class="stat-label">Pending Offers</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">
                        <div class="stat-number">{{ $buyerStats['accepted_offers'] ?? 0 }}</div>
                        <div class="stat-label">Accepted Offers</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">
                        <div class="stat-number">{{ $buyerStats['upcoming_viewings'] ?? 0 }}</div>
                        <div class="stat-label">Upcoming Viewings</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50, #388E3C);">
                        <div class="stat-number">{{ $buyerStats['total_viewings'] ?? 0 }}</div>
                        <div class="stat-label">Total Viewings</div>
                    </div>
                </div>
            @endif

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Buyer Capabilities</h4>
            <div class="capabilities-grid">
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">Property Interest</h4>
                    <p>Register interest in properties and receive notifications about new listings matching your criteria.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">Viewing Management</h4>
                    <p>Book viewings and receive confirmations. Track your viewing schedule and manage appointments.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">AML Documents</h4>
                    <p>Upload AML documents when submitting an offer. Provide identity verification for property transactions.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">Financial Documents</h4>
                    <p>Upload proof of funds and solicitor details to support your offers and demonstrate buying capability.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">Offer Management</h4>
                    <p>Submit and track offers on properties. View offer correspondence and status updates in real-time.</p>
                </div>
                <div class="capability-item" style="border-left-color: #4CAF50;">
                    <h4 style="color: #4CAF50;">Offer Tracking</h4>
                    <p>View offer correspondence and status updates. Monitor the status of your offers and receive notifications.</p>
                </div>
            </div>

            <h4 style="margin-top: 30px; margin-bottom: 15px; color: var(--dark-text);">Quick Access</h4>
            <div class="quick-access">
                <a href="{{ route('buyer.dashboard') }}" class="quick-link">My Dashboard</a>
                <a href="{{ route('buyer.profile') }}" class="quick-link">My Profile</a>
            </div>
        </div>
    @endif
</div>
@endsection

