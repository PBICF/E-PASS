@extends('layouts.app')

@section('title', 'UPDATE PASS ACCOUNT')

@section('content')
<div class="mt-4" x-data="Account()">
    <form method="post" action="{{ site_url('pass/account/update') }}">
        <div class="row justify-content-end">
            <div class="col-md-2">
                <div class="form-group mb-3">
                    <label class="form-label">Employee No.</label>
                    <input 
                        type="text" 
                        name="EMPNO" 
                        class="form-control form-control-sm" 
                        x-mask="999999" 
                        required
                        x-model="empno"
                        placeholder="Enter Employee No."
                        x-on:keydown.enter.prevent="inquire" 
                        x-on:keydown.tab.prevent="inquire" />
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient text-white text-center">
                        <h5 class="mb-0">Pass Account</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <td class="text-center">Year</td>
                                    <td class="text-center">Total</td>
                                    <td class="text-center">Availed</td>
                                    <td class="text-center">As On</td>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="pass && pass.length">
                                    <template x-for="p in pass" :key="p.year">
                                        <tr class="text-center">
                                            <td>
                                                <input :name="`${p.year}[ACYEAR]`" class="form-control form-control-sm" :value="p.year" readonly />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PASS_TOTAL]`" class="form-control form-control-sm" :value="p.total" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PASS_AVAILED]`" class="form-control form-control-sm" :value="p.availed" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PASS_ASON]`" class="form-control form-control-sm" x-datepicker :value="p.ason || '-'" style="width: 120px"/>
                                            </td>
                                        </tr>
                                    </template>
                                </template>

                                <template x-if="!pass || !pass.length">
                                    <tr class="text-center">
                                        <td colspan="5">No Data Available</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient text-white text-center">
                        <h5 class="mb-0"><span style='font-family: "Times New Roman";font-weight: bold;'>II</span> Class-A A/C</h5>
                    </div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <td class="text-center">Year</td>
                                    <td class="text-center">Total</td>
                                    <td class="text-center">Availed</td>
                                    <td class="text-center">As On</td>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="second_pass && second_pass.length">
                                    <template x-for="p in second_pass" :key="p.year">
                                        <tr class="text-center">
                                            <td>
                                                <input :name="`${p.year}[ACYEAR]`" class="form-control form-control-sm" :value="p.year" readonly />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[SECONDA_TOTAL]`" class="form-control form-control-sm" :value="p.total" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[SECONDA_AVAILED]`" class="form-control form-control-sm" :value="p.availed" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[SECONDA_ASON]`" class="form-control form-control-sm" x-datepicker :value="p.ason || '-'" style="width: 120px" />
                                            </td>
                                        </tr>
                                    </template>
                                </template>

                                <template x-if="!second_pass || !second_pass.length">
                                    <tr class="text-center">
                                        <td colspan="5">No Data Available</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient text-white text-center">
                        <h5 class="mb-0">PTO Account</h5>
                    </div>

                    <div class="card-body">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <td class="text-center">Year</td>
                                    <td class="text-center">Total</td>
                                    <td class="text-center">Availed</td>
                                    <td class="text-center">As On</td>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="ptos && ptos.length">
                                    <template x-for="(p, i) in ptos" :key="p.year">
                                        <tr class="text-center">
                                            <td>
                                                <input :name="`${p.year}[ACYEAR]`" class="form-control form-control-sm" :value="p.year" readonly />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PTO_TOTAL]`" class="form-control form-control-sm" :value="p.total" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PTO_AVAILED]`" class="form-control form-control-sm" :value="p.availed" />
                                            </td>
                                            <td>
                                                <input :name="`${p.year}[PTO_ASON]`" class="form-control form-control-sm" x-datepicker :value="p.ason || '-'" style="width: 120px" />
                                            </td>
                                        </tr>
                                    </template>
                                </template>

                                <template x-if="!ptos || !ptos.length">
                                    <tr class="text-center">
                                        <td colspan="5">No Data Available</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ site_url('/') }}" class="btn btn-primary">Go Back</a>
                <button type="button" class="btn btn-dark" x-on:click="clear">Clear</button>
                <button type="submit" class="btn btn-gradient">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function Account() {
        return {
            empno: null,
            pass: [],
            ptos: [],
            second_pass: [],
            clear() {
                this.empno = null;
                this.pass = [];
                this.ptos = [];
                this.second_pass = [];
            },
            inquire() {
                if(this.empno.trim() == '' || this.empno == null) return;
                fetch("{{ site_url('api/employees/inquire') }}", {
                    method: 'POST',
                    body: new URLSearchParams({ empno: this.empno })
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw data;   // force catch on 4xx / 5xx
                    }

                    return data;
                })
                .then(data => {
                    this.pass = data.passes || [];
                    this.ptos = data.ptos || [];
                    this.second_pass = data.second_pass || [];
                })
                .catch(err => {
                    // reset state but keep empno so user can retry
                    this.pass = [];
                    this.ptos = [];
                    this.second_pass = [];

                    console.error(err);

                    swalAlert({
                        icon: 'error',
                        title: 'Error',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        message: err.error || 'Failed to fetch account data',
                    });
                });
            },
        }
    }
</script>
@endsection
