<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kompkklhp extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kompkklhp_mod', 'mod');

        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']      = 'Komparasi KK dan LHP';
        $data['template']   = 'Kompkklhp_view';
        $data['js']         = 'Kompkklhp_script';
        $data['css']        = 'Kompkklhp_style';
        $data['link']       = base_url() . 'kompkklhp';

        $this->load->view('v_main', $data);
    }

    public function get_header_kk()
    {
        $thn = $this->input->get('thn');
        $q   = $this->input->get('q');

        $data = $this->mod->getHeaderKK($thn, $q);

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id'          => $row->NOMOR_KK,
                'text'        => $row->NOMOR_KK . ' - ' . $row->NAMA_BARANG,
                'NOMOR_KK'    => $row->NOMOR_KK,
                'TANGGAL_KK'  => !empty($row->TANGGAL_KK) ? date('Y-m-d', strtotime($row->TANGGAL_KK)) : '',
                'NAMA_BARANG' => $row->NAMA_BARANG,
                'CUSTOMER'    => $row->CUSTOMER,
                'REVISI_KE'   => $row->REVISI_KE,
                'STATUS_KK'   => $row->STATUS_KK,
            ];
        }

        echo json_encode(['results' => $result]);
    }

    public function get_proses_kk()
    {
        $nomor_kk   = $this->input->post('nomor_kk');
        $tahun      = $this->input->post('tahun');
        $tanggal_kk = $this->input->post('tanggal_kk');

        $data = $this->mod->getProsesKK($nomor_kk, $tahun, $tanggal_kk);

        echo json_encode(['data' => $data]);
    }

    public function get_proses_lhp()
    {
        $nomor_kk   = $this->input->post('nomor_kk');
        $tanggal_kk = $this->input->post('tanggal_kk');
        $revisi_ke  = $this->input->post('revisi_ke');
        $status_kk  = $this->input->post('status_kk');

        $data = $this->mod->getProsesLHP($nomor_kk, $tanggal_kk, $revisi_ke, $status_kk);

        echo json_encode(['data' => $data]);
    }
}
