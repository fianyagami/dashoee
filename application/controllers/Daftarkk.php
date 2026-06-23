<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Daftarkk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftarkk_mod');

        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']      = "Daftar KK";
        $data['template']   = "Daftarkk_view";
        $data['js']         = "Daftarkk_script";
        $data['css']        = "Daftarkk_style";
        $data['link']       = base_url() . 'daftarkk';

        $this->load->view('v_main', $data);
    }

    public function get_daftarkk_head()
    {
        $thn   = $this->input->post('thn');

        $data = $this->Daftarkk_mod->getDataKKhead(
            $thn
        );

        echo json_encode($data);
    }

    public function get_daftarkk_detail()
    {
        $thn     = $this->input->post('thn');
        $nokk    = $this->input->post('nokk');
        $tgl_kk  = $this->input->post('tgl_kk');

        $data = $this->Daftarkk_mod->getDataKKdetail(
            $thn,
            $nokk,
            $tgl_kk
        );

        echo json_encode($data);
    }
}
