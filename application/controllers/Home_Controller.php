<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property Family_model family
 * @property Account_model account
 * @property Employee_model employee
 * @property Pass_type_model pass_type
 * @property Trans_model trans
 */
class Home_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('First_class_pass', NULL, 'FCP');
		$this->load->library('Second_class_pass', NUll, 'SCP');
		$this->load->library('Second_AC_class_pass', NUll, 'SACP');
		$this->load->library('First_A_pass', NULL, 'FACP');

		$this->load->model('Employee_model', 'employee');
		$this->load->model('Family_model', 'family');
		$this->load->model('Account_model', 'account');
		$this->load->model('Pass_type_model', 'pass_type');
		$this->load->model('Print_pass_model', 'print');
		$this->load->model('Trans_model', 'trans');
	}

	public function print()
	{
		return view('pass.print');
	}

	public function cancel()
	{
		return view('pass.cancel');
	}

	public function cancel_pass()
	{
		if ($this->input->method() !== 'post') {
			return redirect('pass/cancel');
		}

		$passno = trim((string) $this->input->post('passno'));
		$reason = trim((string) $this->input->post('reason'));

		if ($passno === '' || ! ctype_digit($passno)) {
			return redirect_with('pass/cancel', [
				'error' => 'Please enter a valid pass number.'
			]);
		}

		if ($reason === '') {
			return redirect_with('pass/cancel', [
				'error' => 'Cancel reason is required.'
			]);
		}

		$pass = $this->trans->find((int) $passno);
		if (! $pass) {
			return redirect_with('pass/cancel', [
				'error' => 'Pass number not found.'
			]);
		}

		try {
			$this->trans->cancel_pass((int) $passno, $reason);
			log_message('info', "Pass {$passno} cancelled by ". $this->session->userdata('username'));
			return redirect_with('pass/cancel', [
				'success' => "Pass {$passno} cancelled successfully."
			]);
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			return redirect_with('pass/cancel', [
				'error' => $e->getMessage(),
			]);
		}
	}

	public function print_pass($passno = null)
	{
		$passno = $passno ? $passno : trim((string) $this->input->post('passno'));
		$empno = trim((string) $this->input->post('empno'));

		if ($passno !== '') {
			if (! is_numeric($passno)) {
				return custom_404();
			}

			$pass_details = $this->print->get_pass((int) $passno);
			if (! $pass_details) {
				return custom_404();
			}

			return redirect("pass/$passno/pdf");
		}

		if ($empno === '' || ! ctype_digit($empno)) {
			return custom_404();
		}

		$passes = $this->print->get_passes_by_empno((int) $empno);
		return view('pass.print', compact('passes', 'empno'));
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
		} else if($pass_details['PCLASS'] == 'First-A') {
			return $this->FACP->generate($pass_details);
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
