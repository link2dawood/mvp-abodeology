@extends('layouts.admin')

@section('title', 'Settings')

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

    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card-header {
        background: #000;
        color: #fff;
        padding: 15px 20px;
        margin: -25px -25px 20px -25px;
        border-radius: 12px 12px 0 0;
        font-weight: 600;
        font-size: 18px;
    }

    .card-body {
        padding: 0;
    }

    .settings-group {
        margin-bottom: 30px;
    }

    .settings-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .settings-item {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .settings-item:last-child {
        border-bottom: none;
    }

    .settings-item:hover {
        background: #f9f9f9;
    }

    .settings-key {
        font-weight: 600;
        color: #333;
        flex: 1;
    }

    .settings-value {
        color: #666;
        flex: 2;
        margin: 0 20px;
        word-break: break-word;
    }

    .settings-type {
        color: #999;
        font-size: 12px;
        text-transform: uppercase;
        margin-right: 15px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #2CB8B4;
        color: #fff;
    }

    .btn-primary:hover {
        background: #25a5a1;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-secondary {
        background: #e0e0e0;
        color: #666;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Settings</h2>
        <p class="page-subtitle">Manage application settings and configuration</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @forelse($settings as $group => $groupSettings)
        <div class="card settings-group">
            <div class="card-header">
                {{ ucfirst($group) }} Settings
                <span class="badge badge-secondary" style="margin-left: 10px;">{{ $groupSettings->count() }}</span>
            </div>
            <div class="card-body">
                <ul class="settings-list">
                    @foreach($groupSettings as $setting)
                        <li class="settings-item">
                            <div class="settings-key">
                                {{ str_replace('_', ' ', ucwords($setting->key, '_')) }}
                                @if($setting->description)
                                    <br>
                                    <small style="color: #999; font-weight: normal;">{{ $setting->description }}</small>
                                @endif
                            </div>
                            <div class="settings-value">
                                @if($setting->type === 'boolean')
                                    {{ $setting->value ? 'Yes' : 'No' }}
                                @elseif($setting->type === 'json' || $setting->type === 'array')
                                    <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-size: 12px;">JSON Data</code>
                                @elseif(strlen($setting->value) > 100)
                                    {{ substr($setting->value, 0, 100) }}...
                                @else
                                    {{ $setting->value ?? 'Not set' }}
                                @endif
                            </div>
                            <span class="settings-type">{{ $setting->type }}</span>
                            <a href="{{ route('admin.settings.edit', $group) }}" class="btn btn-primary">Edit</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <p>No settings found. Please run the seeder to populate default settings.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection


