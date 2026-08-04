<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashplpteknik extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashplpteknik_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']    = "Dashboard PLP Teknik";
        $data['template'] = "Dashplpteknik_view";
        $data['js']       = "Dashplpteknik_script";
        $data['css']      = "Dashplpteknik_style";
        $data['link']     = base_url() . 'dashplpteknik';

        $this->load->view('v_main', $data);
    }

    public function get_dashboard()
    {
        $thn = $this->input->post('thn');
        $bln = $this->input->post('bln');

        if (empty($thn) || empty($bln)) {
            echo json_encode(['error' => 'Tahun dan Bulan wajib dipilih.']);
            return;
        }

        $data = array(
            'top_mesin'        => $this->Dashplpteknik_mod->getTopMesin($thn, $bln),
            'jenis_pekerjaan'  => $this->Dashplpteknik_mod->getJenisPekerjaan($thn, $bln),
            'tren_harian'      => $this->Dashplpteknik_mod->getTrenHarian($thn, $bln),
            'top_waktu_proses' => $this->Dashplpteknik_mod->getTopWaktuProses($thn, $bln),
        );

        echo json_encode($data);
    }
}
