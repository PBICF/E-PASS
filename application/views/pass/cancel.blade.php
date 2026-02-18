@extends('layouts.app')

@section('title', 'Cancel Pass')

@section('content')
<div class="mt-4" x-data="CancelPass()">
    <div class="row justify-content-end">
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label class="form-label">Pass Number</label>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    x-model="passno"
                    placeholder="Enter Pass Number"
                    x-on:keydown.enter.prevent="inquire"
                    x-on:keydown.tab.prevent="inquire" />
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <template x-if="pass">
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Pass No</th>
                                            <th>Emp. No</th>
                                            <th>Name</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Issue Date</th>
                                            <th>Valid To</th>
                                            <th>Passenger</th>
                                            <th style="width: 130px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td x-text="val('PASSNO')"></td>
                                            <td x-text="val('ENO')"></td>
                                            <td x-text="val('ENAME')"></td>
                                            <td x-text="val('FRSTN')"></td>
                                            <td x-text="val('TOSTN')"></td>
                                            <td x-text="val('TDATE')"></td>
                                            <td x-text="val('VALIDTO')"></td>
                                            <td>
                                                <div x-text="val('DEPEND1')"></div>
                                                <div x-text="val('DEPEND2')"></div>
                                            </td>
                                            <td class="text-center">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger"
                                                    x-on:click="openCancelModal()"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cancelPassModal">
                                                    Cancel
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <template x-if="!pass">
                        <div class="text-center py-4 text-muted">No Data Available</div>
                    </template>
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

    <div class="modal fade" id="cancelPassModal" tabindex="-1" aria-labelledby="cancelPassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ site_url('pass/cancel/submit') }}" x-on:submit.prevent="cancelTicket">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelPassModalLabel">Cancel Pass</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="passno" x-model="cancelForm.passno">
                        <div class="mb-0">
                            <label class="form-label">Cancel Reason</label>
                            <textarea
                                class="form-control"
                                name="reason"
                                rows="3"
                                x-model="cancelForm.reason"
                                placeholder="Enter cancel reason"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function CancelPass() {
        return {
            passno: '',
            pass: null,
            cancelForm: {
                passno: '',
                reason: ''
            },
            val(key) {
                if (!this.pass) return '';
                return this.pass[key] ?? this.pass[key.toLowerCase()] ?? '';
            },
            clear() {
                this.passno = '';
                this.pass = null;
                this.cancelForm.passno = '';
                this.cancelForm.reason = '';
            },
            inquire() {
                if (!this.passno || this.passno.trim() === '') return;

                fetch(`{{ site_url('api/pass') }}/${this.passno.trim()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(data => {
                    this.pass = data.pass || null;
                    if (!this.pass) {
                        swalAlert({
                            icon: 'error',
                            title: 'Error',
                            showCancelButton: true,
                            cancelButtonText: 'Close',
                            message: 'Pass not found',
                        });
                    }
                })
                .catch(err => {
                    this.pass = null;
                    swalAlert({
                        icon: 'error',
                        title: 'Error',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        message: err.error || 'Failed to fetch pass details',
                    });
                });
            },
            openCancelModal() {
                this.cancelForm.passno = this.val('PASSNO');
                this.cancelForm.reason = '';
            },

            async cancelTicket($event) {
                let result = await swalAlert({
                    icon: 'warning',
                    title: 'Warning!',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, cancel',
                    message: 'Are you sure? You Want Cancel this PASS?',
                });

                if (result.isConfirmed) {
                    $event.target.submit();
                };
            }
        }
    }
</script>
@endsection
