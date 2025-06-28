@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Edit Checkpoint</h4>
    @if (
        $errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="{{ route('clients.branches.checkpoints.update', [
                'client' => $checkpoint->branch->user_id ?? $checkpoint->client_id,
                'branch' => $checkpoint->branch_id,
                'checkpoint' => $checkpoint->id,
            ]) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">Select Client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" {{ $client->id == $checkpoint->client_id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branch->id == $checkpoint->branch_id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="guard_id" class="form-label">Assign Checkpoint to <span class="text-danger">*</span></label>
                        <select class="form-select" id="guard_id" name="guard_id" required>
                            <option value="">Select Guard</option>
                            @foreach ($guards as $guard)
                                <option value="{{ $guard->id }}" {{ $guard->id == $checkpoint->user_id ? 'selected' : '' }}>{{ $guard->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="date_to_check" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_to_check" name="date_to_check" value="{{ $checkpoint->date_to_check }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="time_to_check" class="form-label">Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="time_to_check" name="time_to_check" value="{{ $checkpoint->time_to_check }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="name" class="form-label">Checkpoint Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $checkpoint->name }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Checkpoint Description</label>
                        <textarea class="form-control" id="description" name="description">{{ $checkpoint->description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" value="{{ $checkpoint->latitude }}">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" value="{{ $checkpoint->longitude }}">
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" class="form-control" id="priority" name="priority" value="{{ $checkpoint->priority }}">
                    </div>
                    <div class="col-md-6">
                        <label for="radius" class="form-label">Geofence Radius (meters)</label>
                        <input type="number" class="form-control" id="radius" name="radius" value="{{ $checkpoint->radius }}">
                    </div>
                    <div class="col-md-12">
                        <label for="nfc_tag" class="form-label">NFC Tag UID (Optional)</label>
                        <input type="text" class="form-control" id="nfc_tag" name="nfc_tag" value="{{ $checkpoint->nfc_tag }}">
                        <small class="text-muted">Leave empty to generate automatically</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $checkpoint->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">Update Checkpoint</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
