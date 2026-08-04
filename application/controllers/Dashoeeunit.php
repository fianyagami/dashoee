<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeeunit extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeeunit_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['tahun_unit'] = date('Y');
        $data['bulan_unit'] = date('n');

        $data['judul']      = "OEE per Unit";
        $data['template']   = "Dashoeeunit_view";
        $data['js']         = "Dashoeeunit_script";
        $data['css']        = "Dashoeeunit_style";

        $data['link']       = base_url() . 'dashoeeunit';

        $this->load->view('v_main', $data);
    }

    public function getDashboard()
    {
        $thn = $this->input->post('thn');
        $bln = $this->input->post('bln');

        $result = array(
            'summary'       => $this->Dashoeeunit_mod->getSummaryOEE($thn, $bln),
            'downtime'      => $this->Dashoeeunit_mod->getTopDowntime($thn, $bln),
            'defect'        => $this->Dashoeeunit_mod->getTopDefect($thn, $bln),
            'actual_target' => $this->Dashoeeunit_mod->getActualTarget($thn, $bln)
        );

        echo json_encode($result);
    }

    public function getDetailModal()
    {
        $type = $this->input->post('type');
        $thn  = $this->input->post('thn');
        $bln  = $this->input->post('bln');

        $data = $this->Dashoeeunit_mod->getDetailModal($type, $thn, $bln);

        echo json_encode(array('data' => $data));
    }
}
