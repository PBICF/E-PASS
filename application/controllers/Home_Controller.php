<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property Family_model family
 * @property Account_model account
 * @property Employee_model employee
 * @property Pass_type_model pass_type
 */
class Home_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('First_class_pass', NULL, 'FCP');
		$this->load->library('Second_class_pass', NUll, 'SCP');
		$this->load->library('Second_AC_class_pass', NUll, 'SACP');

		$this->load->model('Employee_model', 'employee');
		$this->load->model('Family_model', 'family');
		$this->load->model('Account_model', 'account');
		$this->load->model('Pass_type_model', 'pass_type');
		$this->load->model('Print_pass_model', 'print');
	}

	public function print()
	{
		return view('pass.print');
	}

	public function print_pass($passno = null)
	{
		$passno = $passno ? $passno : $this->input->post('passno');
	    if (! $passno || ! is_numeric($passno)) {
			return custom_404();
		}

	    $pass_details = $this->print->get_pass($passno);
	    if (! $pass_details) {
	        return custom_404();
	    }

		//return view('pass.pdf_iframe', compact('passno'));
		return redirect("pass/$passno/pdf");
	}

	public function render_pass($passno)
	{
		$passno = $passno ? $passno : $this->input->post('passno');
	    if (! $passno || ! is_numeric($passno)) {
			return custom_404();
		}

	    $pass_details = $this->print->get_pass($passno);
	    if (! $pass_details) {
	        return custom_404();
	    }

		if($pass_details['PCLASS'] == 'First') {
			return $this->FCP->generate($pass_details);
		} else if($pass_details['PCLASS'] == 'Second-A') {
			return $this->SACP->generate($pass_details);
		} else {
			return $this->SCP->generate($pass_details);
		}
	}
	
	public function update_employee()
	{
		if ($this->input->method() !== 'post') {
			show_error('Invalid request', 405);
		}
	}

	public function update_family()
	{
		if ($this->input->method() !== 'post') {
			show_error('Invalid request', 405);
		}

		foreach ($this->input->post() as $frelation => $data) {
			$this->family->update($data['empno'], $frelation, array_change_key_case($data, CASE_UPPER));
		}

		return redirect('home/'. $data['empno'] . '?tab=family');
	}

	public function update_pass()
	{
		if ($this->input->method() !== 'post') {
			show_error('Invalid request', 405);
		}

		foreach ($this->input->post()  as $year => $data) {
			$this->account->update($data['empno'], $year, array_change_key_case($data, CASE_UPPER));
		}

		return redirect('home/'. $data['empno'] . '?tab=pass');
	}

	public function update_remarks()
	{
		if ($this->input->method() !== 'post') {
			show_error('Invalid request', 405);
		}

	}
}
