@extends('layouts.app')

@section('title', 'EDIT EMPLOYEE PROFILE')

@section('content')
<div class="mt-4" x-data="Employee()">
    <form method="post" action="{{ site_url('pass/employee/update') }}">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Employee No.</label>
                                    <input 
                                        type="text" 
                                        name="EMPNO" 
                                        class="form-control form-control-sm" 
                                        x-mask="999999" 
                                        required
                                        x-model="employee.empno"
                                        placeholder="Enter Employee No."
                                        x-on:keydown.enter.prevent="inquire" 
                                        x-on:keydown.tab.prevent="inquire" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="ENAME" class="form-control form-control-sm" x-model="employee.ename" />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="DESIG" class="form-control form-control-sm" x-model="employee.desig" />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Unit / Shop</label>
                                <input type="text" name="UNIT" class="form-control form-control-sm" x-model="employee.unit" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Cell Number</label>
                                <input type="text" name="CELLNO" class="form-control form-control-sm" x-model="employee.cellno" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date of Birth</label>
                                <input 
                                    type="text" 
                                    name="DTBIRTH" 
                                    class="form-control form-control-sm" 
                                    x-datepicker="{ onSelect: (val) => employee.dtbirth = val }"
                                    x-model="employee.dtbirth" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date of Appointment</label>
                                <input 
                                    type="text" 
                                    name="DTAPPT" 
                                    class="form-control form-control-sm" 
                                    x-datepicker="{ onSelect: (val) => employee.dtappt = val }"
                                    x-model="employee.dtappt" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date of Retirement</label>
                                <input 
                                    type="text" 
                                    name="DTRETT" 
                                    class="form-control form-control-sm" 
                                    x-datepicker="{ onSelect: (val) => employee.dtrett = val }"
                                    x-model="employee.dtrett" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Office</label>
                                <input type="text" name="OFFICE" class="form-control form-control-sm" x-model="employee.office" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pay Rate</label>
                                <input type="text" name="PAY" class="form-control form-control-sm" x-model="employee.pay" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pay Scale</label>
                                <input type="text" name="ESCALE" class="form-control form-control-sm" x-model="employee.escale" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Group</label>
                                <select class="form-select form-select-sm" name="GROUPIND" x-model="employee.groupind">
                                    <option value="1">Gazetted</option>
                                    <option value="2">Group-C</option>
                                    <option value="3">Group-D</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Class</label>
                                <select class="form-select form-select-sm" name="ECLASS" x-model="employee.eclass">
                                    @foreach ($classes as $c)
                                        <option value="{{ $c['SCODE'] }}">{{ $c['SNAME'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select form-select-sm" name="EMPTYPE" x-model="employee.emptype">
                                    @foreach ($estatus as $s)
                                        <option value="{{ $s['ETYPE'] }}">{{ $s['EDETAILS'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Remarks 1</label>
                                <textarea class="form-control form-control-sm" name="REMARKS" rows="2" x-model="employee.remarks"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks 2</label>
                                <textarea class="form-control form-control-sm" name="REMARKS2" rows="2" x-model="employee.remarks"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ID Card No</label>
                                <input type="text" class="form-control form-control-sm" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Widow Pass Eligibility</label>
                                <select class="form-select form-select-sm" name="WIDOW_IND" x-model="employee.widow_ind">
                                    <option value="1">Alternative Year</option>
                                    <option value="2">Every Year</option>
                                    <option value="3">Not Applicable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ site_url('/') }}" class="btn btn-primary">Go Back</a>
                        <button type="button" class="btn btn-dark" x-on:click="clear">Clear</button>
                        <button type="submit" class="btn btn-gradient">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function Employee() {
        return {
            employee: {
                empno: null,
                ename: '',
                desig: '',
                unit: '',
                cellno: '',
                dtbirth: '',
                dtappt: '',
                dtrett: '',
                office: '',
                pay: '',
                escale: '',
                groupind: '',
                eclass: '',
                emptype: '',
                remarks: '',
                widow_ind: '',
            },
            clear() {
                this.employee = {
                    empno: null,
                    ename: '',
                    desig: '',
                    unit: '',
                    cellno: '',
                    dtbirth: '',
                    dtappt: '',
                    dtrett: '',
                    office: '',
                    pay: '',
                    escale: '',
                    groupind: '',
                    eclass: '',
                    emptype: '',
                    remarks: '',
                    widow_ind: '',
                };
            },
            inquire() {
                let empno = this.employee.empno;
                if(empno?.trim() == '' || empno == null) return;
                fetch("{{ site_url('api/employees/inquire') }}", {
                    method: 'POST',
                    body: new URLSearchParams({ empno })
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw data; // force catch()
                    }

                    return data;
                })
                .then(data => {
                    this.employee = data.employee;
                })
                .catch(err => {
                    this.clear();

                    swalAlert({
                        icon: 'error',
                        title: 'Error',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        message: err.error || 'Something went wrong',
                    });

                    // 🔑 restore empno for retry
                    this.employee.empno = empno;
                });

            },
        }
    }
</script>
@endsection
