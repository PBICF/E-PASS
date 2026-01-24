@extends('layouts.app')

@section('title', 'ISSUE NEW PASS')

@section('content')
<form method="POST" 
    action="{{ site_url('pass/submit') }}" 
    x-data="Employee()" 
    x-init="employee.empno = '{{ old_input('empno', null) }}';inquire();" 
    x-on:submit.prevent="submit">
    <div class="row" x-show="currentStep === 1">
        <div class="col-md-8 col-sm-12">
            <div class="card emp-card shadow-sm">
                <div class="card-body p-3">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Employee No</label>
                            <input 
                                type="text" 
                                name="empno" 
                                class="form-control" 
                                x-mask="999999" 
                                required
                                x-model="employee.empno"
                                x-on:keydown.enter="inquire"
                                x-on:keydown.tab.prevent="inquire" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" x-model="employee.ename" readonly disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" x-model="employee.desig" readonly disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unit / Shop</label>
                            <input type="text" class="form-control" x-model="employee.unit" readonly disabled>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Cell Number</label>
                            <input type="text" class="form-control" x-model="employee.cellno" readonly disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="text" class="form-control datepicker" x-model="employee.dtbirth" readonly disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date of Appointment</label>
                            <input type="text" class="form-control datepicker" x-model="employee.dtappt" readonly disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date of Retirement</label>
                            <input type="text" class="form-control datepicker" x-model="employee.dtrett" readonly disabled>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Office</label>
                            <input type="text" class="form-control" x-model="employee.office" readonly disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pay Rate</label>
                            <input type="text" class="form-control" x-model="employee.pay" readonly disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pay Scale</label>
                            <input type="text" class="form-control" x-model="employee.escale" readonly disabled>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Group</label>
                            <select class="form-select" x-model="employee.groupind" readonly disabled>
                                <option value="1">Gazetted</option>
                                <option value="2">Group-C</option>
                                <option value="3">Group-D</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Class</label>
                            <select class="form-select" x-model="employee.eclass" readonly disabled>
                                @foreach ($classes as $c)
                                    <option value="{{ $c['SCODE'] }}">{{ $c['SNAME'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" x-model="employee.emptype" readonly disabled>
                                @foreach ($estatus as $s)
                                    <option value="{{ $s['ETYPE'] }}">{{ $s['EDETAILS'] }}<option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" rows="2" x-model="employee.remarks" readonly disabled></textarea>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">ID Card No</label>
                            <input type="text" class="form-control" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Widow Pass Eligibility</label>
                            <select class="form-select" x-model="employee.widow_ind" readonly disabled>
                                <option value="1">Alternative Year</option>
                                <option value="2">Every Year</option>
                                <option value="3">Not Applicable</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card emp-card shadow-sm mb-3">
                <div class="card-body p-3">
                    <h6>Pass Account</h6>
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <td colspan="2">Year</td>
                                <td>Total</td>
                                <td>Availed</td>
                                <td>Balance</td>
                                <td>As On</td>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="pass && pass.length">
                                <template x-for="p in pass" :key="p.year">
                                    <tr class="text-center">
                                        <td>
                                            <input
                                                class="form-check-input custom-check"
                                                type="checkbox"
                                                name="account_year"
                                                :checked="selection.type === 'PASS' && selection.year === p.year"
                                                x-on:change="
                                                    if ($event.target.checked) {
                                                        selection.type = 'PASS';
                                                        selection.year = p.year;
                                                    } else {
                                                        selection.type = null;
                                                        selection.year = null;
                                                    }
                                                    console.log(selection.type, selection.year);
                                                "
                                            >
                                        </td>
                                        <td>
                                            <span x-text="p.year"></span>
                                        </td>
                                        <td x-text="p.total"></td>
                                        <td x-text="p.availed"></td>
                                        <td x-text="p.total - p.availed"></td>
                                        <td x-text="p.ason || '-'"></td>
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

             <div class="card emp-card shadow-sm mb-3">
                <div class="card-body p-3">
                    <h6><span style='font-family: "Times New Roman";font-weight: bold;'>II</span> Class-A A/C</h6>
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <td colspan="2">Year</td>
                                <td>Total</td>
                                <td>Availed</td>
                                <td>Balance</td>
                                <td>As On</td>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="second_pass && second_pass.length">
                                <template x-for="p in second_pass" :key="p.year">
                                    <tr class="text-center">
                                        <td>
                                            <input
                                                class="form-check-input custom-check"
                                                type="checkbox"
                                                name="account_year"
                                                :checked="selection.type === '2AC' && selection.year === p.year"
                                                x-on:change="
                                                    if ($event.target.checked) {
                                                        selection.type = '2AC';
                                                        selection.year = p.year;
                                                    } else {
                                                        selection.type = null;
                                                        selection.year = null;
                                                    }
                                                    console.log(selection.type, selection.year);
                                                "
                                            >
                                        </td>
                                        <td>
                                            <span x-text="p.year"></span>
                                        </td>

                                        <td x-text="p.total"></td>
                                        <td x-text="p.availed"></td>
                                        <td x-text="p.total - p.availed"></td>
                                        <td x-text="p.ason || '-'"></td>
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

            <div class="card emp-card shadow-sm mb-3">
                <div class="card-body p-3">
                    <h6>PTO Account</h6>
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <td colspan="2">Year</td>
                                <td>Total</td>
                                <td>Availed</td>
                                <td>Balance</td>
                                <td>As On</td>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="ptos && ptos.length">
                                <template x-for="p in ptos" :key="p.year">
                                    <tr class="text-center">
                                        <td>
                                            <input
                                                class="form-check-input custom-check"
                                                type="checkbox"
                                                name="account_year"
                                                :checked="selection.type === 'PTO' && selection.year === p.year"
                                                x-on:change="
                                                    if ($event.target.checked) {
                                                        selection.type = 'PTO';
                                                        selection.year = p.year;
                                                    } else {
                                                        selection.type = null;
                                                        selection.year = null;
                                                    }
                                                    console.log(selection.type, selection.year);
                                                "
                                            >
                                        </td>
                                        <td>
                                            <span x-text="p.year"></span>
                                        </td>

                                        <td x-text="p.total"></td>
                                        <td x-text="p.availed"></td>
                                        <td x-text="p.total - p.availed"></td>
                                        <td x-text="p.ason || '-'"></td>
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
    <div class="row" x-show="currentStep === 2">
        <div class="col-md-12">
            <div class="card emp-card shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Family Details</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead>
                                <tr>
                                    <th style="width:60px;">Sl.No</th>
                                    <th>Name</th>
                                    <th style="width:160px;">Relationship</th>
                                    <th style="width:160px;">Date of Birth</th>
                                    <th style="width:100px;" class="text-center">Allowed?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="family && family.length">
                                    <template x-for="(member, index) in family" :key="member.fslno">
                                        <tr :data-member="index + 1" class="family-row" :id="'family-row-' + (index + 1)">
                                            <td x-text="index + 1"></td>

                                            <td class="d-flex justify-content-start gap-2">
                                                <template x-if="member.fallowed == 'Y' || member.fallowed == null">
                                                    <input
                                                        :id="`member-${member.fslno}`"
                                                        type="checkbox"
                                                        name="members[]"
                                                        class="form-check-input custom-check"
                                                        :value="String(member.fslno)"
                                                        x-model="selectedMembers"
                                                        :disabled="!(member.fallowed === 'Y' || member.fallowed === null)">
                                                </template>
                                                <label x-text="member.name" :for="`member-${member.fslno}`"></span>
                                            </td>

                                            <td>
                                                <span x-text="(() => {
                                                        const map = {
                                                            @foreach ($relationships as $relationship)
                                                                '{{ $relationship['RELCODE'] }}': '{{ $relationship['RELNAME'] }}',
                                                            @endforeach
                                                        };
                                                        return map[member.frelation] ?? '--';
                                                    })()"
                                                ></span>
                                            </td>

                                            <td>
                                                <span x-text="member.db"></span>
                                            </td>

                                            <td class="text-center" x-text="(member.fallowed == 'Y' || member.fallowed == null) ? 'Y' : 'N'"></td>
                                        </tr>
                                    </template>
                                </template>

                                <template x-if="!family || !family.length">
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
    </div>
    <div class="row" x-show="currentStep === 3">.
        <div class="col-md-12">
            <div class="card emp-card shadow-sm p-0">
                <div class="card-body p-3">
                    <div class="row mb-2 justify-content-between">
                        <div class="col-md-4">
                            <div class="page-header">
                                <h4 class="page-title mb-0">Generate Pass</h4>
                                <strong>Employee Name: <span x-text="employee.ename"></span> (<span x-text="employee.empno"></span>) </strong>
                            </div> 
                        </div>
                        <div class="col-md-3">                    
                            <div class="form-groop rounded-2 bg-gradient p-2">
                                <label class="form-label text-white fw-bold">Pass No.</label>
                                <input type="text" 
                                    @class(["form-control form-control-sm", "is-invalid" => have_error('pass_no')]) 
                                    name="pass_no" 
                                    value="{{ old_input('pass_no') }}" 
                                    @if(old_input('pass_no') === NULL)
                                    x-model="next_pass_number"
                                    @endif
                                    maxlength="8" 
                                    inputmode="numeric" 
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Type of Pass</label>
                            <select class="form-select form-select-sm" 
                                    name="pass_type" 
                                    x-model="passType"
                                    x-on:change="updateValidity">
                                <option value="">-- SELECT PASS TYPE --</option>                                
                                @foreach ($pass_types as $type)
                                    <option
                                        value="{{ $type['TCODE'] }}"
                                        @selected(old_input('pass_type') == $type['TCODE'])
                                        :disabled="selection.type == '' && [1,3].includes({{ $type['TCODE'] }})">
                                        {{ $type['TNAME'] }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Single / Return?</label>
                            <select class="form-select form-select-sm" name="single_return">
                                <option value="1" @selected(old_input('single_return') == '1')>Single</option>
                                <option value="2" @selected(old_input('single_return', '2') == '2')>Return</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Validity From</label>
                            <input type="text" class="form-control form-control-sm" name="validity_from" x-mask="99/99/9999" value="{{ old_input('validity_from') ?? date('d/m/Y') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Validity To</label>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                name="validity_to" 
                                x-mask="99/99/9999" 
                                x-model="validityTo" 
                                x-datepicker="{
                                    maxDate: '+4m',
                                    onSelect: (val) => validityTo = val 
                                }" />
                        </div>
                    </div>

                    <!-- Stations Block -->
                    <div class="row mb-2" x-data="{ enabled: false }">
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <label class="form-label">From Station (Code)</label>
                                <input 
                                    type="text" 
                                    name="from_station_code" 
                                    @class(["form-control form-control-sm text-uppercase", "is-invalid" => have_error('from_station_code')]) 
                                    maxlength="4" 
                                    autocomplete="off"
                                    x-on:change="getRoutes"
                                    x-model="from_station"
                                    tabindex="1"
                                    value="{{ old_input('from_station_code') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Station Name</label>
                                <input 
                                    type="text" 
                                    name="from_station_name" 
                                    @class(["form-control form-control-sm text-uppercase", "is-invalid" => have_error('from_station_name')]) 
                                    value="{{ old_input('from_station_name') }}" readonly />
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Station Name (Hindi)</label>
                                <input type="text" name="from_station_name_hindi" class="form-control form-control-sm hindi-font" readonly value="{{ old_input('from_station_name_hindi') }}" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-flex flex-row">Via Stations &nbsp;<span class="via_code_details text-muted"></span></label>
                                <div class="d-flex gap-2 justify-content-between">
                                    @for ( $i = 1; $i < 10; $i++ )
                                        <input 
                                            type="text" 
                                            name="via[]" 
                                            class="form-control form-control-sm text-uppercase" 
                                            autocomplete="off" 
                                            tabindex="{{ 2 + $i }}"
                                            value="{{ old_input('via')[($i-1)] ?? '' }}">                                    
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2" x-show="routes.length > 0" x-data="{ show: true }">
                            <a href="javascript:void(0)" x-on:click="show = !show" x-text="show ? 'Hide Routes' : 'Show Routes'"></a>
                            <div class="col-md-12" x-show="show == true">
                                <table class="table table-sm table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;"></th>
                                            <template x-for="i in 15" :key="'h'+i">
                                                <th x-text="i"></th>
                                            </template>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <template x-for="(route, rIndex) in routes" :key="rIndex">
                                            <tr>
                                                <!-- CHECKBOX -->
                                                <td>
                                                    <input type="radio"
                                                        class="form-check-input"
                                                        :checked="selectedRouteIndex === rIndex"
                                                        x-on:change="selectRoute(route, rIndex); show = false">
                                                </td>

                                                <!-- VIA STATIONS -->
                                                <template x-for="i in 15" :key="'c'+i">
                                                    <td x-text="route[`VIA${i}`] || ''"></td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <label class="form-label ">To Station (Code)</label>
                                <input 
                                    type="text" 
                                    name="to_station_code" 
                                    @class(["form-control form-control-sm text-uppercase", "is-invalid" => have_error('to_station_code')]) 
                                    maxlength="4"                                
                                    x-on:change="getRoutes"
                                    x-model="to_station"
                                    tabindex="2"
                                    autocomplete="off" 
                                    value="{{ old_input('to_station_code') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Name</label>
                                <input type="text" name="to_station_name" @class(["form-control form-control-sm text-uppercase", "is-invalid" => have_error('to_station_name')]) readonly value="{{ old_input('to_station_name') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Station Name (Hindi)</label>
                                <input type="text" name="to_station_name_hindi" class="form-control form-control-sm hindi-font" readonly value="{{ old_input('to_station_name_hindi') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Return Via &nbsp;<span class="return_code_details text-muted"></span></label>
                                <div class="d-flex gap-2 justify-content-between">
                                    @for ( $i = 1; $i < 10; $i++ )
                                        <input type="text" name="return_via[]" class="form-control form-control-sm text-uppercase" autocomplete="off" value="{{ old_input('return_via')[($i-1)] ?? '' }}">
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label class="form-label">Home / Foreign</label>
                            <div class="col-md-6">
                                <select class="form-select" name="home_foreign">
                                    <option value="H" @selected(old_input('home_foreign') == 'H')>Home Line</option>
                                    <option value="F" @selected(old_input('home_foreign') == 'F')>Foreign Line (Not through KRC)</option>
                                    <option value="K" @selected(old_input('home_foreign') == 'K')>Foreign Line (Through KRC)</option>
                                    <option value="A" @selected(old_input('home_foreign') == 'A')>KRC only</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-center gap-2">
                                <input type="checkbox" id="companion_required" name="is_companion" value="1" class="form-check-input custom-check" @checked(old_input('is_companion'))>
                                <label class="form-check-label" for="companion_required">Companion Required?</label>
                            </div>                        
                            
                            <div class="col-md-3 d-flex align-items-center gap-2">
                                <input type="checkbox" id="different_return_via" name="different_return_via" value="1" class="form-check-input custom-check" x-model="enabled" @checked(old_input('different_return_via')) >
                                <label class="form-check-label" for="different_return_via">Different Return via?</label>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-6 col-sm-12">
                                <label class="form-label">Break Journey (Onward) &nbsp;<span class="bj_code_details text-muted"></span></label>
                                <div class="d-flex gap-2 justify-content-between">
                                    @for( $i = 1; $i < 10; $i++ )
                                        <input type="text" name="break_journey[]" class="form-control form-control-sm text-uppercase" autocomplete="off" value="{{ old_input('break_journey')[($i-1)] ?? '' }}">
                                    @endfor
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <label class="form-label">Break Journey (Return) &nbsp;<span class="bjr_code_details text-muted"></span></label>
                                <div class="d-flex gap-2 justify-content-between">
                                    @for( $i = 1; $i < 10; $i++ )
                                        <input type="text" name="break_journey_return[]" class="form-control form-control-sm text-uppercase" autocomplete="off" value="{{ old_input('break_journey_return')[($i-1)] ?? '' }}" readonly>
                                    @endfor
                                </div>
                            </div>
                        </div>                            
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label">Remarks (1)</label>
                            <input type="text" class="form-control form-control-sm" name="remarks1" value="{{ old_input('remarks1') }}">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label">Remarks (2)</label>
                            <input type="text" class="form-control form-control-sm" name="remarks2" value="{{ old_input('remarks2') }}">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label text-danger">For office use only</label>
                            <input type="text" class="form-control form-control-sm" name="office_use_only" value="{{ old_input('office_use_only') }}">
                            <small class="text-danger">* This will not be printed in the Pass.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-2">
        <div class="d-flex gap-2 justify-content-end mt-4">
            <button type="button" class="btn btn-primary" x-on:click="history.back()">Go Back</button>
            <button type="button" class="btn btn-success" x-show="state.showInquire && currentStep == 1" x-on:click="inquire">Inquire</button>
            <button type="button" class="btn btn-dark" x-show="!state.showInquire" x-on:click="clear">Clear</button>
            <button type="button" class="btn btn-secondary" x-show="currentStep > 1" x-on:click="prevStep">Previous</button>
            <button type="submit" class="btn btn-gradient" x-text="currentStep == 3 ? 'Submit' : 'Next'"></button>
        </div>
    </div>
    <input type="hidden" name="current_tab"  x-model="currentStep">
    <input type="hidden" name="account_type" x-model="selection.type" />
    <input type="hidden" name="account_year" x-model="selection.year" />
</form>
@endsection

@section('scripts')
<script src="{{ @asset('assets/js/pass.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    const STATION = @json($stations);
    function Employee() {
        const oldMembers = @json(old_input('members'));
        return {
            routes: [],
            currentStep: {{ $current_tab }},
            validityTo: "{{ old_input('validity_to') ?? date('d/m/Y', strtotime('+4 months')) }}",
            selectedMembers: (function() {
                try {
                    if (Array.isArray(oldMembers)) return oldMembers.map(String);
                    if (typeof oldMembers === 'string' && oldMembers.trim()) return JSON.parse(oldMembers).map(String);
                } catch (e) {
                    // fallthrough to return empty
                }
                return [];
            })(),
            updateValidity() {
                const today = new Date();

                if (this.passType === '1' || this.passType === '3') {
                    today.setMonth(today.getMonth() + 4); // +4 months
                } else {
                    today.setDate(today.getDate() + 7); // +1 week
                }

                this.validityTo = formatDate(today);
            },
            submit() {
                if (this.currentStep === 1) {
                    if (!this.employee.empno) {
                        swalAlert({
                            icon: 'warning',
                            title: 'Error!',
                            showCancelButton: true,
                            message: 'Please enter Employee No and click Inquire before proceeding to Family Details.',
                        });
                        return;
                    }

                    if (!this.selection.type || !this.selection.year) {
                        swalAlert({
                            icon: 'info',
                            title: 'Proceed without PASS/PTO account?',
                            confirmButtonText: 'Okay',
                            message: 'You have not selected an account year (PASS or PTO). Do you want to continue?',
                            callback: function (result) {
                                if(result.isConfirmed === true) {
                                    $('form').trigger('submit');
                                }
                            }  
                        });
                        return;
                    } else {
                        let account = this.selection.type == 'PASS' 
                            ? this.pass.find(item => item.year === this.selection.year) 
                            : (this.selection.type == 'PTO' 
                            ? this.ptos.find(item => item.year === this.selection.year)
                            : this.second_pass.find(item => item.year === this.selection.year));

                        if(account.balance <= 0) {
                            return swalAlert({
                                icon: 'warning',
                                title: 'Error!',
                                showCancelButton: true,
                                message: 'The account does not have enough balance for the selected pass type.',
                            });                            
                        }
                    }

                    $('form').trigger('submit');
                }

                // When moving from Step 2 -> Step 3 ensure at least one family member is selected
                if (this.currentStep === 2) {
                    if (!this.selectedMembers || !this.selectedMembers.length) {
                        swalAlert({
                            icon: 'warning',
                            title: 'Error!',
                            showCancelButton: true,
                            message: 'Please select at least one family member before proceeding to Pass Details.',
                        });
                        return;
                    }

                    $('form').trigger('submit');
                }

                if (this.currentStep === 3) {
                    if($('input[name="from_station_code"]').val().trim() == '') {
                        swalAlert({
                            icon: 'error',
                            title: 'From Station code required',
                            showCancelButton: true,
                            cancelButtonText: 'Close', 
                            message: 'Please enter from station code.',
                        });

                        return;
                    }
                    
                    if($('input[name="to_station_code"]').val().trim() == '') {
                        swalAlert({
                            icon: 'error',
                            title: 'To Station code required',
                            showCancelButton: true,
                            cancelButtonText: 'Close', 
                            message: 'Please enter to station code.',
                        });

                        return;
                    }

                    var via = $('input[name="via[]"]').map(function() {
                        if(this.value !== '' ) return this.value;
                    }).get();
                    

                    if([1, 3].includes(Number(this.passType)) && (this.selection.type == '' || this.selection.year == '')) {
                        swalAlert({
                            icon: 'error',
                            title: 'Pass type required',
                            showCancelButton: true,
                            cancelButtonText: 'Close', 
                            message: 'Please select a pass type before proceeding.',
                        });

                        return;
                    }

                    
                    if(via.length <= 0) {
                        swalAlert({
                            icon: 'info',
                            title: 'Proceed without via route?',
                            confirmButtonText: 'Okay',
                            message: 'No via route has been selected. Do you want to continue?',
                            callback: function (result) {
                                if(result.isConfirmed === true) {
                                   $('form').trigger('submit');
                                }
                            }  
                        });
                    } else {
                        $('form').trigger('submit');
                    }
                }
            },
            getRoutes() {
                let from_station = this.from_station.toUpperCase();
                let to_station   = this.to_station.toUpperCase();

                if(from_station == '' || to_station == '' || from_station == to_station) {
                    return;
                }

                fetch("{{ site_url('api/routes') }}", {
                    method: 'POST',
                    body: new URLSearchParams({ frstn: from_station, tostn: to_station })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw data;
                    }

                    return data;
                })
                .then(data => {
                    if(data.code == 200) {
                        this.routes = data.routes;
                    }
                })
                .catch(err => {
                    console.error(err);
                });
            },
            prevStep() {
                if (this.currentStep > 1) this.currentStep--;
            },
            from_station: '{{ old_input('from_station_code') }}',
            to_station: '{{ old_input('to_station_code') }}',
            next_pass_number: null,
            selectedRouteIndex: null,
            passType: null,
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

            family: [],

            pass: {
                year: '',
                total: 0,
                availed: 0,
                balance: 0,
                ason: ''
            },

            ptos: {
                year: '',
                total: 0,
                availed: 0,
                balance: 0,
                ason: ''
            },

            second_pass: {
                year: '',
                total: 0,
                availed: 0,
                balance: 0,
                ason: ''
            },

            state: {
                isLoading: false,
                error: {},
                showInquire: true,
            },

            selection: {
                type: "{{ old_input('account_type') }}",
                year: "{{ old_input('account_year') }}"
            },

            inquire() {
                if(this.employee.empno.trim() == '' || this.employee.empno == null) return;
                if(this.state.isLoading == true) return;
                this.state.isLoading = true;

                fetch("{{ site_url('api/employees/inquire') }}", {
                    method: 'POST',
                    body: new URLSearchParams({ empno: this.employee.empno })
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw data;   // force catch on 4xx / 5xx
                    }

                    return data;
                })
                .then(data => {
                    this.employee = data.employee || {};

                    this.pass = data.passes || [];
                    this.ptos = data.ptos || [];
                    this.second_pass = data.second_pass || [];
                    this.family = data.family || [];
                    this.next_pass_number = data.next_pass_number || null;
                })
                .catch(err => {
                    console.error(err);

                    // preserve empno for retry
                    const empno = this.employee.empno;

                    this.employee = { empno };
                    this.pass = [];
                    this.ptos = [];
                    this.second_pass = [];
                    this.family = [];
                    this.next_pass_number = null;

                    swalAlert({
                        icon: 'error',
                        title: 'Error',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        message: err.error || 'Failed to fetch employee data',
                    });
                })
                .finally(() => {
                    this.state.isLoading = false;
                });
            },

            selectRoute(route, index = null) {
                this.selectedRouteIndex = index;

                // Collect VIA1..VIA15
                const vias = [];
                for (let i = 1; i <= 15; i++) {
                    const val = route[`VIA${i}`];
                    if (val && val.trim() !== '') {
                        vias.push(val);
                    }
                }

                const reversed = vias.reverse();

                // Fill VIA inputs (max 9 as per your form)
                const viaInputs = document.querySelectorAll('input[name="via[]"]');
                const returnViaInputs = document.querySelectorAll('input[name="return_via[]"]');
                const differentReturnCheckbox = document.getElementById('different_return_via');

                viaInputs.forEach((input, idx) => input.value = vias[idx] || '');

                if (! differentReturnCheckbox.checked) {
                    returnViaInputs.forEach((input, idx) => input.value = reversed[idx] || '');
                }
            },


            clear() {
                this.selectedRouteIndex = null,
                this.routes = [],
                this.from_station = '',
                this.to_station = '',
                this.validityTo = null;
                this.next_pass_number = null;
                this.passType = null;
                this.currentStep = 1;
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

                this.family = [];

                this.pass, this.ptos, this.second_pass = {
                    year: '',
                    total: 0,
                    availed: 0,
                    balance: 0,
                    ason: ''
                };

                this.state = {
                    isLoading: false,
                    error: {},
                };

                this.selection = {
                    type: null,
                    year: null
                };

                // Clear member selections
                this.selectedMembers = [];

                // Show Inquire button again
                this.state.showInquire = true;
            }
        };
    }
</script>
@endsection