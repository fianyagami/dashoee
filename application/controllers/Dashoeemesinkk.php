<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeemesinkk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeemesinkk_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['tahun']      = date('Y');
        $data['bulan']      = date('n');

        $data['judul']      = "OEE per Mesin dan KK";
        $data['template']   = "Dashoeemesinkk_view";
        $data['js']         = "Dashoeemesinkk_script";
        $data['css']        = "Dashoeemesinkk_style";

        $data['link']       = base_url() . 'dashoeemesinkk';

        $this->load->view('v_main', $data);
    }

    public function getMesin()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Dashoeemesinkk_mod->getMesin($q));
    }

    public function getKK()
    {
        $thn_kk = $this->input->get('thn_kk');
        $q      = $this->input->get('q');

        echo json_encode($this->Dashoeemesinkk_mod->getKK($thn_kk, $q));
    }

    public function getDashboard()
    {
        $tahun      = $this->input->post('tahun');
        $bulan      = $this->input->post('bulan');
        $kdmesin    = $this->input->post('kdmesin');
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $result = array(
            'summary'       => $this->Dashoeemesinkk_mod->getSummaryOEE($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'downtime'      => $this->Dashoeemesinkk_mod->getTopDowntime($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'defect'        => $this->Dashoeemesinkk_mod->getTopDefect($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk),
            'actual_target' => $this->Dashoeemesinkk_mod->getActualTarget($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
        );

        echo json_encode($result);
    }

    public function getDetailModal()
    {
        $type       = $this->input->post('type');
        $tahun      = $this->input->post('tahun');
        $bulan      = $this->input->post('bulan');
        $kdmesin    = $this->input->post('kdmesin');
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $data = $this->Dashoeemesinkk_mod->getDetailModal($type, $tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk);

        echo json_encode(array('data' => $data));
    }
}
