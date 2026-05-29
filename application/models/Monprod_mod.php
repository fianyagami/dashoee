<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monprod_mod extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getMesin($thn, $bln)
    {
        $sql = "

        SELECT DISTINCT
            NAMA_DEPARTEMEN,
            MESIN

        FROM VOEE_MONITORING

        WHERE THN = ?
        AND BLN_ = ?

        ORDER BY
            NAMA_DEPARTEMEN,
            MESIN

    ";

        return $this->db->query($sql, [
            $thn,
            $bln
        ])->result();
    }

    // public function getDetail($thn, $bln, $dept, $mesin)
    // {
    //     $sql = "
    //         SELECT
    //             TANGGAL,
    //             SHIFT_,
    //             NOMOR_KK,
    //             PRODUK,
    //             PROSES,
    //             TARGET,
    //             SAT_TARGET,

    //             SUM(
    //                 CASE
    //                     WHEN KEGIATAN = 'PRODUKSI MURNI'
    //                     THEN WAKTU_BLT
    //                     ELSE 0
    //                 END
    //             ) AS WAKTU_PROD,

    //             SUM(
    //                 CASE
    //                     WHEN KEGIATAN != 'PRODUKSI MURNI'
    //                     THEN WAKTU_BLT
    //                     ELSE 0
    //                 END
    //             ) AS WAKTU_NON_PROD,

    //             SUM(WAKTU_BLT) AS WAKTU_TOTAL,

    //             SUM(BAIK) AS BAIK,
    //             SUM(RUSAK) AS RUSAK,
    //             SUM(OUTPUT) AS OUTPUT,

    //             COALESCE(
    //                 MAX(SAT_HASIL_BAIK),
    //                 MAX(SAT_HASIL_RUSAK)
    //             ) AS SAT_HASIL_OUTPUT

    //         FROM VOEE_MONITORING

    //         WHERE THN = ?
    //         AND BLN_ = ?
    //         AND NAMA_DEPARTEMEN = ?
    //         AND MESIN = ?

    //         GROUP BY
    //             TANGGAL,
    //             SHIFT_,
    //             NOMOR_KK,
    //             PRODUK,
    //             PROSES,
    //             TARGET,
    //             SAT_TARGET

    //         ORDER BY
    //             TANGGAL,
    //             SHIFT_,
    //             NOMOR_KK
    //     ";

    //     return $this->db->query($sql, [
    //         $thn,
    //         $bln,
    //         $dept,
    //         $mesin
    //     ])->result();
    // }

    public function getDetail($thn, $bln, $mesin)
    {
        $sql = "
            SELECT
                TANGGAL,
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
}
