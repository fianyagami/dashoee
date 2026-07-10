<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeekk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeekk_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['tahun_kk']   = date('Y');

        $data['judul']      = "OEE per KK";
        $data['template']   = "Dashoeekk_view";
        $data['js']         = "Dashoeekk_script";
        $data['css']        = "Dashoeekk_style";

        $data['link']       = base_url() . 'dashoeekk';

        $this->load->view('v_main', $data);
    }

    public function getKK()
    {
        $thn_kk = $this->input->get('thn_kk');
        $q      = $this->input->get('q');

        echo json_encode($this->Dashoeekk_mod->getKK($thn_kk, $q));
    }

    public function getDashboard()
    {
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $result = array(
            'summary'       => $this->Dashoeekk_mod->getSummaryOEE($nomor_kk, $tanggal_kk),
            'downtime'      => $this->Dashoeekk_mod->getTopDowntime($nomor_kk, $tanggal_kk),
            'defect'        => $this->Dashoeekk_mod->getTopDefect($nomor_kk, $tanggal_kk),
            'actual_target' => $this->Dashoeekk_mod->getActualTarget($nomor_kk, $tanggal_kk)
        );

        echo json_encode($result);
    }

    public function getDetailModal()
    {
        $type       = $this->input->post('type');
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $data = $this->Dashoeekk_mod->getDetailModal($type, $nomor_kk, $tanggal_kk);

        echo json_encode(array('data' => $data));
    }
}
