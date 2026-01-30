<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['account_validation'] = array(
    array(
        'field' => 'empno',
        'label' => 'Employee No',
        'rules' => 'required|callback__valid_employee',
    ),
);

$config['family_validation'] = array(
    array(
        'field' => 'members',
        'label' => 'Members',
        'rules' => 'callback__validate_members',
    ),
);


$config['pass_validation'] = array(
    array(
        'field' => 'pass_type',
        'label' => 'Pass Type',
        'rules' => 'required|numeric|callback__valid_pass_type',
    ),
    array(
        'field' => 'pass_no',
        'label' => 'Pass Number',
        'rules' => 'required|max[8]|numeric|callback__unique_pass_no',
    ),
    array(
        'field' => 'single_return',
        'label' => 'Single / Return',
        'rules' => 'required|in_list[1,2]',
    ),
    array(
        'field' => 'validity_from',
        'label' => 'Validity From',
        'rules' => 'required|callback__valid_date',
    ),
    array(
        'field' => 'validity_to',
        'label' => 'Validity To',
        'rules' => 'required|callback__valid_date',
    ),
    array(
        'field' => 'from_station_code',
        'label' => 'From Station',
        'rules' => 'required|callback__valid_station_code',
    ),
    array(
        'field' => 'to_station_code',
        'label' => 'To Station',
        'rules' => 'required|callback__valid_station_code|callback__not_same_station',
    ),
    array(
        'field' => 'home_foreign',
        'label' => 'Home / Foreign',
        'rules' => 'required|in_list[H,F,K,A]',
    ),
    array(
        'field' => 'via[]',
        'label' => 'Via',
        'rules' => 'max_length[5]',
    ),
    array(
        'field' => 'return_via[]',
        'label' => 'Return Via',
        'rules' => 'max_length[5]',
    ),
    array(
        'field' => 'break_journey[]',
        'label' => 'Break Journey (Onward)',
        'rules' => 'max_length[5]',
    ),
    array(
        'field' => 'employee',
        'label' => 'Employee',
        'rules' => 'callback__valid_pass_balance',
    ),
);

$config['account_update_validation'] = array(
    array(
        'field' => 'EMPNO',
        'label' => 'Employee No',
        'rules' => 'required|integer'
    ),
    array(
        'field' => 'ACYEAR',
        'label' => 'Pass Year',
        'rules' => 'integer|exact_length[4]'
    ),
    array(
        'field' => 'PASS_TOTAL',
        'label' => 'Pass Total',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'PASS_AVAILED',
        'label' => 'Pass Availed',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'PASS_ASON',
        'label' => 'Pass As On',
        'rules' => 'callback__valid_ason_date'
    ),
    array(
        'field' => 'SECONDA_TOTAL',
        'label' => '2AC Total',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'SECONDA_AVAILED',
        'label' => '2AC Availed',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'SECONDA_ASON',
        'label' => '2AC As On',
        'rules' => 'callback__valid_ason_date'
    ),
    array(
        'field' => 'PTO_TOTAL',
        'label' => 'PTO Total',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'PTO_AVAILED',
        'label' => 'PTO Availed',
        'rules' => 'required|integer|greater_than_equal_to[0]'
    ),
    array(
        'field' => 'PTO_ASON',
        'label' => 'PTO As On',
        'rules' => 'callback__valid_ason_date'
    ),
);

$config['employee_update_validation'] = array(
    array(
        'field' => 'EMPNO',
        'label' => 'Employee No',
        'rules' => 'required|integer|exact_length[6]'
    ),
    array(
        'field' => 'ENAME',
        'label' => 'Employee Name',
        'rules' => 'required'
    ),
    array(
        'field' => 'DESIG',
        'label' => 'Employee Designation',
        'rules' => 'required'
    ),
    array(
        'field' => 'UNIT',
        'label' => 'Employee Designation',
        'rules' => 'required'
    ),
    array(
        'field' => 'OFFICE',
        'label' => 'Office',
        'rules' => 'required'
    ),
    array(
        'field' => 'DTBIRTH',
        'label' => 'Date of Birth',
        'rules' => 'required|callback__valid_date'
    ),
    array(
        'field' => 'DTAPPT',
        'label' => 'Date of Appointment',
        'rules' => 'required|callback__valid_date'
    ),
    array(
        'field' => 'DTRETT',
        'label' => 'Date of Retirement',
        'rules' => 'callback__valid_date'
    ),
    array(
        'field' => 'PAY',
        'label' => 'Pay Rate',
        'rules' => 'required|integer'
    ),
    array(
        'field' => 'ESCALE',
        'label' => 'Pay Scale',
        'rules' => 'required'
    ),
    array(
        'field' => 'EMPTYPE',
        'label' => 'Status',
        'rules' => 'required|integer|callback__valid_emptype'
    ),
    array(
        'field' => 'GROUPIND',
        'label' => 'Group',
        'rules' => 'required|integer|in_list[1,2,3]'
    ),
    array(
        'field' => 'ECLASS',
        'label' => 'Class',
        'rules' => 'required|integer|callback__valid_eclass'
    ),
    array(
        'field' => 'WIDOW_IND',
        'label' => 'Widow Pass Eligibility',
        'rules' => 'required|integer|in_list[1,2,3]'
    ),
    array(
        'field' => 'CELLNO',
        'label' => 'Cell Number',
        'rules' => 'required|integer|exact_length[10]'
    ),
);

$config['family_update_validation'] = array(
    array(
        'field' => 'empno',
        'label' => 'Employee No',
        'rules' => 'required|integer|exact_length[6]'
    ),
    array(
        'field' => 'fslno',
        'label' => 'F Relation',
        'rules' => 'required'
    ),
    array(
        'field' => 'name',
        'label' => 'Name',
        'rules' => 'required'
    ),
    array(
        'field' => 'db',
        'label' => 'Date of Birth',
        'rules' => 'required|callback__valid_date'
    ),
    array(
        'field' => 'frelation',
        'label' => 'F Relation',
        'rules' => 'required'
    ),
    array(
        'field' => 'fallowed',
        'label' => 'F Allowed',
        'rules' => 'required|in_list[Y,N]'
    ),
);