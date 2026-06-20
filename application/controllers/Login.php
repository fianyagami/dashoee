<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('m_login');
        $this->load->helper(array('form', 'url', 'do_helper'));
    }

    function index()
    {
        $this->load->view('v_login');
    }

    public function getClientIp()
    {
        $ip = $this->input->ip_address();

        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }

    function log_in()
    {
        $this->load->library('Aes_encryption');
        $aes = new Aes_encryption();

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // $encrypted_password = $aes->encrypt($password);
        // var_dump("Encrypted Password: " . $encrypted_password);

        $where = array(
            'USERNAME' => $username,
            'PASSWORD' => $aes->encrypt($password),
            'IS_AKTIF' => 1
        );
        $cek = $this->m_login->cek_login($where)->num_rows();
        if ($cek > 0) {
            $data = $this->m_login->cek_login($where)->row();

            //          SET USERNAME, DATA NAMA, DATA DEPARTMENT PRODUKSI, DATA DEPARTMENT USER
            // ini_set('session.gc_maxlifetime', 30 * 60);
            $this->session->set_userdata($GLOBALS['project'] . '-ID_USER', $data->ID_USER);
            $this->session->set_userdata($GLOBALS['project'] . '-USERNAME', $data->USERNAME);
            $this->session->set_userdata($GLOBALS['project'] . '-NAMA', $data->NAMA);
            $this->session->set_userdata($GLOBALS['project'] . '-ROLE', $data->ROLE);
            $this->session->set_userdata($GLOBALS['project'] . '-KA', $data->KA);

            //          UPDATE LAST LOGIN
            $ip = $this->getClientIp();

            $this->m_login->insertLogLogin(
                $data->ID_USER,
                $ip
            );

            redirect(base_url("core"));
        } else {
            redirect(base_url());
        }
    }

    function log_out()
    {
        //      UPDATE LAST LOGIN
        // $this->m_login->updateDataLastLogout($_SESSION[$GLOBALS['project'] . '-id_user']);
        //      DESTROY SESSION
        $this->session->unset_userdata($GLOBALS['project'] . '-ID_USER');
        $this->session->unset_userdata($GLOBALS['project'] . '-USERNAME');
        $this->session->unset_userdata($GLOBALS['project'] . '-NAMA');
        $this->session->unset_userdata($GLOBALS['project'] . '-ROLE');
        $this->session->unset_userdata($GLOBALS['project'] . '-KA');
        redirect(base_url());
    }
}
