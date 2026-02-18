<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Employee_model $employee
 * @property Account_model $account
 * @property Pass_type_model $pass_type
 * @property Station_model $station
 * @property Trans_model $trans
 * @property PRoute_model $route
 */
class Pass_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();        
        $this->load->model('Employee_model', 'employee');
        $this->load->model('Pass_type_model', 'pass_type');
        $this->load->model('Station_model', 'station');
        $this->load->model('Trans_model', 'trans');
        $this->load->model('PRoute_model', 'route');
    }

    public function index()
    {
        return view('pass.index');
    }

    public function create()
    {
        $estatus = $this->employee->status->all();
        $pass_types = $this->pass_type->all();
        $relationships = $this->employee->relation->all();

        $stations = $this->station->all();
        $current_tab = flashdata('current_tab', 1);

        return view('pass.create', compact(
            'current_tab',
            'estatus',
            'stations',
            'pass_types',
            'relationships',
        ));
    }

    public function submit()
    {
        if ($this->input->method() !== 'post') {
            return redirect('pass/create');
        }

        $current_tab = (int) $this->input->post('current_tab');
        $groups = [
            1 => 'account_validation',
            2 => 'family_validation',
            3 => 'pass_validation'
        ];

        if (! isset($groups[$current_tab])) {
            return redirect('pass/create');
        }

        // Validate current tab
        if ($this->form_validation->run($groups[$current_tab]) === false) {
            return redirect_with('pass/create', [
                'old'         => $this->input->post(),
                'current_tab' => $current_tab,
                'error'       => validation_errors('<p class="mb-0">', '</p>'),
                'form_error'      => $this->form_validation->error_array(),
            ]);
        }

        // If tab 3 passes then submit
        if ($current_tab === 3) {
            $this->trans->create_pass();

            $pass_no = $this->input->post('pass_no');
            return redirect_with('pass/' . $pass_no . '/pdf');
        }

        // Move to next tab
        return redirect_with('pass/create', [
            'old'         => $this->input->post(),
            'current_tab' => ($current_tab + 1),
        ]);
    }

    public function _unique_pass_no($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            $this->form_validation->set_message('_unique_pass_no', 'The %s is required');
            return false;
        }

        if (! ctype_digit($value) || strlen($value) > 8) {
            $this->form_validation->set_message('_unique_pass_no', 'The %s must be less then 8 digits');
            return false;
        }

        // numeric, 10 digits — now check uniqueness
        if ($this->trans->find((int) $value)) {
            $this->form_validation->set_message('_unique_pass_no', 'The %s already exists');
            return false;
        }

        return true;
    }

    public function _valid_date($str)
    {
        $d = DateTime::createFromFormat('d/m/Y', $str);
        if (! $d || $d->format('d/m/Y') !== $str) {
            $this->form_validation->set_message('_valid_date', 'The %s must be a valid date in dd/mm/YYYY format');
            return false;
        }
        return true;
    }

    public function _valid_station_code($code)
    {
        $code = trim((string) $code);
        if ($code === '') return true;

        if ($this->station->get_by_code(strtoupper($code))) {
            return true;
        }

        $this->form_validation->set_message('_valid_station_code', 'The %s is not a valid station code');
        return false;
    }

    public function _validate_members()
    {
        $members = $this->input->post('members');
        if (! is_array($members) || count($members) === 0) {
            $this->form_validation->set_message('_validate_members', 'At least one member must be selected');
            return false;
        }

        $employee_no = $this->input->post('empno');
        $members = $this->employee->family->getRelations($employee_no, $members);
        foreach ($members as $member) {
            if ($member['FALLOWED'] === 'N' ) {
                $this->form_validation->set_message('_validate_members', 'Family member ' . $member['NAME'] . ' is not eligible for pass');
                return false;
            }
        }

        return true;
    }

    public function _valid_employee($empno)
    {
        $employee = $this->employee->find($empno);
        if (! $employee) {
            $this->form_validation->set_message('_valid_employee', 'The %s does not exist');
            return false;
        }

        return true;
    }

    public function _valid_pass_balance()
    {
        $employee_no = $this->input->post('empno');
        $account_type = $this->input->post('account_type');
        $account_year = $this->input->post('account_year');
        $pass_type = $this->input->post('pass_type');
        
        if(empty($pass_type) ) {
            return true;
        }

        $required = (int) $this->input->post('single_return');

        $should_debit = $this->pass_type->should_debit_account($pass_type);
        if ($should_debit) {

            if(empty($account_type) || empty($account_year) ) {
                $this->form_validation->set_message('_valid_pass_balance', 'The Account Type and Account Year is required');
                return false;
            }

            if(! $this->account->has_balance($employee_no, $account_year, $account_type, $required)) {
                $this->form_validation->set_message('_valid_pass_balance', 'The %s does not have enough PASS/PTO balance.');
                return false;
            }
        }
    }

    public function _not_same_station($to_station)
    {
        $from_station = $this->input->post('from_station_code');
        if( empty($from_station) || empty($to_station ) ) {
            return true;
        }

        if ($from_station === $to_station) {
            $this->form_validation->set_message('_not_same_station', 'From Station and To Station must not be the same');
            return false;
        }

        return true;
    }

    public function _valid_pass_type($pass_type)
    {
        // Must be numeric and >= 1
        if (empty($pass_type) || !ctype_digit($pass_type) || intval($pass_type) < 1) {
            $this->form_validation->set_message('_valid_pass_type', 'Invalid Pass Type.');
            return FALSE;
        }

        // Check TCODE exists in database
        $exists = $this->pass_type->does_exist((int) $pass_type);

        if (!$exists) {
            $this->form_validation->set_message('_valid_pass_type', 'Selected Pass Type does not exist.');
            return FALSE;
        }

        return TRUE;
    }

}