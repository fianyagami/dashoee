<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kompbapobkk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kompbapobkk_mod', 'mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['judul']      = 'Komparasi BAPOB dan KK';
        $data['template']   = 'Kompbapobkk_view';
        $data['js']         = 'Kompbapobkk_script';
        $data['css']        = 'Kompbapobkk_style';
        $data['link']       = base_url() . 'kompbapobkk';

        $this->load->view('v_main', $data);
    }

    public function get_header_kk()
    {
        $thn = $this->input->get('thn');
        $q   = $this->input->get('q');

        $data = $this->mod->getHeaderKK($thn, $q);

        $result = [];
        foreach ($data as $row) {
            $tgl_kk = !empty($row->TANGGAL_KK) ? date('Y-m-d', strtotime($row->TANGGAL_KK)) : '';

            $result[] = [
                'id'            => $row->NOMOR_KK,
                'text'          => $row->NOMOR_KK . ' - ' . $row->NAMA_BARANG,
                'NOMOR_KK'      => $row->NOMOR_KK,
                'TANGGAL_KK'    => !empty($row->TANGGAL_KK) ? date('Y-m-d', strtotime($row->TANGGAL_KK)) : '',
                'NO_BAPOB'      => $row->NO_BAPOB,
                'TANGGAL_BAPOB' => $row->TANGGAL_BAPOB,
                'TAHUN_BAPOB'   => $row->TAHUN_BAPOB,
                'NAMA_BARANG'   => $row->NAMA_BARANG,
                'CUSTOMER'      => $row->CUSTOMER,
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

    public function get_header_bapob()
    {
        $thn        = $this->input->get('thn');
        $tgl_bapob  = $this->input->get('tgl_bapob');
        $q          = $this->input->get('q');
        $customer   = $this->input->get('customer');


        $data = $this->mod->getHeaderBAPOB($thn, $tgl_bapob, $customer, $q);

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id'             => $row->NO_BAPOB,
                'text'           => $row->NO_BAPOB . ' - ' . $row->PRODUK,
                'NO_BAPOB'       => $row->NO_BAPOB,
                'PRODUK'         => $row->PRODUK,
                'DIBUAT'         => $row->DIBUAT,
                'KODE_TRANSAKSI' => $row->KODE_TRANSAKSI,
                // 'TANGGAL_BAPOB'  => !empty($row->TANGGAL) ? date('d/m/Y', strtotime($row->TANGGAL)) : '',
                'TANGGAL_BAPOB'  => $row->TANGGAL,
                'CUSTOMER'       => $row->CUSTOMER
            ];
        }

        echo json_encode(['results' => $result]);
    }

    public function get_proses_bapob()
    {
        $no_bapob       = $this->input->post('no_bapob');
        $dibuat         = $this->input->post('dibuat');
        $kode_transaksi = $this->input->post('kode_transaksi');
        $tanggal_bapob  = $this->input->post('tanggal_bapob');

        if ($dibuat == 'PORT') {
            $data = $this->mod->getProsesBAPOBport($kode_transaksi, $tanggal_bapob);
        } else {
            $data = $this->mod->getProsesBAPOBweb($no_bapob, $kode_transaksi, $tanggal_bapob);
        }

        echo json_encode(['data' => $data]);
    }
}
