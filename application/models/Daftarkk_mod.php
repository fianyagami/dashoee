<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Daftarkk_mod extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDataKKhead($thn)
    {
        $sql = "
        SELECT 
        NOMOR_KK, 
        NO_BAPOB, 
        NOMER_PO_CUSTOMER, 
        STAT_KK, 
        TANGGAL_KK, 
        TO_CHAR(TANGGAL_KK, 'DD-MM-YYYY') AS TANGGAL_KK_CHAR, 
        TO_CHAR(TANGGAL_KK, 'YYYY-MM-DD') AS TANGGAL_KK_PARAM,
        CUSTOMER, 
        NAMA_BARANG, 
        NAMA_KATEGORI, 
        OPLAAG_PO, 
        SATUAN_OPLAAG, 
        ARSIP_STATUS, 
        NAMA, 
        NAMA2
        FROM V_KK_ALL_FILE
        WHERE TAHUN = ?
        ORDER BY TANGGAL_KK DESC ";
        $query = $this->db->query($sql, array($thn));
        return $query->result();
    }

    public function getDataKKdetail($thn, $nokk, $tgl_kk)
    {
        $sql = "
        SELECT
        URUT, 
        URUT_FLOW, 
        NAMA_PROSES, 
        NAMA_MESIN, 
        WASTE_PROSES, 
        TARGET, 
        SAT_TARGET, 
        JENIS_BAHAN, 
        NAMA_BARANG, 
        JUMLAH_OR_UKURAN, 
        NAMA_HASIL
        FROM V_KK_ALL_DETAIL WHERE NOMOR_KK = ? AND TAHUN = ?  AND TRUNC(TANGGAL_KK) = TO_DATE(?, 'YYYY-MM-DD')";
        $query = $this->db->query($sql, array($nokk, $thn, $tgl_kk));
        return $query->result();
    }
}
