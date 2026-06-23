<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kompbapobkk_mod extends CI_Model
{
    public function getHeaderKK($thn, $q = null)
    {
        $sql = "
            SELECT 
                NOMOR_KK,
                TANGGAL_KK,
                NO_BAPOB,
                TO_CHAR( TANGGAL_BAPOB, 'DD/MM/YYYY' ) AS TANGGAL_BAPOB,
                extract( year FROM TANGGAL_BAPOB ) AS TAHUN_BAPOB,
                NAMA_BARANG,
                CUSTOMER 
            FROM V_KK_ALL_FILE
            WHERE TAHUN = ?
        ";

        $bind = [$thn];

        if (!empty($q)) {
            $sql .= " AND (
                UPPER(NOMOR_KK) LIKE UPPER(?) 
                OR UPPER(NAMA_BARANG) LIKE UPPER(?)
                OR UPPER(NO_BAPOB) LIKE UPPER(?)
            )";

            $like = '%' . $q . '%';
            $bind[] = $like;
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

    // public function getHeaderBAPOB($thn, $tgl_bapob, $q = null)
    // {
    //     $sql = "
    //     SELECT *
    //     FROM (
    //         SELECT 
    //             NO_BAPOB,
    //             PRODUK,
    //             DIBUAT,
    //             KODE_TRANSAKSI,
    //             TANGGAL,
    //             CUSTOMER
    //         FROM VALL_BAPOB_HEAD
    //         WHERE TAHUN = ? AND TANGGAL = ?
    // ";

    //     $bind = [$thn, $tgl_bapob];

    //     if (!empty($q)) {
    //         $sql .= "
    //         AND (
    //             UPPER(NO_BAPOB) LIKE UPPER(?)
    //             OR UPPER(PRODUK) LIKE UPPER(?)
    //         )
    //     ";

    //         $like = '%' . $q . '%';
    //         $bind[] = $like;
    //         $bind[] = $like;
    //     }

    //     $sql .= "
    //         ORDER BY NO_BAPOB
    //     )
    //     WHERE ROWNUM <= 50
    // ";

    //     return $this->db->query($sql, $bind)->result();
    // }

    public function getHeaderBAPOB($thn, $tgl_bapob, $customer = null, $q = null)
    {
        $sql = "
        SELECT *
        FROM (
            SELECT
                NO_BAPOB,
                PRODUK,
                DIBUAT,
                KODE_TRANSAKSI,
                TANGGAL,
                CUSTOMER
            FROM VALL_BAPOB_HEAD
            WHERE TAHUN = ? AND TANGGAL = ?
    ";

        $bind = [$thn, $tgl_bapob];

        if (!empty($q)) {
            $sql .= "
            AND (
                UPPER(NO_BAPOB) LIKE UPPER(?)
                OR UPPER(PRODUK) LIKE UPPER(?)
            )
        ";

            $like = '%' . $q . '%';
            $bind[] = $like;
            $bind[] = $like;
        }

        if (!empty($customer)) {

            $cust = strtoupper($customer);
            $cust = str_replace(['.', ',', 'CV', 'PT', 'UD', 'PD', 'TBK'], ' ', $cust);
            $cust = preg_replace('/\s+/', ' ', $cust);
            $cust = trim($cust);

            $sql .= "
            AND (
                TRIM(
                    REGEXP_REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(UPPER(CUSTOMER), '.', ' '),
                                            ',', ' '),
                                        'CV', ' '),
                                    'PT', ' '),
                                'UD', ' '),
                            'PD', ' '),
                        'TBK', ' '),
                    '\s+', ' ')
                ) LIKE ?
                OR ? LIKE '%' ||
                    TRIM(
                        REGEXP_REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    REPLACE(UPPER(CUSTOMER), '.', ' '),
                                                ',', ' '),
                                            'CV', ' '),
                                        'PT', ' '),
                                    'UD', ' '),
                                'PD', ' '),
                            'TBK', ' '),
                        '\s+', ' ')
                    ) || '%'
            )
        ";

            $bind[] = '%' . $cust . '%';
            $bind[] = $cust;
        }

        $sql .= "
            ORDER BY NO_BAPOB
        )
        WHERE ROWNUM <= 50
    ";

        return $this->db->query($sql, $bind)->result();
    }

    public function getProsesBAPOBport($kode_transaksi, $tanggal_bapob)
    {
        $sql = "
            SELECT 
                URUT_SUB,
                NAMA_SUB,
                TANGGAL_BAPOB,
                URUTAN_PROSES,
                NAMA_PROSES,
                NAMA_MESIN_S,
                TARGET_SPEED_S
            FROM V_BAPOB_DETAIL_1
            WHERE ID_BAPOB_HEAD = ? AND TANGGAL_BAPOB = ?
            ORDER BY URUT_SUB, URUTAN_PROSES
        ";

        return $this->db->query($sql, [$kode_transaksi, $tanggal_bapob])->result();
    }

    public function getProsesBAPOBweb($no_bapob, $kode_transaksi, $tanggal_bapob)
    {
        $sql = "
            SELECT 
                URUT_SUB,
                NAMA_SUB,
                TANGGAL_BAPOB,
                URUTAN_PROSES,
                NAMA_PROSES,
                NAMA_MESIN_S,
                TARGET_SPEED_S
            FROM V_BAPOB_DETAIL_2
            WHERE NO_BAPOB = ?
              AND KODE_TRANSAKSI = ?
              AND TANGGAL_BAPOB = ?
            ORDER BY URUT_SUB, URUTAN_PROSES
        ";

        return $this->db->query($sql, [$no_bapob, $kode_transaksi, $tanggal_bapob])->result();
    }
}
