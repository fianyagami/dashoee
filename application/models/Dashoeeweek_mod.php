<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashoeeweek_mod extends CI_Model
{

    public function getSummaryOEE($tgl_awal, $tgl_akhir)
    {
        $sql = "
        WITH base AS (
            SELECT
                m.*,
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
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN 
                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ 
                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|PRODUK|' || m.PRODUK 
                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|KODE_ROLLS|' || m.KODE_ROLLS 
                    ELSE 
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|ROW|' || m.NOMOR_LHP || '|' || m.NO_URUT_DETAIL 
                END AS GROUP_LIMIT_KEY 
            FROM
                VOEE_MONITORING m
                LEFT JOIN VOEE_LIMITPLAN lp ON lp.KDMESIN = m.KDMESIN 
                AND TRIM( UPPER( lp.KEGIATAN ) ) = TRIM( UPPER( m.KEGIATAN ) ) 
            WHERE
                m.NAMA_DEPARTEMEN != 'PACKING' AND
                m.TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
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
                THN, BLN_, NAMA_DEPARTEMEN, NOMOR_LHP, TANGGAL, NOMOR_KK, TANGGAL_KK,
                REVISI_KE, STATUS_KK, PRODUK, NOMER_PO_CUSTOMER, KODE_BARANG_PO,
                KODE_ROLLS, KODE_BARANG_BHN, NAMA_BARANG_BHN, KDMESIN, MESIN,
                URUT_PROSES, KODE_PROSES, PROSES, SHIFT_, NO_URUT_DETAIL, KATEGORI,
                KEGIATAN, GRUP2, KTG_LOSSTIME, JAM1, JAM2, WAKTU_BLT,
                WAKTU_PLANNED_FIX AS WAKTU_BLT_2,
                BAIK, SAT_HASIL_BAIK, KODE_WASTE, NAMA_WASTE, RUSAK, SAT_HASIL_RUSAK,
                OUTPUT, TARGET, SAT_TARGET, LIMITPLAN, PAR_LIMITPLAN,
                'ORIGINAL' AS TIPE_DATA 
            FROM
                split_data 
            WHERE
                WAKTU_PLANNED_FIX > 0 

            UNION ALL

            SELECT
                THN, BLN_, NAMA_DEPARTEMEN, NOMOR_LHP, TANGGAL, NOMOR_KK, TANGGAL_KK,
                REVISI_KE, STATUS_KK, PRODUK, NOMER_PO_CUSTOMER, KODE_BARANG_PO,
                KODE_ROLLS, KODE_BARANG_BHN, NAMA_BARANG_BHN, KDMESIN, MESIN,
                URUT_PROSES, KODE_PROSES, PROSES, SHIFT_, NO_URUT_DETAIL, KATEGORI,
                'OVER - ' || KEGIATAN AS KEGIATAN, GRUP2,
                'UNPLANNED' AS KTG_LOSSTIME, JAM1, JAM2, WAKTU_BLT,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT_2,
                BAIK, SAT_HASIL_BAIK, KODE_WASTE, NAMA_WASTE, RUSAK, SAT_HASIL_RUSAK,
                OUTPUT, TARGET, SAT_TARGET, LIMITPLAN, PAR_LIMITPLAN,
                'GENERATE_LIMITPLAN' AS TIPE_DATA 
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

        $bind = array($tgl_awal, $tgl_akhir);

        $query = $this->db->query($sql, $bind);
        $row = $query->row_array();

        $ar = (float) $row['AR'];
        $pr = (float) $row['PR'];
        $qr = (float) $row['QR'];

        $row['OEE'] = round(($ar * $pr * $qr) / 10000, 2);

        return $row;
    }

    public function getTopDowntime($tgl_awal, $tgl_akhir)
    {
        $sql = "
        WITH base AS (
            SELECT
                m.*,
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
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' ||
                        m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) 

                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' ||
                        m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' ||
                        m.SHIFT_

                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' ||
                        m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' ||
                        m.SHIFT_ || '|PRODUK|' ||
                        m.PRODUK

                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' ||
                        m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|SHIFT|' ||
                        m.SHIFT_ || '|KODE_ROLLS|' ||
                        m.KODE_ROLLS

                    ELSE
                        TO_CHAR(TRUNC(m.TANGGAL), 'YYYYMMDD') || '|' ||
                        m.KDMESIN || '|' ||
                        TRIM(UPPER(m.KEGIATAN)) || '|ROW|' ||
                        m.NOMOR_LHP || '|' ||
                        m.NO_URUT_DETAIL
                END AS GROUP_LIMIT_KEY
            FROM VOEE_MONITORING m
            LEFT JOIN VOEE_LIMITPLAN lp
                ON lp.KDMESIN = m.KDMESIN
               AND TRIM(UPPER(lp.KEGIATAN)) = TRIM(UPPER(m.KEGIATAN))
            WHERE 
            m.NAMA_DEPARTEMEN != 'PACKING' AND
            m.TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
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
                KEGIATAN, GRUP2, KATEGORI, KTG_LOSSTIME,
                WAKTU_PLANNED_FIX AS WAKTU_BLT
            FROM split_data
            WHERE WAKTU_PLANNED_FIX > 0

            UNION ALL

            SELECT
                'OVER - ' || KEGIATAN AS KEGIATAN,
                GRUP2, KATEGORI,
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

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getTopDefect($tgl_awal, $tgl_akhir)
    {
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
                FROM VOEE_MONITORING
                WHERE 
                NAMA_DEPARTEMEN != 'PACKING' AND                
                TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
                GROUP BY NAMA_WASTE, SAT_HASIL_RUSAK
            )
            ORDER BY DEFECT DESC
        )
        WHERE ROWNUM <= 5
        ";

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getActualTarget($tgl_awal, $tgl_akhir)
    {
        $sql = "
            SELECT
                ROUND(SUM(OUTPUT) / SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 2) AS ACTUAL_OUTPUT,
                ROUND(AVG(TARGET), 2) AS TARGET_OUTPUT,
                ROUND(SUM(OUTPUT), 2) AS TOTAL_OUTPUT,
                ROUND(SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 2) AS TOTAL_WAKTU_PRODUKSI
            FROM VOEE_MONITORING
            WHERE 
                NAMA_DEPARTEMEN != 'PACKING' AND
                TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
        ";

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->row_array();
    }

    public function getDetailModal($type, $tgl_awal, $tgl_akhir)
    {
        switch ($type) {
            case 'AR':
                return $this->getDetailAR($tgl_awal, $tgl_akhir);

            case 'QR':
                return $this->getDetailQR($tgl_awal, $tgl_akhir);

            case 'PR':
                return $this->getDetailPR($tgl_awal, $tgl_akhir);

            default:
                return array();
        }
    }

    private function getDetailAR($tgl_awal, $tgl_akhir)
    {
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
                VOEE_MONITORING m
                LEFT JOIN VOEE_LIMITPLAN lp
                    ON lp.KDMESIN = m.KDMESIN
                    AND TRIM(UPPER(lp.KEGIATAN)) = TRIM(UPPER(m.KEGIATAN))
            WHERE 
                m.NAMA_DEPARTEMEN != 'PACKING' AND
                m.TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
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
            NOMOR_LHP, TANGGAL, NO_URUT_DETAIL, MESIN, NOMOR_KK, PROSES, PRODUK, SHIFT_,
            KEGIATAN, KTG_LOSSTIME, JAM1, JAM2,
            WAKTU_BLT_ASLI, WAKTU_BLT, LIMITPLAN, PAR_LIMITPLAN
        FROM (
            SELECT
                NOMOR_LHP, TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL, NO_URUT_DETAIL, MESIN, NOMOR_KK, PROSES, PRODUK, SHIFT_,
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
                NOMOR_LHP, TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL, NO_URUT_DETAIL, MESIN, NOMOR_KK, PROSES, PRODUK, SHIFT_,
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
        ORDER BY NOMOR_LHP, MESIN, NOMOR_KK, PROSES, SHIFT_, NO_URUT_DETAIL, URUT_DATA
        ";

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->result_array();
    }

    private function getDetailQR($tgl_awal, $tgl_akhir)
    {
        $sql = "
        SELECT
            NOMOR_LHP,
            TO_CHAR(TANGGAL, 'DD/MM/YYYY') AS TANGGAL,
            NO_URUT_DETAIL,
            MESIN,
            NOMOR_KK,
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
        FROM VOEE_MONITORING
        WHERE 
            NAMA_DEPARTEMEN != 'PACKING' AND
            TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
        ORDER BY NOMOR_LHP, MESIN, NOMOR_KK, PROSES, SHIFT_, NO_URUT_DETAIL
        ";

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->result_array();
    }

    private function getDetailPR($tgl_awal, $tgl_akhir)
    {
        $sql = "
        SELECT
            TO_CHAR(TANGGAL, 'DD/MM/YYYY')         AS TANGGAL,
            SHIFT_,
            MESIN,
            NOMOR_KK,
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
        FROM VOEE_MONITORING
        WHERE 
            NAMA_DEPARTEMEN != 'PACKING' AND
            TANGGAL BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
        GROUP BY
            TO_CHAR(TANGGAL, 'DD/MM/YYYY'),
            TANGGAL,
            SHIFT_,
            MESIN,
            NOMOR_KK,
            PRODUK,
            PROSES
        ORDER BY
            TANGGAL, SHIFT_, MESIN, NOMOR_KK, PROSES
        ";

        $bind = array($tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind)->result_array();
    }
}
