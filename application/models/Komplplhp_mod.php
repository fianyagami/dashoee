<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Komplplhp_mod extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getMesinPlp($q = null)
    {
        $sql = "
            SELECT KODE_MESIN, TRIM(NAMA_MESIN) AS NAMA_MESIN
            FROM AMPHIBI.MON_TKN_MS_MESIN
        ";

        $bind = array();

        if (!empty($q)) {
            $sql .= " WHERE UPPER(NAMA_MESIN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(NAMA_MESIN)";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'    => $row->KODE_MESIN,
                'text'  => $row->NAMA_MESIN,
                'kode'  => $row->KODE_MESIN,
                'nama'  => $row->NAMA_MESIN
            );
        }

        return array('results' => $data);
    }

    public function getMesinLhp($q = null)
    {
        $sql = "
            SELECT KODE_MESIN AS KDMESIN, TRIM(NAMA_MESIN) AS MESIN FROM V_MESIN_01
        ";

        $bind = array();

        if (!empty($q)) {
            $sql .= " WHERE UPPER(NAMA_MESIN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(NAMA_MESIN)";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'    => $row->KDMESIN,
                'text'  => $row->MESIN,
                'kode'  => $row->KDMESIN,
                'nama'  => $row->MESIN
            );
        }

        return array('results' => $data);
    }

    public function getDataPlp($thn, $bln, $mesin)
    {
        $sql = "
            SELECT
                KODE_PLP,
                TANGGAL_PLP ,
                REQUEST,
                TO_CHAR(WAKTU_REQ, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_REQ,
                TO_CHAR(WAKTU_PERENCANAAN, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_PERENCANAAN,
                TO_CHAR(WAKTU_START, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_START,
                TO_CHAR(WAKTU_FINISH, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_FINISH,
                WAKTU_PROSES
            FROM V_PLP_TEKNIK
            WHERE TAHUN = ?
            AND BULAN = ?
            AND KODE_MESIN = ?
            ORDER BY TANGGAL_PLP, WAKTU_START
        ";

        return $this->db->query($sql, [$thn, $bln, $mesin])->result();
    }

    public function getDataLhp($thn, $bln, $mesin)
    {
        $sql = "
            SELECT
                TO_CHAR(TANGGAL, 'YYYY-MM-DD HH24:MI:SS') AS TANGGAL,
                NOMOR_KK,
                PRODUK,
                SHIFT_,
                KATEGORI,
                KEGIATAN,
                TO_CHAR(JAM1, 'YYYY-MM-DD HH24:MI:SS') AS JAM1,
                TO_CHAR(JAM2, 'YYYY-MM-DD HH24:MI:SS') AS JAM2,
                WAKTU_BLT
            FROM VOEE_MONITORING
            WHERE THN = ?
            AND BLN_ = ?
            AND KDMESIN = ?
            AND KATEGORI = 'LAIN-LAIN (TEKNISI)'
            ORDER BY TANGGAL, JAM1
        ";

        return $this->db->query($sql, [$thn, $bln, $mesin])->result();
    }
}
