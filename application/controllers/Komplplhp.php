<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Komplplhp extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Komplplhp_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']    = "Komparasi PLP vs LHP";
        $data['template'] = "Komplplhp_view";
        $data['js']       = "Komplplhp_script";
        $data['css']      = "Komplplhp_style";
        $data['link']     = base_url() . 'komplplhp';

        $this->load->view('v_main', $data);
    }

    public function get_mesin_plp()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Komplplhp_mod->getMesinPlp($q));
    }

    public function get_mesin_lhp()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Komplplhp_mod->getMesinLhp($q));
    }

    public function get_data_plp()
    {
        $thn   = $this->input->post('thn');
        $bln   = $this->input->post('bln');
        $mesin = $this->input->post('mesin');

        if (empty($thn) || empty($bln) || empty($mesin)) {
            echo json_encode([]);
            return;
        }

        echo json_encode($this->Komplplhp_mod->getDataPlp($thn, $bln, $mesin));
    }

    public function get_data_lhp()
    {
        $thn   = $this->input->post('thn');
        $bln   = $this->input->post('bln');
        $mesin = $this->input->post('mesin');

        if (empty($thn) || empty($bln) || empty($mesin)) {
            echo json_encode([]);
            return;
        }

        echo json_encode($this->Komplplhp_mod->getDataLhp($thn, $bln, $mesin));
    }
}
