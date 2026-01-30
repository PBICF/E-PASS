@extends('layouts.app')

@section('title', 'PASS ISSUE & UPDATE')

@section('content')
<div class="row mt-5">
    <div class="col-md-3">
        <a href="{{ site_url('pass/create') }}" class="text-white text-decoration-none">
            <div class="card green-section">
                <div class="card-body">
                    <h5 class="text-center">Issue Employee Pass</h5>
                    <p class="mt-3 text-center">Create and assign a pass to an employee.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ site_url('pass/account/update') }}" class="text-white text-decoration-none">
            <div class="card blue-section">
                <div class="card-body">
                    <h5 class="text-center">Update Pass Account</h5>
                    <p class="mt-3 text-center">Modify an employee's pass account details and balances.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ site_url('pass/employee/update') }}" class="text-white text-decoration-none">
            <div class="card orange-section">
                <div class="card-body">
                    <h5 class="text-center">Update Employee Data</h5>
                    <p class="mt-3 text-center">Update employee and family contact and personal details.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ site_url('pass/family/update') }}" class="text-white text-decoration-none">
            <div class="card purple-section">
                <div class="card-body">
                    <h5 class="text-center">Update Family Records</h5>
                    <p class="mt-3 text-center">Manage family member records, benefits and eligibility statuses.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mt-3">
        <a href="{{ site_url('pass/reprint') }}" class="text-white text-decoration-none">
            <div class="card purple-section">
                <div class="card-body">
                    <h5 class="text-center">Re-Print Pass</h5>
                    <p class="mt-3 text-center">Print the pass again using the existing pass number.</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
