<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Employee_model $employee
 * @property Account_model $account
 * @property PRoute_model $route
 */
class Ajax_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('Employee_model', 'employee');
        $this->load->model('Account_model', 'account');
        $this->load->model('PRoute_model', 'route');

        if(! $this->input->is_ajax_request()) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Bad Request',
                    'code'  => 400
                ]));
        }
    }

    public function inquire()
    {
        $empno = $this->input->post('empno', true);
        if(empty($empno) && !is_numeric($empno)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Invalid or Missing Employee Number',
                    'code'  => 400
                ]));
        }

        $employee = $this->employee->find($empno);
        if(empty($employee)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Employee Not Found',
                    'code'  => 400
                ]));
        }

        $passes = $this->account->findPassByEmp($empno);
        $ptos = $this->account->findPtoByEmp($empno);
        $second_pass = $this->account->findSecondPassByEmp($empno);
        $family = $this->employee->family->find($empno);
        
        return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'employee' => array_change_key_case($employee),
                    'passes' => array_change_key_case_recursive($passes),
                    'ptos' => array_change_key_case_recursive($ptos),
                    'second_pass' => array_change_key_case_recursive($second_pass),
                    'family' => array_change_key_case_recursive($family),
                    'next_pass_number' => next_pass_number($empno),
                    'code'  => 200
                ]));
    }

    public function get_family()
    {
        $empno = $this->input->post('empno', true);
        if(empty($empno) && !is_numeric($empno)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Invalid or Missing Employee Number',
                    'code'  => 400
                ]));
        }

        $family = $this->employee->family->find($empno);
        return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'family' => array_change_key_case_recursive($family),
                    'code'  => 200
                ]));
    }

    public function update_family()
    {
        $empno = $this->input->post('empno', true);
        $fslno = $this->input->post('fslno', true);

        if(empty($empno) && !is_numeric($empno)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Invalid or Missing Employee Number',
                    'code'  => 400,
                ]));
        }

        if($this->form_validation->run('family_update_validation') === false) {
            $this->form_validation->set_error_delimiters('', '');
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => validation_errors('', ''),
                    'code'  => 400,
                ]));
        }

        $this->employee->family->update($empno, $fslno, $this->input->post());
        return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'code'  => 200,
                    'success' => 'Recoard has been successfully updated.',
                ]));
    }

    public function routes()
    {
        $frstn = $this->input->post('frstn', true);
        $tostn = $this->input->post('tostn', true);

        if(empty($frstn) || empty($tostn)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'Invalid or Missing Station Codes.',
                    'code'  => 400,
                ]));
        }

        $routes = $this->route->get_route_between($frstn, $tostn);
        return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'code'  => 200,
                    'routes' => $routes,
                ]));
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
}
