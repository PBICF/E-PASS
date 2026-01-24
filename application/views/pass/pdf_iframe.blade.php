@extends('layouts.app')

@section('title', 'Pass Preview')

@section('content')
<div class="row my-4">
    <div class="card emp-card shadow-sm">
        <div class="card-body p-3">
            <h6 class="mb-3">Pass Preview</h6>
            <div class="ratio" style="width:420px; height:300px;">
                <iframe src="{{ site_url('pass/' . $passno . '/pdf') }}" frameborder="0" width="100%" height="100%"></iframe>
            </div>
            <div class="mt-3">
                <a class="btn btn-primary" href="{{ site_url('pass/' . $passno . '/pdf') }}" target="_blank">Open in new tab</a>
                <a class="btn btn-secondary" href="{{ site_url('print') }}">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection