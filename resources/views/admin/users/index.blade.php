@extends('layouts.admin')

@section('title', 'Users')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px;
        text-align: left;
        font-size: 14px;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:hover {
        background: #f9f9f9;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .role-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .role-admin {
        background: #E14F4F;
        color: #FFF;
    }

    .role-agent {
        background: #6c757d;
        color: #FFF;
    }

    .role-buyer {
        background: var(--abodeology-teal);
        color: #FFF;
    }

    .role-seller {
        background: #28a745;
        color: #FFF;
    }

    .role-both {
        background: #F4C542;
        color: #000;
    }

    .role-pva {
        background: #17a2b8;
        color: #FFF;
    }

    .role-null {
        background: #e0e0e0;
        color: #666;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    .filter-card {
        background: var(--white);
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        padding: 10px 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--abodeology-teal);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .btn-reset {
        background: #6c757d;
        color: var(--white);
    }

    .btn-reset:hover {
        background: #5a6268;
    }

    .users-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-header-actions {
        flex-shrink: 0;
    }

    .add-user-btn {
        white-space: nowrap;
    }

    .loading-indicator {
        display: none;
        text-align: center;
        padding: 20px;
        color: var(--abodeology-teal);
        font-weight: 600;
    }

    .loading-indicator.active {
        display: block;
    }

    .users-table-container {
        position: relative;
    }

    .users-desktop {
        display: block;
    }

    .users-mobile {
        display: none;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .container {
            padding: 0 12px;
        }

        .users-page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        h2 {
            font-size: 24px;
        }

        .card, .filter-card {
            padding: 20px;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .filter-actions .btn {
            width: 100%;
            text-align: center;
        }

        .users-desktop {
            display: none;
        }

        .users-mobile {
            display: block;
        }

        .user-mobile-card {
            border: 1px solid var(--line-grey);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.04);
        }

        .user-mobile-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
        }

        .user-mobile-name {
            font-size: 15px;
            font-weight: 700;
            word-break: break-word;
        }

        .user-mobile-id {
            color: #6b7280;
            font-size: 12px;
            white-space: nowrap;
        }

        .user-mobile-email {
            color: #374151;
            font-size: 13px;
            margin-bottom: 8px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .user-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0;
            border-top: 1px solid #f2f2f2;
        }

        .user-mobile-label {
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            flex: 0 0 38%;
        }

        .user-mobile-value {
            color: #1f2937;
            font-size: 13px;
            text-align: right;
            flex: 1;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th,
        .table td {
            padding: 8px;
            font-size: 13px;
        }

        .role-badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .page-subtitle {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 0 10px;
        }

        h2 {
            font-size: 20px;
        }

        .card {
            padding: 15px;
        }

        .table th,
        .table td {
            padding: 6px;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header users-page-header">
        <div class="page-header-left">
            <h2>Users</h2>
            <p class="page-subtitle">View and manage all platform users (Admin Only)</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Add New User - before filters -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.users.create') }}" style="display: inline-block; padding: 12px 24px; background: #2CB8B4; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">+ Add New User</a>
    </div>

    <!-- SEARCH FILTERS -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
            <div class="form-group">
                <label for="search">Search (Name, Email, Phone)</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search users...">
            </div>

            <div class="form-group">
                <label for="role">Filter by Role</label>
                <select name="role" id="role">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                    <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                    <option value="both" {{ request('role') == 'both' ? 'selected' : '' }}>Both (Buyer & Seller)</option>
                    <option value="pva" {{ request('role') == 'pva' ? 'selected' : '' }}>PVA</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date_from">Registered From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label for="date_to">Registered To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-main" id="applyFiltersBtn">Apply Filters</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-reset">Reset</a>
            </div>
        </form>
    </div>

    <div class="card users-table-container">
        <div class="loading-indicator" id="loadingIndicator">Loading...</div>
        <div id="usersTableContent">
            <div class="users-desktop">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="role-badge role-{{ $user->role ?? 'null' }}">
                                        {{ $user->role ? ucfirst($user->role) : 'No Role' }}
                                    </span>
                                    @if($user->role === 'both')
                                        <span style="font-size: 11px; color: #666; display: block; margin-top: 4px;">
                                            (Appears in both Buyer & Seller pipelines)
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 40px;">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="users-mobile">
                @forelse($users as $user)
                    <div class="user-mobile-card">
                        <div class="user-mobile-top">
                            <div class="user-mobile-name">{{ $user->name }}</div>
                            <div class="user-mobile-id">#{{ $user->id }}</div>
                        </div>
                        <div class="user-mobile-email">{{ $user->email }}</div>

                        <div class="user-mobile-row">
                            <div class="user-mobile-label">Phone</div>
                            <div class="user-mobile-value">{{ $user->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="user-mobile-row">
                            <div class="user-mobile-label">Role</div>
                            <div class="user-mobile-value">
                                <span class="role-badge role-{{ $user->role ?? 'null' }}">
                                    {{ $user->role ? ucfirst($user->role) : 'No Role' }}
                                </span>
                                @if($user->role === 'both')
                                    <div style="font-size: 11px; color: #666; margin-top: 4px;">
                                        (Buyer & Seller)
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="user-mobile-row">
                            <div class="user-mobile-label">Registered</div>
                            <div class="user-mobile-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: #999; padding: 20px 6px;">
                        No users found
                    </div>
                @endforelse
            </div>

        @if($users->hasPages())
            <div style="margin-top: 20px;" id="paginationContainer">
                {{ $users->links() }}
            </div>
        @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    let searchTimeout;
    const searchInput = document.getElementById('search');
    const roleSelect = document.getElementById('role');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const usersTableContent = document.getElementById('usersTableContent');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function to perform live search
    function performLiveSearch() {
        const search = searchInput.value;
        const role = roleSelect.value;
        const dateFrom = dateFromInput.value;
        const dateTo = dateToInput.value;

        // Show loading indicator
        loadingIndicator.classList.add('active');
        usersTableContent.style.opacity = '0.5';

        // Build query string
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (role) params.append('role', role);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        // Make AJAX request
        fetch(`{{ route('admin.users.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary container to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract the table content from the response
            const responseTableContent = tempDiv.querySelector('#usersTableContent');
            const responsePagination = tempDiv.querySelector('#paginationContainer');
            
            if (responseTableContent) {
                usersTableContent.innerHTML = responseTableContent.innerHTML;
            } else {
                // Fallback: try to find table in the response
                const responseTable = tempDiv.querySelector('.users-table-container .table');
                if (responseTable) {
                    usersTableContent.innerHTML = responseTable.outerHTML;
                }
            }
            
            // Update pagination
            const currentPagination = document.getElementById('paginationContainer');
            if (responsePagination) {
                if (currentPagination) {
                    currentPagination.innerHTML = responsePagination.innerHTML;
                } else {
                    const paginationDiv = document.createElement('div');
                    paginationDiv.id = 'paginationContainer';
                    paginationDiv.style.marginTop = '20px';
                    paginationDiv.innerHTML = responsePagination.innerHTML;
                    usersTableContent.appendChild(paginationDiv);
                }
            } else if (currentPagination) {
                currentPagination.remove();
            }

            // Update URL without page reload
            const newUrl = `{{ route('admin.users.index') }}?${params.toString()}`;
            window.history.pushState({path: newUrl}, '', newUrl);
        })
        .catch(error => {
            // Log error silently in production
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                console.error('Search error:', error);
            }
        })
        .finally(() => {
            loadingIndicator.classList.remove('active');
            usersTableContent.style.opacity = '1';
        });
    }

    // Debounced search function (500ms delay)
    const debouncedSearch = debounce(performLiveSearch, 500);

    // Event listeners for live search
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            debouncedSearch();
        });
    }

    // Event listeners for filters (immediate search on change)
    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            performLiveSearch();
        });
    }

    if (dateFromInput) {
        dateFromInput.addEventListener('change', () => {
            performLiveSearch();
        });
    }

    if (dateToInput) {
        dateToInput.addEventListener('change', () => {
            performLiveSearch();
        });
    }

    // Prevent form submission on Enter key in search field
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performLiveSearch();
            }
        });
    }

    // Hide apply button since we're doing live search
    if (applyFiltersBtn) {
        applyFiltersBtn.style.display = 'none';
    }
})();
</script>
@endpush
@endsection

