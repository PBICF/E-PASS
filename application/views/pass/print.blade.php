@extends('layouts.app')

@section('title', 'Re-Print Pass')

@section('content')
<div class="container-md my-4" x-data="{ tab: 'employee' }">

    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-1 rounded-6 p-4">
                <form method="post" action="{{ site_url('print/pass') }}">
                    <div class="mb-3">
                        <label class="form-label">Pass Number</label>
                        <input type="text" name="passno" class="form-control" placeholder="Pass Number" required>
                    </div>

                    <button class="btn btn-gradient w-100">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection