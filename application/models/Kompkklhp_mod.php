<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kompkklhp_mod extends CI_Model
{
    public function getHeaderKK($thn, $q = null)
    {
        $sql = "
            SELECT
                NOMOR_KK,
                TANGGAL_KK,
                NAMA_BARANG,
                CUSTOMER,
                REVISI_KE,
                STATUS_KK
            FROM V_KK_ALL_FILE
            WHERE TAHUN = ?
        ";

        $bind = [$thn];

        if (!empty($q)) {
            $sql .= " AND (
                UPPER(NOMOR_KK) LIKE UPPER(?)
                OR UPPER(NAMA_BARANG) LIKE UPPER(?)
            )";

            $like = '%' . $q . '%';
            $bind[] = $like;
            $bind[] = $like;
        }

        $sql .= " ORDER BY NOMOR_KK";

        return $this->db->query($sql, $bind)->result();
    }

    public function getProsesKK($nomor_kk, $tahun, $tanggal_kk)
    {
        $sql = "
            SELECT
                URUT,
                URUT_FLOW,
                NAMA_PROSES,
                NAMA_MESIN,
                WASTE_PROSES,
                TARGET,
                SAT_TARGET
            FROM V_KK_ALL_DETAIL
            WHERE NOMOR_KK = ?
              AND TAHUN = ?
              AND TRUNC(TANGGAL_KK) = TO_DATE(?, 'YYYY-MM-DD')
            GROUP BY
                URUT,
                URUT_FLOW,
                NAMA_PROSES,
                NAMA_MESIN,
                WASTE_PROSES,
                TARGET,
                SAT_TARGET
            ORDER BY URUT, URUT_FLOW
        ";

        return $this->db->query($sql, [$nomor_kk, $tahun, $tanggal_kk])->result();
    }

    public function getProsesLHP($nomor_kk, $tanggal_kk, $revisi_ke, $status_kk)
    {
        $sql = "
            SELECT
                URUT_PROSES,
                PROSES,
                TOT_WAKTU,
                TOT_BAIK,
                TOT_RUSAK
            FROM VOEE_LHP_PROSES
            WHERE NOMOR_KK = ?
              AND TRUNC(TANGGAL_KK) = TO_DATE(?, 'YYYY-MM-DD')
              AND REVISI_KE = ?
              AND STATUS_KK = ?
            ORDER BY URUT_PROSES
        ";

        return $this->db->query($sql, [$nomor_kk, $tanggal_kk, $revisi_ke, $status_kk])->result();
    }
}
