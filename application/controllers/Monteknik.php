<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monteknik extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Monteknik_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']    = "Monitoring PLP Teknik";
        $data['template'] = "Monteknik_view";
        $data['js']       = "Monteknik_script";
        $data['css']      = "Monteknik_style";
        $data['link']     = base_url() . 'monteknik';

        $this->load->view('v_main', $data);
    }

    public function get_departemen()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Monteknik_mod->getDepartemen($q));
    }

    public function get_mesin()
    {
        $q    = $this->input->get('q');
        $dept = $this->input->get('dept');
        echo json_encode($this->Monteknik_mod->getMesin($q, $dept));
    }

    public function get_detail()
    {
        $thn   = $this->input->post('thn');
        $bln   = $this->input->post('bln');
        $dept  = $this->input->post('dept');
        $mesin = $this->input->post('mesin');

        if (empty($thn) || empty($bln)) {
            echo json_encode([]);
            return;
        }

        $data = $this->Monteknik_mod->getDetail($thn, $bln, $dept, $mesin);

        echo json_encode($data);
    }
}
