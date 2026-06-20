<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monprod extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Monprod_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']      = "Monitoring Produksi";
        $data['template']   = "Monprod_view";
        $data['js']         = "Monprod_script";
        $data['css']        = "Monprod_style";
        $data['link']       = base_url() . 'monprod';

        $this->load->view('v_main', $data);
    }

    public function get_mesin()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Monprod_mod->getMesin($q));
    }

    public function get_detail()
    {
        $thn   = $this->input->post('thn');
        $bln   = $this->input->post('bln');
        // $dept  = $this->input->post('dept');
        $mesin = $this->input->post('mesin');

        $data = $this->Monprod_mod->getDetail(
            $thn,
            $bln,
            // $dept,
            $mesin
        );

        echo json_encode($data);
    }

    public function get_detail_waktu()
    {
        $mesin    = $this->input->post('mesin');
        $tanggal  = $this->input->post('tanggal');
        $shift    = $this->input->post('shift');
        $nomor_kk = $this->input->post('nomor_kk');
        $proses   = $this->input->post('proses');

        $data = $this->Monprod_mod->get_detail_waktu(
            $mesin,
            $tanggal,
            $shift,
            $nomor_kk,
            $proses
        );

        echo json_encode($data);
    }
}
