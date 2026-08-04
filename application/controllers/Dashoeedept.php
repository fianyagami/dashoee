<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeedept extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeedept_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['tahun_dept'] = date('Y');
        $data['bulan_dept'] = date('n');

        $data['judul']      = "OEE per Departemen";
        $data['template']   = "Dashoeedept_view";
        $data['js']         = "Dashoeedept_script";
        $data['css']        = "Dashoeedept_style";

        $data['link']       = base_url() . 'dashoeedept';

        $this->load->view('v_main', $data);
    }

    public function getDepartemen()
    {
        $q = $this->input->get('q');

        echo json_encode($this->Dashoeedept_mod->getDepartemen($q));
    }

    public function getDashboard()
    {
        $thn        = $this->input->post('thn');
        $bln        = $this->input->post('bln');
        $departemen = $this->input->post('departemen');

        $result = array(
            'summary'       => $this->Dashoeedept_mod->getSummaryOEE($thn, $bln, $departemen),
            'downtime'      => $this->Dashoeedept_mod->getTopDowntime($thn, $bln, $departemen),
            'defect'        => $this->Dashoeedept_mod->getTopDefect($thn, $bln, $departemen),
            'actual_target' => $this->Dashoeedept_mod->getActualTarget($thn, $bln, $departemen)
        );

        echo json_encode($result);
    }

    public function getDetailModal()
    {
        $type       = $this->input->post('type');
        $thn        = $this->input->post('thn');
        $bln        = $this->input->post('bln');
        $departemen = $this->input->post('departemen');

        $data = $this->Dashoeedept_mod->getDetailModal($type, $thn, $bln, $departemen);

        echo json_encode(array('data' => $data));
    }
}
