@extends('layouts.admin')

@section('title', 'Edit Settings - ' . ucfirst($group))

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

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-label small {
        font-weight: normal;
        color: #666;
        display: block;
        margin-top: 4px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #2CB8B4;
        box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-check-label {
        margin: 0;
        cursor: pointer;
    }

    .btn {
        padding: 10px 20px;
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

    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .group-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .group-tab {
        padding: 10px 20px;
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 6px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: all 0.2s;
    }

    .group-tab:hover {
        background: #e9e9e9;
    }

    .group-tab.active {
        background: #2CB8B4;
        color: #fff;
        border-color: #2CB8B4;
    }

    .file-preview {
        margin-top: 10px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .file-preview img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 4px;
    }

    .current-value {
        color: #666;
        font-size: 12px;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Edit Settings</h2>
        <p class="page-subtitle">Update {{ ucfirst($group) }} settings</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div class="group-tabs">
        @foreach($groups as $g)
            <a href="{{ route('admin.settings.edit', $g) }}" class="group-tab {{ $g === $group ? 'active' : '' }}">
                {{ ucfirst($g) }}
            </a>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                {{ ucfirst($group) }} Settings
            </div>
            <div class="card-body">
                @forelse($settings as $setting)
                    <div class="form-group">
                        <label class="form-label">
                            {{ str_replace('_', ' ', ucwords($setting->key, '_')) }}
                            @if($setting->description)
                                <small>{{ $setting->description }}</small>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="setting_{{ $setting->key }}" 
                                    name="settings[{{ $setting->key }}]" 
                                    value="1"
                                    {{ $setting->value ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="setting_{{ $setting->key }}">
                                    Enable this setting
                                </label>
                            </div>
                        @elseif($setting->key === 'logo_url')
                            <input 
                                type="text" 
                                class="form-control" 
                                name="settings[{{ $setting->key }}]" 
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                placeholder="Enter logo URL or upload a file"
                            >
                            <input 
                                type="file" 
                                class="form-control" 
                                name="settings[logo_url_file]" 
                                accept="image/*"
                                style="margin-top: 10px;"
                            >
                            @if($setting->value)
                                <div class="file-preview">
                                    <img src="{{ $setting->value }}" alt="Current logo" onerror="this.style.display='none'">
                                    <div>
                                        <strong>Current Logo:</strong><br>
                                        <a href="{{ $setting->value }}" target="_blank" style="color: #2CB8B4; font-size: 12px;">{{ $setting->value }}</a>
                                    </div>
                                </div>
                            @endif
                        @elseif($setting->type === 'text')
                            <textarea 
                                class="form-control" 
                                name="settings[{{ $setting->key }}]"
                                placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}"
                            >{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                        @elseif($setting->type === 'number' || $setting->type === 'integer' || $setting->type === 'float')
                            <input 
                                type="number" 
                                class="form-control" 
                                name="settings[{{ $setting->key }}]" 
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                step="{{ $setting->type === 'float' ? '0.01' : '1' }}"
                            >
                        @else
                            <input 
                                type="text" 
                                class="form-control" 
                                name="settings[{{ $setting->key }}]" 
                                value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}"
                            >
                        @endif

                        @if($errors->has('settings.' . $setting->key))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">
                                {{ $errors->first('settings.' . $setting->key) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p style="color: #999; text-align: center; padding: 20px;">No settings found in this group.</p>
                @endforelse

                @if($settings->count() > 0)
                    <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection


