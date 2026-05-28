<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monprod extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Monprod_mod');
    }

    public function index()
    {
        $data['template'] = "Monprod_view";
        $data['link'] = base_url() . 'monprod';
        $this->load->view('v_main', $data);
    }

    public function get_mesin()
    {
        $thn = $this->input->post('thn');
        $bln = $this->input->post('bln');

        $data = $this->Monprod_mod
            ->getMesin($thn, $bln);

        echo json_encode($data);
    }

    public function get_detail()
    {
        $thn   = $this->input->post('thn');
        $bln   = $this->input->post('bln');
        $dept  = $this->input->post('dept');
        $mesin = $this->input->post('mesin');

        $data = $this->Monprod_mod->getDetail(
            $thn,
            $bln,
            $dept,
            $mesin
        );



        echo json_encode($data);
    }
}
