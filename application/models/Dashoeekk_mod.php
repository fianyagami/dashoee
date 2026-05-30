<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeekk_mod extends CI_Model
{

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

    public function getKK($thn_kk, $q = null)
    {
        $sql = "
            SELECT 
                TAHUN, 
                NOMOR_KK, 
                TO_CHAR(TANGGAL_KK, 'YYYY-MM-DD HH24:MI:SS') AS TANGGAL_KK, 
                NAMA_BARANG
            FROM V_KK_ALL
            WHERE TAHUN = ?
        ";

        $bind = array($thn_kk);

        if (!empty($q)) {
            $sql .= " AND (
                UPPER(NOMOR_KK) LIKE UPPER(?) 
                OR UPPER(NAMA_BARANG) LIKE UPPER(?)
            ) ";
            $bind[] = "%" . $q . "%";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TAHUN DESC, TANGGAL_KK DESC ";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'         => $row->NOMOR_KK,
                'text'       => $row->NOMOR_KK . ' - ' . $row->NAMA_BARANG,
                'nomor_kk'   => $row->NOMOR_KK,
                'tanggal_kk' => $row->TANGGAL_KK,
                'nama_barang' => $row->NAMA_BARANG
            );
        }

        return array('results' => $data);
    }

    public function getSummaryOEE($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
            SELECT
                ROUND(
                    (
                        SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END)
                        /
                        NULLIF(
                            SUM(WAKTU_BLT) -
                            SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END)
                        , 0)
                    ) * 100
                , 2) AS AR,

                ROUND(
                    (
                        (
                            SUM(OUTPUT)
                            /
                            NULLIF(SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 0)
                        )
                        /
                        NULLIF(AVG(TARGET), 0)
                    ) * 100
                , 2) AS PR,

                ROUND(
                    (
                        SUM(BAIK)
                        /
                        NULLIF(SUM(OUTPUT), 0)
                    ) * 100
                , 2) AS QR,

                SUM(BAIK) AS BAIK,
                SUM(RUSAK) AS RUSAK,
                SUM(OUTPUT) AS OUTPUT,
                AVG(TARGET) AS TARGET_KK,

                SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END) AS WAKTU_PRO,
                SUM(WAKTU_BLT) AS WAKTU_ALL,
                SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END) AS WAKTU_PLANNED,

                73 AS TARGET_AR,
                85 AS TARGET_PR,
                98 AS TARGET_QR

            FROM VOEE_MONITORING
            WHERE THN = ?
              AND BLN_ = ?
              AND KDMESIN = ?
              AND NOMOR_KK = ?
              AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";

        $query = $this->db->query($sql, array($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk));
        $row = $query->row_array();

        $ar = (float) $row['AR'];
        $pr = (float) $row['PR'];
        $qr = (float) $row['QR'];

        $row['OEE'] = round(($ar * $pr * $qr) / 10000, 2);

        return $row;
    }

    public function getTopDowntime($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
            SELECT *
            FROM (
                SELECT
                    KEGIATAN,
                    ROUND(
                        SUM(WAKTU_BLT) /
                        NULLIF(SUM(SUM(WAKTU_BLT)) OVER (), 0) * 100
                    , 2) AS PERSEN
                FROM VOEE_MONITORING
                WHERE THN = ?
                  AND BLN_ = ?
                  AND KDMESIN = ?
                  AND NOMOR_KK = ?
                  AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
                  AND KATEGORI <> 'PRODUKSI'
                GROUP BY KEGIATAN
                ORDER BY PERSEN DESC
            )
            WHERE ROWNUM <= 5
        ";

        return $this->db->query($sql, array($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk))->result_array();
    }

    public function getTopDefect($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
            SELECT
            NVL( NAMA_WASTE, '-' ) AS KEGIATAN,
            DEFECT AS JUMLAH,
            NVL( SAT_HASIL_RUSAK, '-' ) AS SAT_HASIL_RUSAK 
        FROM
            (
            SELECT
                NAMA_WASTE,
                SUM( RUSAK ) AS DEFECT,
                SAT_HASIL_RUSAK 
            FROM
                VOEE_MONITORING 
            WHERE
                THN = ?
                AND BLN_ = ?
                AND KDMESIN = ?
                AND NOMOR_KK = ?
                AND TRUNC( TANGGAL_KK ) = TRUNC( TO_DATE( ?, 'YYYY-MM-DD HH24:MI:SS' ) ) 
            GROUP BY
                NAMA_WASTE,
                SAT_HASIL_RUSAK 
            ORDER BY
                SUM( RUSAK ) DESC 
            ) 
        WHERE
            ROWNUM <= 5
        ";

        return $this->db->query($sql, array($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk))->result_array();
    }

    public function getActualTarget($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
            SELECT
                ROUND( SUM( BAIK ) / SUM( CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END ), 2 )  AS ACTUAL_OUTPUT,
                ROUND( AVG( TARGET ), 2 ) AS TARGET_OUTPUT
            FROM VOEE_MONITORING
            WHERE THN = ?
              AND BLN_ = ?
              AND KDMESIN = ?
              AND NOMOR_KK = ?
              AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";

        return $this->db->query($sql, array($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk))->row_array();
    }
}
