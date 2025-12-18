@extends('layouts.admin')

@section('title', 'Edit HomeCheck Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit HomeCheck Report</h4>
                    <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Property
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Property:</strong> {{ $property->address }}
                        @if($property->postcode)
                            <br><small class="text-muted">{{ $property->postcode }}</small>
                        @endif
                    </div>

                    <form action="{{ route('admin.homechecks.update', $homecheckReport->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scheduled_date">Scheduled Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('scheduled_date') is-invalid @enderror" 
                                           id="scheduled_date" 
                                           name="scheduled_date" 
                                           value="{{ old('scheduled_date', $homecheckReport->scheduled_date ? $homecheckReport->scheduled_date->format('Y-m-d') : '') }}"
                                           min="{{ date('Y-m-d') }}">
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ old('status', $homecheckReport->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="scheduled" {{ old('status', $homecheckReport->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ old('status', $homecheckReport->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $homecheckReport->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $homecheckReport->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      maxlength="1000">{{ old('notes', $homecheckReport->notes) }}</textarea>
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($homecheckReport->scheduler)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <strong>Scheduled by:</strong> {{ $homecheckReport->scheduler->name }} 
                                    on {{ $homecheckReport->created_at->format('M d, Y g:i A') }}
                                </small>
                            </div>
                        @endif

                        @if($homecheckReport->completed_at)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <strong>Completed:</strong> 
                                    @if($homecheckReport->completer)
                                        by {{ $homecheckReport->completer->name }}
                                    @endif
                                    on {{ $homecheckReport->completed_at->format('M d, Y g:i A') }}
                                </small>
                            </div>
                        @endif

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update HomeCheck
                            </button>
                            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

