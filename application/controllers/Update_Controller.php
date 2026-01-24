<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Employee_model $employee
 * @property Account_model $account
 * @property Pass_type_model $pass_type
 * @property Station_model $station
 * @property Classes_model $classes
 * @property Trans_model $trans
 */
class Update_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model', 'employee');
        $this->load->model('Pass_type_model', 'pass_type');
        $this->load->model('Station_model', 'station');
        $this->load->model('Classes_model', 'classes');
        $this->load->model('Trans_model', 'trans');
        $this->load->model('Account_model', 'account');
    }

    public function pass_account()
    {
        if ($this->input->method() === 'post') {
            $post = $this->input->post();
            $empno = $post['EMPNO'];
            unset($post['EMPNO']);

            foreach ($post as $year => $data) {
                $this->form_validation->set_data($data);
                
                if ($this->form_validation->run('account_update_validation') === false) {
                    $this->account->update_account($empno, $year, $data);
                } else {
                    return redirect_with('pass/account/update', [
                        'error' => validation_errors('<p class="mb-0">', '</p>')
                    ]);
                }
            }

            return redirect_with('pass/account/update', [
                'success' => 'Account updated successfully.'
            ]);
        }

        return view('update.account');
    }

    public function employee_update()
    {
        if ($this->input->method() === 'post') {
            if ($this->form_validation->run('employee_update_validation') === false) {
                return redirect_with('pass/employee/update', [
                    'error' => validation_errors('<p class="mb-0">', '</p>')
                ]);
            }

            $post = $this->input->post();
            $this->employee->update($post['EMPNO'], $post);
            return redirect_with('pass/employee/update', [
                'success' => 'Employee updated successfully.'
            ]);
        }

        $estatus = $this->employee->status->all();
        $classes = $this->classes->all();
        $pass_types = $this->pass_type->all();

        return view('update.employee', compact(
            'estatus',
            'classes',
            'pass_types',
        ));
    }

    public function family_update()
    {
        if ($this->input->method() === 'post') {
        }

        $relationships = $this->employee->family->relation->all();
        return view('update.family', compact('relationships'));
    }

    public function _valid_date($value)
    {
        if(empty($value) || $value === '-' || $value === null) {
            return true;
        }

        $d = DateTime::createFromFormat('d/m/Y', $value);
        if (! $d || $d->format('d/m/Y') !== $value) {
            $this->form_validation->set_message('_valid_date', 'The %s must be a valid date in dd/mm/YYYY format');
            return false;
        }

        return true;
    }


    public function _valid_ason_date($value)
    {
        if ($value === '' || $value === null || $value === '-') {
            return true;
        }

        $d = DateTime::createFromFormat('d/m/Y', $value);
        if (!$d || $d->format('d/m/Y') !== $value) {
            $this->form_validation->set_message(
                'valid_ason_date',
                'The {field} must be a valid date (dd/mm/yyyy).'
            );
            return false;
        }

        return true;
    }

    function _valid_emptype($value)
    {
        return $this->employee->status->is_valid($value);
    }

    public function _valid_eclass($value)
    {
        return $this->classes->is_valid($value);
    }
}
