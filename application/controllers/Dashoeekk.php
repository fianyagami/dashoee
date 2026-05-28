<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeekk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeekk_mod');
    }

    public function index()
    {
        $data['tahun'] = date('Y');
        $data['bulan'] = date('n');
        $data['template'] = "Dashoeekk_view";
        $data['link'] = base_url() . 'dashoeekk';
        $this->load->view('v_main', $data);
    }

    public function getMesin()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Dashoeekk_mod->getMesin($q));
    }

    public function getKK()
    {
        $thn_kk = $this->input->get('thn_kk');
        $q      = $this->input->get('q');

        echo json_encode($this->Dashoeekk_mod->getKK($thn_kk, $q));
    }

    public function getDashboard()
    {
        $tahun      = $this->input->post('tahun');
        $bulan      = $this->input->post('bulan');
        $kdmesin    = $this->input->post('kdmesin');
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $result = array(
            'summary'       => $this->Dashoeekk_mod->getSummaryOEE($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'downtime'      => $this->Dashoeekk_mod->getTopDowntime($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'defect'        => $this->Dashoeekk_mod->getTopDefect($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'actual_target' => $this->Dashoeekk_mod->getActualTarget($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
        );

        echo json_encode($result);
    }
}
