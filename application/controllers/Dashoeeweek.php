<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeeweek extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashoeeweek_mod');
        if (!$this->session->userdata($GLOBALS['project'] . '-ID_USER')) {
            redirect(base_url('login'));
        }
    }

    public function index()
    {
        $data['tahun']  = (int) date('Y');
        $data['minggu'] = (int) date('W'); // PHP 'W' = ISO-8601 week number, sama definisinya dgn Oracle IW

        $data['judul']      = "OEE per Week";
        $data['template']   = "Dashoeeweek_view";
        $data['js']         = "Dashoeeweek_script";
        $data['css']        = "Dashoeeweek_style";

        $data['link']       = base_url() . 'dashoeeweek';

        $this->load->view('v_main', $data);
    }

    /**
     * Hitung rentang tanggal Senin s.d Minggu dari ISO Year + ISO Week.
     * Return array('awal' => 'Y-m-d', 'akhir' => 'Y-m-d')
     */
    private function getWeekRange($tahun, $minggu)
    {
        $dto = new DateTime();
        $dto->setISODate((int) $tahun, (int) $minggu, 1); // hari ke-1 = Senin

        $awal = $dto->format('Y-m-d');

        $dto->modify('+6 days'); // Minggu (hari terakhir minggu itu)
        $akhir = $dto->format('Y-m-d');

        return array('awal' => $awal, 'akhir' => $akhir);
    }

    public function getDashboard()
    {
        $tahun  = $this->input->post('tahun');
        $minggu = $this->input->post('minggu');

        $range = $this->getWeekRange($tahun, $minggu);

        $result = array(
            'summary'       => $this->Dashoeeweek_mod->getSummaryOEE($range['awal'], $range['akhir']),
            'downtime'      => $this->Dashoeeweek_mod->getTopDowntime($range['awal'], $range['akhir']),
            'defect'        => $this->Dashoeeweek_mod->getTopDefect($range['awal'], $range['akhir']),
            'actual_target' => $this->Dashoeeweek_mod->getActualTarget($range['awal'], $range['akhir']),
            'range'         => $range
        );

        echo json_encode($result);
    }

    public function getDetailModal()
    {
        $type   = $this->input->post('type');
        $tahun  = $this->input->post('tahun');
        $minggu = $this->input->post('minggu');

        $range = $this->getWeekRange($tahun, $minggu);

        $data = $this->Dashoeeweek_mod->getDetailModal($type, $range['awal'], $range['akhir']);

        echo json_encode(array('data' => $data));
    }
}
