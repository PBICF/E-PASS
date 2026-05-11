@extends('layouts.app')

@section('title', 'Auth Login')

@section('content')

<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-4 mt-5">
            <div class="card">
                <div class="card-body p-4">

                    <h4 class="text-center mb-4">Login</h4>

                    <form method="post" action="{{ base_url('auth/process') }}">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection