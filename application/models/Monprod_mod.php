<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monprod_mod extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getMesin($q = null)
    {
        $sql = "
            SELECT KODE_MESIN AS KDMESIN, TRIM(NAMA_MESIN) AS MESIN FROM V_MESIN_01
        ";

        $bind = array();

        if (!empty($q)) {
            $sql .= " WHERE UPPER(NAMA_MESIN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
            // $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(NAMA_MESIN) ";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'      => $row->KDMESIN,
                'text'    => $row->MESIN,
                'kdmesin' => $row->KDMESIN,
                'mesin'   => $row->MESIN
            );
        }

        return array('results' => $data);
    }


    public function getDetail($thn, $bln, $mesin)
    {
        $sql = "
            SELECT
                TANGGAL,
                TO_CHAR(TANGGAL, 'YYYY-MM-DD HH24:MI:SS') AS TANGGAL_PARAM,
                SHIFT_,
                NOMOR_KK,
                PRODUK,
                PROSES,
                TARGET,
                SAT_TARGET,

                SUM(
                    CASE
                        WHEN KEGIATAN = 'PRODUKSI MURNI'
                        THEN WAKTU_BLT
                        ELSE 0
                    END
                ) AS WAKTU_PROD,

                SUM(
                    CASE
                        WHEN KEGIATAN != 'PRODUKSI MURNI'
                        THEN WAKTU_BLT
                        ELSE 0
                    END
                ) AS WAKTU_NON_PROD,

                SUM(WAKTU_BLT) AS WAKTU_TOTAL,

                SUM(BAIK) AS BAIK,
                SUM(RUSAK) AS RUSAK,
                SUM(OUTPUT) AS OUTPUT,

                COALESCE(
                    MAX(SAT_HASIL_BAIK),
                    MAX(SAT_HASIL_RUSAK)
                ) AS SAT_HASIL_OUTPUT

            FROM VOEE_MONITORING

            WHERE THN = ?
            AND BLN_ = ?
            AND KDMESIN = ?

            GROUP BY
                TANGGAL,
                SHIFT_,
                NOMOR_KK,
                PRODUK,
                PROSES,
                TARGET,
                SAT_TARGET

            ORDER BY
                TANGGAL,
                SHIFT_,
                NOMOR_KK
        ";

        return $this->db->query($sql, [
            $thn,
            $bln,
            $mesin
        ])->result();
    }

    public function get_detail_waktu($mesin, $tanggal, $shift, $nomor_kk, $proses)
    {
        $sql = "
        SELECT
            NOMOR_LHP,
            NO_URUT_DETAIL,
            KEGIATAN,
            KTG_LOSSTIME,
            TO_CHAR(JAM1, 'YYYY-MM-DD HH24:MI:SS') AS JAM1,
            TO_CHAR(JAM2, 'YYYY-MM-DD HH24:MI:SS') AS JAM2,
            WAKTU_BLT,
            BAIK,
            SAT_HASIL_BAIK,
            NAMA_WASTE,
            RUSAK AS RUSAK,
            SAT_HASIL_RUSAK,
            OUTPUT,
            TRIM(NAMA1) || 
                CASE WHEN NAMA2 IS NOT NULL THEN ', ' || TRIM(NAMA2) ELSE '' END ||
                CASE WHEN NAMA3 IS NOT NULL THEN ', ' || TRIM(NAMA3) ELSE '' END AS OPERATOR,
            PENGAWAS
        FROM VOEE_MONITORING
        WHERE KDMESIN = ?
          AND TANGGAL = TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
          AND SHIFT_ = ?
          AND NOMOR_KK = ?
          AND PROSES = ?
        ORDER BY NOMOR_LHP ASC, NO_URUT_DETAIL ASC
    ";

        return $this->db->query($sql, array(
            $mesin,
            $tanggal,
            $shift,
            $nomor_kk,
            $proses
        ))->result();
    }
}
