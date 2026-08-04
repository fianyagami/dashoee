<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monteknik_mod extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getDepartemen($q = null)
    {
        $sql = "
            SELECT KODE_DEPARTEMEN, TRIM(NAMA_DEPARTEMEN) AS NAMA_DEPARTEMEN
            FROM AMPHIBI.DEPARTEMEN_BARU
        ";

        $bind = array();

        if (!empty($q)) {
            $sql .= " WHERE UPPER(NAMA_DEPARTEMEN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(NAMA_DEPARTEMEN)";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'    => $row->KODE_DEPARTEMEN,
                'text'  => $row->NAMA_DEPARTEMEN,
                'kode'  => $row->KODE_DEPARTEMEN,
                'nama'  => $row->NAMA_DEPARTEMEN
            );
        }

        return array('results' => $data);
    }

    public function getMesin($q = null, $dept = null)
    {
        // Mesin wajib bergantung pada Departemen, kalau Departemen kosong jangan query
        if (empty($dept)) {
            return array('results' => array());
        }

        $sql = "
            SELECT KODE_MESIN, TRIM(NAMA_MESIN) AS NAMA_MESIN
            FROM AMPHIBI.MON_TKN_MS_MESIN
            WHERE KODE_DEPARTEMEN = ?
        ";

        $bind = array($dept);

        if (!empty($q)) {
            $sql .= " AND UPPER(NAMA_MESIN) LIKE UPPER(?)";
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

    public function getDetail($thn, $bln, $dept = null, $mesin = null)
    {
        // TO_CHAR(TANGGAL_PLP, 'YYYY-MM-DD HH24:MI:SS') AS TANGGAL_PLP,
        $sql = "
            SELECT
                KODE_PLP,
                TANGGAL_PLP,
                KODE_DEPARTEMEN,
                NAMA_DEPARTEMEN,
                KODE_MESIN,
                NAMA_MESIN,
                PELAPOR,
                JENIS_PEKERJAAN,
                REQUEST,
                TO_CHAR(WAKTU_REQ, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_REQ,
                TO_CHAR(WAKTU_PERENCANAAN, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_PERENCANAAN,
                TO_CHAR(WAKTU_START, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_START,
                TO_CHAR(WAKTU_FINISH, 'YYYY-MM-DD HH24:MI:SS') AS WAKTU_FINISH,
                WAKTU_PROSES,
                STATUS,
                KONFIRMASI
            FROM V_PLP_TEKNIK
            WHERE TAHUN = ?
            AND BULAN = ?
        ";

        $bind = array($thn, $bln);

        if (!empty($dept)) {
            $sql .= " AND KODE_DEPARTEMEN = ?";
            $bind[] = $dept;
        }

        if (!empty($mesin)) {
            $sql .= " AND KODE_MESIN = ?";
            $bind[] = $mesin;
        }

        $sql .= " ORDER BY TANGGAL_PLP DESC";

        return $this->db->query($sql, $bind)->result();
    }
}
