@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h4 class="mb-4">QR Code for Checkpoint: <strong>{{ $checkpoint->name }}</strong></h4>
    <div class="d-flex justify-content-center mb-4">
        {!! $qr !!}
    </div>
    <p class="lead">Scan this code at the checkpoint.</p>
    <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
