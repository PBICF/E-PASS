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
                        <input type="text" name="passno" class="form-control" placeholder="Pass Number">
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <hr class="flex-grow-1">
                        <span class="mx-3">OR</span>
                        <hr class="flex-grow-1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employee Number</label>
                        <input type="text" name="empno" class="form-control" placeholder="Employee Number">
                    </div>

                    <button class="btn btn-gradient w-100">Search</button>
                </form>
            </div>
        </div>
    </div>

    @if (isset($empno))
    <div class="row justify-content-center mt-4">
        <div class="col-12 col-lg-10">
            <div class="card border-1 rounded-6">
                <div class="card-header bg-white">
                    <strong>Pass List for Employee: {{ $empno }}</strong>
                </div>
                <div class="card-body p-0">
                    @if (!empty($passes))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Pass No</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Valid From</th>
                                    <th>Valid To</th>
                                    <th>Class</th>
                                    <th>Passangers</th>
                                    <th>Is Canceled</th>
                                    <th style="width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($passes as $pass)
                                <tr>
                                    <td>{{ $pass['PASSNO'] }}</td>
                                    <td>{{ $pass['FRSTN'] }}</td>
                                    <td>{{ $pass['TOSTN'] }}</td>
                                    <td>{{ $pass['PVALIDFR'] }}</td>
                                    <td>{{ $pass['PVALIDTO'] }}</td>
                                    <td>{{ $pass['PCLASS'] }}</td>
                                    <td>{{ $pass['DEPEND1'] }}{{ $pass['DEPEND2'] }}</td>
                                    <td class="text-center">
                                        @if($pass['TCANCEL'])
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-primary" href="{{ site_url('print/pass/' . $pass['PASSNO']) }}">
                                            Print
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-3 text-muted mb-0">No pass found for this employee.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
