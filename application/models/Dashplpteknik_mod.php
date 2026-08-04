<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashplpteknik_mod extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getTopMesin($thn, $bln)
    {
        $sql = "
            SELECT NAMA_MESIN, JUMLAH FROM (
                SELECT
                    NAMA_MESIN,
                    COUNT(*) AS JUMLAH
                FROM V_PLP_TEKNIK
                WHERE TAHUN = ?
                AND BULAN = ?
                GROUP BY NAMA_MESIN
                ORDER BY COUNT(*) DESC
            )
            WHERE ROWNUM <= 10
        ";

        $query = $this->db->query($sql, [$thn, $bln]);

        $mesin  = array();
        $jumlah = array();

        foreach ($query->result() as $row) {
            $mesin[]  = $row->NAMA_MESIN;
            $jumlah[] = (int) $row->JUMLAH;
        }

        return array(
            'kategori' => $mesin,
            'nilai'    => $jumlah
        );
    }

    public function getJenisPekerjaan($thn, $bln)
    {
        $sql = "
            SELECT
                JENIS_PEKERJAAN,
                COUNT(*) AS JUMLAH
            FROM V_PLP_TEKNIK
            WHERE TAHUN = ?
            AND BULAN = ?
            GROUP BY JENIS_PEKERJAAN
            ORDER BY COUNT(*) DESC
        ";

        $query = $this->db->query($sql, [$thn, $bln]);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'name'  => $row->JENIS_PEKERJAAN,
                'value' => (int) $row->JUMLAH
            );
        }

        return $data;
    }

    public function getTrenHarian($thn, $bln)
    {
        $sql = "
            SELECT
                TO_CHAR(TANGGAL_PLP, 'DD') AS TGL,
                COUNT(*) AS JUMLAH
            FROM V_PLP_TEKNIK
            WHERE TAHUN = ?
            AND BULAN = ?
            GROUP BY TO_CHAR(TANGGAL_PLP, 'DD')
            ORDER BY TGL
        ";

        $query = $this->db->query($sql, [$thn, $bln]);

        // Map hasil query -> [tanggal => jumlah]
        $map = array();
        foreach ($query->result() as $row) {
            $map[(int) $row->TGL] = (int) $row->JUMLAH;
        }

        // Isi semua tanggal dalam bulan tsb, walaupun jumlahnya 0
        $jumlahHariDalamBulan = (int) date('t', mktime(0, 0, 0, (int) $bln, 1, (int) $thn));

        $tanggal = array();
        $jumlah  = array();

        for ($i = 1; $i <= $jumlahHariDalamBulan; $i++) {
            $tanggal[] = (string) $i;
            $jumlah[]  = isset($map[$i]) ? $map[$i] : 0;
        }

        return array(
            'kategori' => $tanggal,
            'nilai'    => $jumlah
        );
    }

    public function getTopWaktuProses($thn, $bln)
    {
        // WAKTU_PROSES di view sudah string terformat, jadi rata-rata dihitung
        // langsung dari selisih WAKTU_FINISH - WAKTU_START (kolom DATE mentah)
        $sql = "
            SELECT NAMA_MESIN, RATA2_JAM FROM (
                SELECT
                    NAMA_MESIN,
                    AVG(WAKTU_FINISH - WAKTU_START) * 24 AS RATA2_JAM
                FROM V_PLP_TEKNIK
                WHERE TAHUN = ?
                AND BULAN = ?
                AND WAKTU_START IS NOT NULL
                AND WAKTU_FINISH IS NOT NULL
                GROUP BY NAMA_MESIN
                ORDER BY AVG(WAKTU_FINISH - WAKTU_START) DESC
            )
            WHERE ROWNUM <= 10
        ";

        $query = $this->db->query($sql, [$thn, $bln]);

        $mesin  = array();
        $jumlah = array();

        foreach ($query->result() as $row) {
            $mesin[]  = $row->NAMA_MESIN;
            $jumlah[] = round((float) $row->RATA2_JAM, 2);
        }

        return array(
            'kategori' => $mesin,
            'nilai'    => $jumlah
        );
    }
}
