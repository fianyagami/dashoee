<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeeunit_mod extends CI_Model
{

    /**
     * Whitelist mapping Tahun -> nama View.
     * Kalau tahun belum ada view-nya, fallback ke tabel utama VOEE_MONITORING.
     */
    private function getViewName($thn)
    {
        $map = array(
            '2025' => 'VOEE_MONITORING_25',
            '2026' => 'VOEE_MONITORING_26',
        );

        $thn = (string) $thn;

        if (!isset($map[$thn])) {
            log_message('error', 'Dashoeeunit_mod: tahun ' . $thn . ' belum punya view, fallback ke VOEE_MONITORING');
            return 'VOEE_MONITORING';
        }

        return $map[$thn];
    }

    public function getSummaryOEE($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        WITH base AS (
            SELECT
                m.TANGGAL, m.KDMESIN, m.KEGIATAN, m.SHIFT_, m.PRODUK, m.KODE_ROLLS,
                m.NOMOR_LHP, m.NO_URUT_DETAIL, m.JAM1, m.JAM2,
                m.KATEGORI, m.KTG_LOSSTIME, m.WAKTU_BLT,
                m.OUTPUT, m.BAIK, m.RUSAK, m.TARGET,
                CASE
                    WHEN TRIM(UPPER(m.KEGIATAN)) = 'ISHOMA'
                        AND TO_CHAR(m.TANGGAL, 'D') = '6'
                        AND m.SHIFT_ = '1'
                    THEN 1.5
                    ELSE lp.LIMITPLAN
                END AS LIMITPLAN,
                lp.PAR_LIMITPLAN,
                CASE
                    WHEN lp.PAR_LIMITPLAN = 'DAY' THEN m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN 
                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ 
                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|PRODUK|' || m.PRODUK 
                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|KODE_ROLLS|' || m.KODE_ROLLS                     
                    ELSE m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|ROW|' || m.NOMOR_LHP || '|' || m.NO_URUT_DETAIL 
                END AS GROUP_LIMIT_KEY 
            FROM
                {$view} m
                LEFT JOIN VOEE_LIMITPLAN lp ON lp.KDMESIN = m.KDMESIN 
                AND TRIM( UPPER( lp.KEGIATAN ) ) = TRIM( UPPER( m.KEGIATAN ) ) 
            WHERE
                m.THN = ?
                AND m.BLN_ = ?
                AND m.NAMA_DEPARTEMEN != 'PACKING'
        ),
        calc AS (
            SELECT
                b.*,
            SUM( CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL THEN b.WAKTU_BLT ELSE 0 END ) OVER ( PARTITION BY b.GROUP_LIMIT_KEY ORDER BY b.JAM1, b.JAM2, b.NO_URUT_DETAIL ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW ) AS CUM_WAKTU,
            SUM( CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL THEN b.WAKTU_BLT ELSE 0 END ) OVER ( PARTITION BY b.GROUP_LIMIT_KEY ORDER BY b.JAM1, b.JAM2, b.NO_URUT_DETAIL ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING ) AS CUM_WAKTU_PREV 
            FROM
                base b 
        ),
        split_data AS (
            SELECT
                c.*,
                CASE
                    WHEN c.KTG_LOSSTIME = 'PLANNED' 
                    AND c.LIMITPLAN IS NOT NULL THEN
                        GREATEST( LEAST( c.LIMITPLAN - NVL( c.CUM_WAKTU_PREV, 0 ), c.WAKTU_BLT ), 0 ) ELSE c.WAKTU_BLT 
                        END AS WAKTU_PLANNED_FIX,
                CASE
                        WHEN c.KTG_LOSSTIME = 'PLANNED' 
                        AND c.LIMITPLAN IS NOT NULL THEN
                            GREATEST(
                                c.WAKTU_BLT - GREATEST( LEAST( c.LIMITPLAN - NVL( c.CUM_WAKTU_PREV, 0 ), c.WAKTU_BLT ), 0 ),
                                0 
                            ) ELSE 0 
                        END AS WAKTU_UNPLANNED_FIX 
            FROM
                calc c 
        ),
        data_fix AS (
            SELECT
                KATEGORI, KTG_LOSSTIME,
                WAKTU_PLANNED_FIX AS WAKTU_BLT_2,
                BAIK, RUSAK, OUTPUT, TARGET
            FROM
                split_data 
            WHERE
                WAKTU_PLANNED_FIX > 0 

            UNION ALL

            SELECT
                KATEGORI,
                'UNPLANNED' AS KTG_LOSSTIME,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT_2,
                BAIK, RUSAK, OUTPUT, TARGET
            FROM
                split_data 
            WHERE
                WAKTU_UNPLANNED_FIX > 0 
        ) 
        SELECT
            ROUND(
                (
                SUM( CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT_2 ELSE 0 END ) / NULLIF( SUM( WAKTU_BLT_2 ) - SUM( CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT_2 ELSE 0 END ), 0 ) 
            ) * 100,
            2 
            ) AS AR,
            ROUND(
                (
                ( SUM( OUTPUT ) / NULLIF( SUM( CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT_2 ELSE 0 END ), 0 ) ) / NULLIF( AVG( TARGET ), 0 ) 
            ) * 100,
            2 
            ) AS PR,
            ROUND( ( SUM( BAIK ) / NULLIF( SUM( OUTPUT ), 0 ) ) * 100, 2 ) AS QR,
            SUM( BAIK ) AS BAIK,
            SUM( RUSAK ) AS RUSAK,
            SUM( OUTPUT ) AS OUTPUT,
            AVG( TARGET ) AS TARGET_KK,
            SUM( CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT_2 ELSE 0 END ) AS WAKTU_PRO,
            SUM( WAKTU_BLT_2 ) AS WAKTU_ALL,
            SUM( CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT_2 ELSE 0 END ) AS WAKTU_PLANNED,
            85 AS TARGET_AR,
            85 AS TARGET_PR,
            98 AS TARGET_QR 
        FROM
            data_fix
        ";

        $bind = array($thn, $bln);

        $query = $this->db->query($sql, $bind);
        $row = $query->row_array();

        $ar = (float) $row['AR'];
        $pr = (float) $row['PR'];
        $qr = (float) $row['QR'];

        $row['OEE'] = round(($ar * $pr * $qr) / 10000, 2);

        return $row;
    }

    public function getTopDowntime($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        WITH base AS (
            SELECT
                m.TANGGAL, m.KDMESIN, m.KEGIATAN, m.SHIFT_, m.PRODUK, m.KODE_ROLLS,
                m.NOMOR_LHP, m.NO_URUT_DETAIL, m.JAM1, m.JAM2,
                m.KATEGORI, m.KTG_LOSSTIME, m.WAKTU_BLT,
                CASE
                    WHEN TRIM(UPPER(m.KEGIATAN)) = 'ISHOMA'
                        AND TO_CHAR(m.TANGGAL, 'D') = '6'
                        AND m.SHIFT_ = '1'
                    THEN 1.5
                    ELSE lp.LIMITPLAN
                END AS LIMITPLAN,
                lp.PAR_LIMITPLAN,
                CASE
                    WHEN lp.PAR_LIMITPLAN = 'DAY' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN))                   
                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_
                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_ || '|PRODUK|' || m.PRODUK
                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_ || '|KODE_ROLLS|' || m.KODE_ROLLS
                    ELSE
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|ROW|' || m.NOMOR_LHP || '|' || m.NO_URUT_DETAIL
                END AS GROUP_LIMIT_KEY
            FROM {$view} m
            LEFT JOIN VOEE_LIMITPLAN lp
                ON lp.KDMESIN = m.KDMESIN
               AND TRIM(UPPER(lp.KEGIATAN)) = TRIM(UPPER(m.KEGIATAN))
            WHERE 
                m.THN = ?
                AND m.BLN_ = ?
                AND m.NAMA_DEPARTEMEN != 'PACKING'
        ),
        calc AS (
            SELECT
                b.*,
                SUM(CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL
                         THEN b.WAKTU_BLT ELSE 0 END)
                    OVER (PARTITION BY b.GROUP_LIMIT_KEY
                          ORDER BY b.JAM1, b.JAM2, b.NO_URUT_DETAIL
                          ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS CUM_WAKTU,
                SUM(CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL
                         THEN b.WAKTU_BLT ELSE 0 END)
                    OVER (PARTITION BY b.GROUP_LIMIT_KEY
                          ORDER BY b.JAM1, b.JAM2, b.NO_URUT_DETAIL
                          ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING) AS CUM_WAKTU_PREV
            FROM base b
        ),
        split_data AS (
            SELECT
                c.*,
                CASE
                    WHEN c.KTG_LOSSTIME = 'PLANNED'
                     AND c.LIMITPLAN IS NOT NULL
                    THEN
                        GREATEST(
                            LEAST(
                                c.LIMITPLAN - NVL(c.CUM_WAKTU_PREV, 0),
                                c.WAKTU_BLT
                            ),
                            0
                        )
                    ELSE c.WAKTU_BLT
                END AS WAKTU_PLANNED_FIX,

                CASE
                    WHEN c.KTG_LOSSTIME = 'PLANNED'
                     AND c.LIMITPLAN IS NOT NULL
                    THEN
                        GREATEST(
                            c.WAKTU_BLT -
                            GREATEST(
                                LEAST(
                                    c.LIMITPLAN - NVL(c.CUM_WAKTU_PREV, 0),
                                    c.WAKTU_BLT
                                ),
                                0
                            ),
                            0
                        )
                    ELSE 0
                END AS WAKTU_UNPLANNED_FIX

            FROM calc c
        ),

        data_fix AS (
            SELECT
                KEGIATAN, KATEGORI, KTG_LOSSTIME,
                WAKTU_PLANNED_FIX AS WAKTU_BLT
            FROM split_data
            WHERE WAKTU_PLANNED_FIX > 0

            UNION ALL

            SELECT
                'OVER - ' || KEGIATAN AS KEGIATAN,
                KATEGORI,
                'UNPLANNED' AS KTG_LOSSTIME,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT
            FROM split_data
            WHERE WAKTU_UNPLANNED_FIX > 0
        ),

        total_waktu AS (
            SELECT
                SUM(WAKTU_BLT) AS TOTAL_ALL,
                SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END) AS TOTAL_PLANNED,
                SUM(WAKTU_BLT) 
                    - SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END) AS DENOMINATOR
            FROM data_fix
        )

       SELECT *
        FROM (
            SELECT
                d.KEGIATAN,
                COUNT(*) AS FREQ_DOWNTIME,
                SUM(d.WAKTU_BLT) AS WAKTU_DOWNTIME,
                ROUND(
                    SUM(d.WAKTU_BLT) /
                    NULLIF(t.DENOMINATOR, 0) * 100, 
                    2
                ) AS PERSEN
            FROM data_fix d
            CROSS JOIN total_waktu t  
            WHERE d.KATEGORI <> 'PRODUKSI' AND d.KTG_LOSSTIME = 'UNPLANNED'
            GROUP BY d.KEGIATAN, t.DENOMINATOR
            ORDER BY PERSEN DESC
        )
        WHERE ROWNUM <= 5
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getTopDefect($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        SELECT *
        FROM (
            SELECT
                NVL(NAMA_WASTE, '-') AS KEGIATAN,
                DEFECT AS JUMLAH,
                NVL(SAT_HASIL_RUSAK, '-') AS SAT_HASIL_RUSAK,
                ROUND(DEFECT / SUM(DEFECT) OVER () * 100, 2) AS PERSEN
            FROM (
                SELECT
                    NAMA_WASTE,
                    SUM(RUSAK) AS DEFECT,
                    SAT_HASIL_RUSAK
                FROM {$view}
                WHERE 
                    THN = ?
                    AND BLN_ = ?
                    AND NAMA_DEPARTEMEN != 'PACKING'
                GROUP BY NAMA_WASTE, SAT_HASIL_RUSAK
            )
            ORDER BY DEFECT DESC
        )
        WHERE ROWNUM <= 5
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getActualTarget($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
            SELECT
                ROUND(SUM(OUTPUT) / SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 2) AS ACTUAL_OUTPUT,
                ROUND(AVG(TARGET), 2) AS TARGET_OUTPUT,
                ROUND(SUM(OUTPUT), 2) AS TOTAL_OUTPUT,
                ROUND(SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 2) AS TOTAL_WAKTU_PRODUKSI
            FROM {$view}
            WHERE 
                THN = ?
                AND BLN_ = ?
                AND NAMA_DEPARTEMEN != 'PACKING'
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->row_array();
    }

    public function getDetailModal($type, $thn, $bln)
    {
        switch ($type) {
            case 'AR':
                return $this->getDetailAR($thn, $bln);

            case 'QR':
                return $this->getDetailQR($thn, $bln);

            case 'PR':
                return $this->getDetailPR($thn, $bln);

            default:
                return array();
        }
    }

    private function getDetailAR($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        WITH base AS (
            SELECT
                m.THN, m.BLN_, m.NAMA_DEPARTEMEN, m.NOMOR_LHP, m.TANGGAL, m.NOMOR_KK,
                m.TANGGAL_KK, m.REVISI_KE, m.STATUS_KK, m.PRODUK, m.NOMER_PO_CUSTOMER,
                m.KODE_BARANG_PO, m.KODE_ROLLS, m.KODE_BARANG_BHN, m.NAMA_BARANG_BHN,
                m.KDMESIN, m.MESIN, m.URUT_PROSES, m.KODE_PROSES, m.PROSES, m.SHIFT_,
                m.NO_URUT_DETAIL, m.KATEGORI, m.KEGIATAN, m.GRUP2, m.KTG_LOSSTIME,
                m.JAM1, m.JAM2, m.WAKTU_BLT, m.BAIK, m.SAT_HASIL_BAIK, m.KODE_WASTE,
                m.NAMA_WASTE, m.RUSAK, m.SAT_HASIL_RUSAK, m.OUTPUT, m.TARGET, m.SAT_TARGET,
                CASE
                    WHEN TRIM(UPPER(m.KEGIATAN)) = 'ISHOMA'
                        AND TO_CHAR(m.TANGGAL, 'D') = '6'
                        AND m.SHIFT_ = '1'
                    THEN 1.5
                    ELSE lp.LIMITPLAN
                END AS LIMITPLAN,
                lp.PAR_LIMITPLAN,
                CASE
                    WHEN lp.PAR_LIMITPLAN = 'DAY' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN))                   
                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_
                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_ || '|PRODUK|' || m.PRODUK
                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' || m.SHIFT_ || '|KODE_ROLLS|' || m.KODE_ROLLS
                    ELSE
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' || m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|ROW|' || m.NOMOR_LHP || '|' || m.NO_URUT_DETAIL
                END AS GROUP_LIMIT_KEY
            FROM
                {$view} m
                LEFT JOIN VOEE_LIMITPLAN lp
                    ON lp.KDMESIN = m.KDMESIN
                    AND TRIM(UPPER(lp.KEGIATAN)) = TRIM(UPPER(m.KEGIATAN))
            WHERE 
                m.THN = ?
                AND m.BLN_ = ?
                AND m.NAMA_DEPARTEMEN != 'PACKING'
        ),
        calc AS (
            SELECT
                b.*,
                SUM(CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL
                         THEN b.WAKTU_BLT ELSE 0 END)
                    OVER (PARTITION BY b.GROUP_LIMIT_KEY
                          ORDER BY b.JAM1, b.JAM2, b.NOMOR_LHP, b.NO_URUT_DETAIL
                          ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS CUM_WAKTU,
                SUM(CASE WHEN b.KTG_LOSSTIME = 'PLANNED' AND b.LIMITPLAN IS NOT NULL
                         THEN b.WAKTU_BLT ELSE 0 END)
                    OVER (PARTITION BY b.GROUP_LIMIT_KEY
                          ORDER BY b.JAM1, b.JAM2, b.NOMOR_LHP, b.NO_URUT_DETAIL
                          ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING) AS CUM_WAKTU_PREV
            FROM
                base b
        ),
        split_data AS (
            SELECT
                c.*,
                CASE
                    WHEN c.KTG_LOSSTIME = 'PLANNED' AND c.LIMITPLAN IS NOT NULL THEN
                        GREATEST(LEAST(c.LIMITPLAN - NVL(c.CUM_WAKTU_PREV, 0), c.WAKTU_BLT), 0)
                    ELSE
                        c.WAKTU_BLT
                END AS WAKTU_PLANNED_FIX,
                CASE
                    WHEN c.KTG_LOSSTIME = 'PLANNED' AND c.LIMITPLAN IS NOT NULL THEN
                        GREATEST(
                            c.WAKTU_BLT - GREATEST(LEAST(c.LIMITPLAN - NVL(c.CUM_WAKTU_PREV, 0), c.WAKTU_BLT), 0),
                            0
                        )
                    ELSE
                        0
                END AS WAKTU_UNPLANNED_FIX
            FROM
                calc c
        )
        SELECT
            NOMOR_LHP, NOMOR_KK, TANGGAL, NO_URUT_DETAIL, MESIN, PROSES, PRODUK, SHIFT_,
            KEGIATAN, KTG_LOSSTIME, JAM1, JAM2,
            WAKTU_BLT_ASLI, WAKTU_BLT, LIMITPLAN, PAR_LIMITPLAN
        FROM (
            SELECT
                NOMOR_LHP, NOMOR_KK, TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL, NO_URUT_DETAIL, MESIN, PROSES, PRODUK, SHIFT_,
                KEGIATAN, KTG_LOSSTIME, 
                TO_CHAR(JAM1, 'YYYY-MM-DD HH24:MI:SS') AS JAM1, 
                TO_CHAR(JAM2, 'YYYY-MM-DD HH24:MI:SS') AS JAM2,
                WAKTU_BLT AS WAKTU_BLT_ASLI,
                WAKTU_PLANNED_FIX AS WAKTU_BLT,
                LIMITPLAN, PAR_LIMITPLAN,
                1 AS URUT_DATA
            FROM split_data
            WHERE WAKTU_PLANNED_FIX > 0

            UNION ALL

            SELECT
                NOMOR_LHP, NOMOR_KK, TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL, NO_URUT_DETAIL, MESIN, PROSES, PRODUK, SHIFT_,
                'OVER - ' || KEGIATAN AS KEGIATAN,
                'UNPLANNED' AS KTG_LOSSTIME,
                TO_CHAR(JAM1, 'YYYY-MM-DD HH24:MI:SS') AS JAM1, 
                TO_CHAR(JAM2, 'YYYY-MM-DD HH24:MI:SS') AS JAM2,
                WAKTU_BLT AS WAKTU_BLT_ASLI,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT,
                LIMITPLAN, PAR_LIMITPLAN,
                2 AS URUT_DATA
            FROM split_data
            WHERE WAKTU_UNPLANNED_FIX > 0
        )
        ORDER BY NOMOR_LHP, MESIN, PROSES, SHIFT_, NO_URUT_DETAIL, URUT_DATA
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->result_array();
    }

    private function getDetailQR($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        SELECT
            NOMOR_LHP,
            NOMOR_KK,
            TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL,
            NO_URUT_DETAIL,
            MESIN,
            PROSES,
            PRODUK,
            SHIFT_,
            KEGIATAN,
            BAIK,
            SAT_HASIL_BAIK,
            KODE_WASTE,
            NAMA_WASTE,
            RUSAK,
            SAT_HASIL_RUSAK,
            OUTPUT
        FROM {$view}
        WHERE 
            THN = ?
            AND BLN_ = ?
            AND NAMA_DEPARTEMEN != 'PACKING'
        ORDER BY NOMOR_LHP, MESIN, SHIFT_, NO_URUT_DETAIL
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->result_array();
    }

    private function getDetailPR($thn, $bln)
    {
        $view = $this->getViewName($thn);

        $sql = "
        SELECT
            TO_CHAR(TANGGAL, 'DD/MM/YYYY')         AS TANGGAL,
            NOMOR_KK,
            SHIFT_,
            MESIN,
            PRODUK,
            PROSES,
            SUM(BAIK + RUSAK)                       AS TOTAL_OUTPUT,
            SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END)
                                                    AS WAKTU_PRODUKSI,
            ROUND(AVG(TARGET), 4)                   AS AVG_TARGET,
            ROUND(
                (
                    SUM(BAIK + RUSAK)
                    /
                    NULLIF(SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 0)
                )
                /
                NULLIF(AVG(TARGET), 0)
            * 100
            , 2)                                    AS PR
        FROM {$view}
        WHERE 
            THN = ?
            AND BLN_ = ?
            AND NAMA_DEPARTEMEN != 'PACKING'
        GROUP BY
            TO_CHAR(TANGGAL, 'DD/MM/YYYY'),
            TANGGAL,
            NOMOR_KK,
            SHIFT_,
            MESIN,
            PRODUK,
            PROSES
        ORDER BY
            TANGGAL, SHIFT_, MESIN, PROSES
        ";

        $bind = array($thn, $bln);

        return $this->db->query($sql, $bind)->result_array();
    }
}
