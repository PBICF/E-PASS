@extends('layouts.app')

@section('title', 'EDIT FAMILY RECORDS')

@section('content')
    <div class="mt-4" x-data="Family()">
        <form method="post" action="{{ site_url('pass/family/update') }}" x-on:submit.prevent="null">
            <div class="row justify-content-end">
                <div class="col-md-2">
                    <div class="form-group mb-3">
                        <label class="form-label">Employee No.</label>
                        <input type="text" name="EMPNO" class="form-control form-control-sm" x-mask="999999" required
                            x-model="empno" placeholder="Enter Employee No." x-on:keydown.enter.prevent="inquire()"
                            x-on:keydown.tab.prevent="inquire()" />
                        {{-- stores the last successfully-inquired empno --}}
                        <input type="hidden" name="LAST_EMPNO" x-model="lastEmpno">
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
                                                <tr :data-member="index + 1" class="family-row"
                                                    :id="'family-row-' + (index + 1)">

                                                    <!-- SL NO -->
                                                    <td x-text="index + 1"></td>

                                                    <!-- NAME -->
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
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
                                                        <input type="text" class="form-control form-control-sm"
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
                                                        <button type="button" class="btn btn-sm btn-gradient"
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
                    <button type="button" class="btn btn-success" x-show="loaded" data-bs-toggle="modal"
                        data-bs-target="#addMemberModal" x-on:click="resetNewMember">
                        + Add New Member
                    </button>
                </div>
            </div>
        </form>

        {{-- ============================================================
        Add New Family Member Modal
        (inside same x-data="Family()" root so it shares empno/newMember)
        ============================================================ --}}
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">Add New Family Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" x-model="newMember.name"
                                placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" x-model="newMember.frelation">
                                <option value="">-- Select --</option>
                                @foreach ($relationships as $relationship)
                                    <option value="{{ $relationship['RELCODE'] }}">{{ $relationship['RELNAME'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="text" class="form-control form-control-sm"
                                x-datepicker="{ onSelect: (val) => newMember.db = val }" x-model="newMember.db"
                                placeholder="DD/MM/YYYY">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Allowed? <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" x-model="newMember.fallowed">
                                <option value="Y">Y</option>
                                <option value="N">N</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" x-on:click="addFamily">
                            Save Member
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function Family() {
            return {
                empno: null,
                lastEmpno: null,
                family: [],
                loaded: false,
                newMember: { name: '', frelation: '', db: '', fallowed: 'Y' },

                resetNewMember() {
                    this.newMember = { name: '', frelation: '', db: '', fallowed: 'Y' };
                },

                clear() {
                    this.empno = null;
                    this.lastEmpno = null;
                    this.family = [];
                    this.loaded = false;
                },

                inquire(empno = null) {
                    if (empno instanceof Event) empno = null;
                    const target = empno ?? this.empno;
                    if (!target || target.toString().trim() == '') return;
                    fetch("{{ site_url('api/employees/inquire') }}", {
                        method: 'POST',
                        body: new URLSearchParams({ empno: target })
                    })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw data;
                            return data;
                        })
                        .then(data => {
                            this.family = data.family || [];
                            this.lastEmpno = this.empno;   // lock in the inquired empno
                            this.loaded = true;
                        })
                        .catch(err => {
                            this.family = [];
                            this.loaded = false;
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

                updateFamily({ empno, fslno, frelation, name, db, fallowed }) {
                    fetch("{{ site_url('api/family/update') }}", {
                        method: 'POST',
                        body: new URLSearchParams({ empno, fslno, frelation, name, db, fallowed })
                    })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw data;
                            return data;
                        })
                        .then(data => {
                            if (data.code == 200) {
                                swalAlert({
                                    icon: 'success',
                                    title: 'Successfully Updated!',
                                    showCancelButton: true,
                                    cancelButtonText: 'Close',
                                    message: data.success,
                                });
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
                },

                addFamily() {
                    if (!this.lastEmpno) {
                        swalAlert({ icon: 'error', title: 'Error', message: 'Please search for an employee first.' });
                        return;
                    }

                    const { name, frelation, db, fallowed } = this.newMember;

                    if (!name || !frelation) {
                        swalAlert({ icon: 'warning', title: 'Validation', message: 'Name and Relationship are required.' });
                        return;
                    }

                    fetch("{{ site_url('api/family/add') }}", {
                        method: 'POST',
                        body: new URLSearchParams({ empno: this.lastEmpno, name, frelation, db, fallowed })
                    })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw data;
                            return data;
                        })
                        .then(data => {
                            if (data.code == 200) {
                                bootstrap.Modal.getInstance(
                                    document.getElementById('addMemberModal')
                                )?.hide();

                                this.inquire();

                                swalAlert({
                                    icon: 'success',
                                    title: 'Member Added!',
                                    showCancelButton: true,
                                    cancelButtonText: 'Close',
                                    message: data.success,
                                });

                                this.resetNewMember();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            swalAlert({
                                icon: 'error',
                                title: 'Error',
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                message: err.error || 'Failed to add member!',
                            });
                        });
                },
            }
        }
    </script>
@endsection