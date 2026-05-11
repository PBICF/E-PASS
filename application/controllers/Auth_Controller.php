<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_Controller extends CI_Controller {

    public function login()
    {
        return view('auth.login');
    }

    public function process()
    {
        if ($this->input->method() !== 'post') {
            redirect('login');
        }

        $users = [
            'accounts'  => 'Account@789',
            'pb'        => 'Pb@123',
            'security'  => 'Security@456'
        ];

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (isset($users[$username]) && $users[$username] === $password) {
            $this->session->set_userdata([
                'username' => $username,
                'logged_in' => true
            ]);
            $this->session->set_flashdata('success', 'Login successful!');
            redirect('/');
        } else {
            $this->session->set_flashdata('error', 'Invalid username or password');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        // Destroy the session completely to remove auth state
        $this->session->sess_destroy();
        redirect('auth/login');
    }

}
