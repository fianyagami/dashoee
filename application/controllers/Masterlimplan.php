<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masterlimplan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Masterlimplan_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']    = "Master Limit Planned";
        $data['template'] = "Masterlimplan_view";
        $data['js']       = "Masterlimplan_script";
        $data['css']      = "Masterlimplan_style";
        $data['link']     = base_url() . 'masterlimplan';

        $this->load->view('v_main', $data);
    }

    public function get_mesin()
    {
        echo json_encode($this->Masterlimplan_mod->getMesin());
    }

    public function get_mesin_aktif()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Masterlimplan_mod->getMesinAktif($q));
    }

    public function get_kegiatan()
    {
        $q = $this->input->get('q');
        echo json_encode($this->Masterlimplan_mod->getKegiatan($q));
    }

    public function get_data()
    {
        $kdmesin = $this->input->post('kdmesin');
        $data    = $this->Masterlimplan_mod->getData($kdmesin);
        echo json_encode($data);
    }

    public function save()
    {
        $id_limitplan = $this->input->post('id_limitplan');
        $kdmesin      = $this->input->post('kdmesin');
        $k_his        = $this->input->post('k_his');
        $limitplan    = $this->input->post('limitplan');
        $par_limitplan = $this->input->post('par_limitplan');

        $result = $this->Masterlimplan_mod->save(
            $id_limitplan,
            $kdmesin,
            $k_his,
            $limitplan,
            $par_limitplan
        );

        echo json_encode($result);
    }
}
