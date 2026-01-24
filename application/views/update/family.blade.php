@extends('layouts.app')

@section('title', 'EDIT FAMILY RECORDS')

@section('content')
<div class="mt-4" x-data="Family()">
    <form method="post" action="{{ site_url('pass/family/update') }}" x-on:submit.prevent="null">
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
            <div class="col-md-12">
                <div class="card emp-card shadow-sm">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">Sl.No</th>
                                        <th>Name</th>
                                        <th style="width:160px;">Relationship</th>
                                        <th style="width:160px;">Date of Birth</th>
                                        <th style="width:100px;" class="text-center">Allowed?</th>
                                        <th style="width:100px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="family && family.length">
                                        <template x-for="(member, index) in family" :key="member.fslno">
                                            <tr :data-member="index + 1"
                                                class="family-row"
                                                :id="'family-row-' + (index + 1)">

                                                <!-- SL NO -->
                                                <td x-text="index + 1"></td>

                                                <!-- NAME -->
                                                <td>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        x-model="member.name">
                                                </td>

                                                <!-- RELATIONSHIP -->
                                                <td>
                                                    <select class="form-select form-select-sm"
                                                            x-model="member.frelation">
                                                        <option value="">-- Select --</option>
                                                        @foreach ($relationships as $relationship)
                                                            <option value="{{ $relationship['RELCODE'] }}">
                                                                {{ $relationship['RELNAME'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <!-- DATE OF BIRTH -->
                                                <td>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        x-datepicker="{ onSelect: (val) => member.db = val }"
                                                        x-model="member.db">
                                                </td>

                                                <!-- ALLOWED -->
                                                <td class="text-center">
                                                    <select class="form-select form-select-sm"
                                                            x-model="member.fallowed">
                                                        <option value="Y">Y</option>
                                                        <option value="N">N</option>
                                                    </select>
                                                </td>

                                                <!-- ACTION -->
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-sm btn-gradient"
                                                            x-on:click="updateFamily(member)">
                                                        Update
                                                    </button>
                                                </td>

                                            </tr>
                                        </template>
                                    </template>

                                    <template x-if="!family || !family.length">
                                        <tr class="text-center">
                                            <td colspan="6">No Data Available</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ site_url('/') }}" class="btn btn-primary">Go Back</a>
                <button type="button" class="btn btn-dark" x-on:click="clear">Clear</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function Family() {
        return {
            empno: null,
            family: [],
            clear() {
                this.empno = null;
                this.family = [];
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
                    this.family = data.family || [];
                })
                .catch(err => {
                    // reset state but keep empno so user can retry
                    this.family = [];
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
            updateFamily({empno, fslno, frelation, name, db, fallowed}) {
                fetch("{{ site_url('api/family/update') }}", {
                    method: 'POST',
                    body: new URLSearchParams({empno, fslno, frelation, name, db, fallowed})
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw data;   // force catch on 4xx / 5xx
                    }

                    return data;
                })
                .then(data => {
                    if(data.code == 200) {
                        swalAlert({
                            icon: 'success',
                            title: 'Successfully Updated!',
                            showCancelButton: true,
                            cancelButtonText: 'Close',
                            message: data.success,
                        })
                    }
                })
                .catch(err => {
                    console.error(err);
                    swalAlert({
                        icon: 'error',
                        title: 'Error',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        message: err.error || 'Failed to update record!',
                    });
                });
            }
        }
    }
</script>
@endsection
