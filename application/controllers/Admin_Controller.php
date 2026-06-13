<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();        
        $this->load->model('Pass_type_model', 'pass_type');
    }

    public function login()
    {
        return view('admin.login');
    }

    public function process_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Simple validation (replace with actual authentication logic)
        if ($username === 'pbicf' && $password === 'Pass@123') {
            $this->session->set_userdata('admin_logged_in', TRUE);
            redirect('admin');
        } else {
            return redirect_with('admin/login', [
                'error' => 'Invalid username or password.'
            ]);
        }
    }

    public function index()
    {
        if (! $this->session->userdata('admin_logged_in')) {
            redirect('admin/login');
        }

        $data['pass_types'] = $this->pass_type->get_all();
        
        return view('admin.index', $data);
    }

    public function update_pass_type(int $tcode)
    {
        if (! $this->session->userdata('admin_logged_in')) {
            redirect('admin/login');
        }

        if ($this->input->method() !== 'post') {
            return redirect('admin');
        }

        $allowed     = $this->input->post('allowed') === 'Y' ? 'Y' : 'N';
        $fixed_dates = $this->input->post('fixed_dates') === 'Y' ? 'Y' : 'N';
        $ac_update   = $this->input->post('ac_update') === 'Y' ? 'Y' : 'N';

        if (! $this->pass_type->does_exist($tcode)) {
            return redirect_with('admin', [
                'error' => 'Pass type not found.'
            ]);
        }

        $this->db
            ->where('TCODE', $tcode)
            ->update('TYPEMR', [
                'TNAME'       => $this->input->post('tname'),
                'EMPTYPE'     => $this->input->post('emptype'),
                'TNAME_HINDI' => $this->input->post('tname_hindi'),
                'TCATEGORY'   => $this->input->post('tcategory'),
                'FIXED_DATES' => $fixed_dates,
                'AC_UPDATE'   => $ac_update,
                'ALLOWED'     => $allowed,
            ]);

        return redirect_with('admin', [
            'success' => 'Pass type updated successfully.'
        ]);
    }

}