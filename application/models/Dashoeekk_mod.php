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
        // $sql = "
        //     SELECT
        //         ROUND(
        //             (
        //                 SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END)
        //                 /
        //                 NULLIF(
        //                     SUM(WAKTU_BLT) -
        //                     SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END)
        //                 , 0)
        //             ) * 100
        //         , 2) AS AR,

        //         ROUND(
        //             (
        //                 (
        //                     SUM(OUTPUT)
        //                     /
        //                     NULLIF(SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 0)
        //                 )
        //                 /
        //                 NULLIF(AVG(TARGET), 0)
        //             ) * 100
        //         , 2) AS PR,

        //         ROUND(
        //             (
        //                 SUM(BAIK)
        //                 /
        //                 NULLIF(SUM(OUTPUT), 0)
        //             ) * 100
        //         , 2) AS QR,

        //         SUM(BAIK) AS BAIK,
        //         SUM(RUSAK) AS RUSAK,
        //         SUM(OUTPUT) AS OUTPUT,
        //         AVG(TARGET) AS TARGET_KK,

        //         SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END) AS WAKTU_PRO,
        //         SUM(WAKTU_BLT) AS WAKTU_ALL,
        //         SUM(CASE WHEN KTG_LOSSTIME = 'PLANNED' THEN WAKTU_BLT ELSE 0 END) AS WAKTU_PLANNED,

        //         73 AS TARGET_AR,
        //         85 AS TARGET_PR,
        //         98 AS TARGET_QR

        //         FROM VOEE_MONITORING
        //         WHERE THN = ?
        //         AND BLN_ = ?
        //         AND KDMESIN = ?
        //     ";
        // $bind = array($tahun, $bulan, $kdmesin);

        $sql = "
        WITH base AS (
            SELECT
                m.*,
                lp.LIMITPLAN,
                lp.PAR_LIMITPLAN,
            CASE
                    
                    WHEN lp.PAR_LIMITPLAN = 'SHIFT' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ 
                    WHEN lp.PAR_LIMITPLAN = 'PRODUK' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|PRODUK|' || m.PRODUK 
                    WHEN lp.PAR_LIMITPLAN = 'BAHAN' THEN
                    m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|SHIFT|' || m.SHIFT_ || '|KODE_ROLLS|' || m.KODE_ROLLS ELSE m.TANGGAL || '|' || m.KDMESIN || '|' || m.KEGIATAN || '|ROW|' || m.NOMOR_LHP || '|' || m.NO_URUT_DETAIL 
                END AS GROUP_LIMIT_KEY 
            FROM
                VOEE_MONITORING m
                LEFT JOIN VOEE_LIMITPLAN lp ON lp.KDMESIN = m.KDMESIN 
                AND TRIM( UPPER( lp.KEGIATAN ) ) = TRIM( UPPER( m.KEGIATAN ) ) 
            WHERE
                m.THN = ?
                AND m.BLN_ = ? 
                AND m.KDMESIN = ? 
    ";

        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= "
              AND m.NOMOR_KK = ?
              AND TRUNC(m.TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";

            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        $sql .= "
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
                        THN,
                        BLN_,
                        NAMA_DEPARTEMEN,
                        NOMOR_LHP,
                        TANGGAL,
                        NOMOR_KK,
                        TANGGAL_KK,
                        REVISI_KE,
                        STATUS_KK,
                        PRODUK,
                        NOMER_PO_CUSTOMER,
                        KODE_BARANG_PO,
                        KODE_ROLLS,
                        KODE_BARANG_BHN,
                        NAMA_BARANG_BHN,
                        KDMESIN,
                        MESIN,
                        URUT_PROSES,
                        KODE_PROSES,
                        PROSES,
                        SHIFT_,
                        NO_URUT_DETAIL,
                        KATEGORI,
                        KEGIATAN,
                        GRUP2,
                        KTG_LOSSTIME,
                        JAM1,
                        JAM2,
                        WAKTU_BLT,
                        WAKTU_PLANNED_FIX AS WAKTU_BLT_2,
                        BAIK,
                        SAT_HASIL_BAIK,
                        KODE_WASTE,
                        NAMA_WASTE,
                        RUSAK,
                        SAT_HASIL_RUSAK,
                        OUTPUT,
                        TARGET,
                        SAT_TARGET,
                        LIMITPLAN,
                        PAR_LIMITPLAN,
                        'ORIGINAL' AS TIPE_DATA 
                    FROM
                        split_data 
                    WHERE
                        WAKTU_PLANNED_FIX > 0 UNION ALL
                    SELECT
                        THN,
                        BLN_,
                        NAMA_DEPARTEMEN,
                        NOMOR_LHP,
                        TANGGAL,
                        NOMOR_KK,
                        TANGGAL_KK,
                        REVISI_KE,
                        STATUS_KK,
                        PRODUK,
                        NOMER_PO_CUSTOMER,
                        KODE_BARANG_PO,
                        KODE_ROLLS,
                        KODE_BARANG_BHN,
                        NAMA_BARANG_BHN,
                        KDMESIN,
                        MESIN,
                        URUT_PROSES,
                        KODE_PROSES,
                        PROSES,
                        SHIFT_,
                        NO_URUT_DETAIL,
                        KATEGORI,
                        'OVER - ' || KEGIATAN AS KEGIATAN,
                        GRUP2,
                        'UNPLANNED' AS KTG_LOSSTIME,
                        JAM1,
                        JAM2,
                        WAKTU_BLT,
                        WAKTU_UNPLANNED_FIX AS WAKTU_BLT_2,
                        BAIK,
                        SAT_HASIL_BAIK,
                        KODE_WASTE,
                        NAMA_WASTE,
                        RUSAK,
                        SAT_HASIL_RUSAK,
                        OUTPUT,
                        TARGET,
                        SAT_TARGET,
                        LIMITPLAN,
                        PAR_LIMITPLAN,
                        'GENERATE_LIMITPLAN' AS TIPE_DATA 
                    FROM
                        split_data 
                    WHERE
                        WAKTU_UNPLANNED_FIX > 0 
                    ) SELECT
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
            73 AS TARGET_AR,
            85 AS TARGET_PR,
            98 AS TARGET_QR 
        FROM
            data_fix
        ";


        $query = $this->db->query($sql, $bind);
        $row = $query->row_array();

        $ar = (float) $row['AR'];
        $pr = (float) $row['PR'];
        $qr = (float) $row['QR'];

        $row['OEE'] = round(($ar * $pr * $qr) / 10000, 2);

        return $row;
    }

    // public function getTopDowntime($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    // {
    //     $sql = "
    //         SELECT * FROM (
    //             SELECT
    //                 KEGIATAN,
    //                 ROUND(
    //                     SUM(WAKTU_BLT) /
    //                     NULLIF(SUM(SUM(WAKTU_BLT)) OVER (), 0) * 100
    //                 , 2) AS PERSEN
    //             FROM VOEE_MONITORING
    //             WHERE THN = ?
    //             AND BLN_ = ?
    //             AND KDMESIN = ?
    //     ";
    //     $bind = array($tahun, $bulan, $kdmesin);

    //     if (!empty($nomor_kk) && !empty($tanggal_kk)) {
    //         $sql .= " AND NOMOR_KK = ?
    //                 AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))";
    //         $bind[] = $nomor_kk;
    //         $bind[] = $tanggal_kk;
    //     }

    //     $sql .= "
    //             AND GRUP2 = 'B0002'
    //             AND KATEGORI <> 'PRODUKSI'
    //             GROUP BY KEGIATAN
    //             ORDER BY PERSEN DESC
    //         )
    //         WHERE ROWNUM <= 5
    //     ";

    //     return $this->db->query($sql, $bind)->result_array();
    // }

    public function getTopDowntime($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
        WITH base AS (
            SELECT
                m.*,
                lp.LIMITPLAN,
                lp.PAR_LIMITPLAN,
                CASE
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
            WHERE m.THN = ?
              AND m.BLN_ = ?
              AND m.KDMESIN = ?
    ";

        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= "
              AND m.NOMOR_KK = ?
              AND TRUNC(m.TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";

            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        $sql .= "
        ),

        calc AS (
            SELECT
                b.*,

                SUM(
                    CASE
                        WHEN b.KTG_LOSSTIME = 'PLANNED'
                         AND b.LIMITPLAN IS NOT NULL
                        THEN b.WAKTU_BLT
                        ELSE 0
                    END
                ) OVER (
                    PARTITION BY b.GROUP_LIMIT_KEY
                    ORDER BY b.JAM1, b.JAM2, b.NOMOR_LHP, b.NO_URUT_DETAIL
                    ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
                ) AS CUM_WAKTU_PREV

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
                KEGIATAN,
                GRUP2,
                KATEGORI,
                KTG_LOSSTIME,
                WAKTU_PLANNED_FIX AS WAKTU_BLT
            FROM split_data
            WHERE WAKTU_PLANNED_FIX > 0

            UNION ALL

            SELECT
                'OVER - ' || KEGIATAN AS KEGIATAN,
                GRUP2,
                KATEGORI,
                'UNPLANNED' AS KTG_LOSSTIME,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT
            FROM split_data
            WHERE WAKTU_UNPLANNED_FIX > 0
        )

       SELECT *
        FROM (
            SELECT
                KEGIATAN,
                COUNT(*) AS FREQ_DOWNTIME,
                SUM(WAKTU_BLT) AS WAKTU_DOWNTIME,
                ROUND(
                    SUM(WAKTU_BLT) /
                    NULLIF(SUM(SUM(WAKTU_BLT)) OVER (), 0) * 100,
                    2
                ) AS PERSEN
            FROM data_fix
            WHERE KATEGORI <> 'PRODUKSI' AND KTG_LOSSTIME = 'UNPLANNED'
            GROUP BY KEGIATAN
            ORDER BY PERSEN DESC
        )
        WHERE ROWNUM <= 5
    ";

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getTopDefect($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
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
                WHERE THN = ?
                AND BLN_ = ?
                AND KDMESIN = ?
    ";
        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= " AND NOMOR_KK = ?
                AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))";
            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        $sql .= "
                GROUP BY NAMA_WASTE, SAT_HASIL_RUSAK
            )
            ORDER BY DEFECT DESC
        )
        WHERE ROWNUM <= 5
    ";

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getActualTarget($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
            SELECT
                ROUND(SUM(BAIK) / SUM(CASE WHEN KATEGORI = 'PRODUKSI' THEN WAKTU_BLT ELSE 0 END), 2) AS ACTUAL_OUTPUT,
                ROUND(AVG(TARGET), 2) AS TARGET_OUTPUT
            FROM VOEE_MONITORING
            WHERE THN = ?
            AND BLN_ = ?
            AND KDMESIN = ?
        ";
        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= " AND NOMOR_KK = ?
              AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))";
            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        return $this->db->query($sql, $bind)->row_array();
    }

    public function getDetailModal($type, $tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        switch ($type) {
            case 'AR':
                return $this->getDetailAR($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk);

            case 'QR':
                return $this->getDetailQR($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk);

            case 'PR':
                // TODO: belum ditentukan, sementara return kosong
                return array();

            default:
                return array();
        }
    }

    private function getDetailAR($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
        WITH base AS (
            SELECT
                m.THN,
                m.BLN_,
                m.NAMA_DEPARTEMEN,
                m.NOMOR_LHP,
                m.TANGGAL,
                m.NOMOR_KK,
                m.TANGGAL_KK,
                m.REVISI_KE,
                m.STATUS_KK,
                m.PRODUK,
                m.NOMER_PO_CUSTOMER,
                m.KODE_BARANG_PO,
                m.KODE_ROLLS,
                m.KODE_BARANG_BHN,
                m.NAMA_BARANG_BHN,
                m.KDMESIN,
                m.MESIN,
                m.URUT_PROSES,
                m.KODE_PROSES,
                m.PROSES,
                m.SHIFT_,
                m.NO_URUT_DETAIL,
                m.KATEGORI,
                m.KEGIATAN,
                m.GRUP2,
                m.KTG_LOSSTIME,
                m.JAM1,
                m.JAM2,
                m.WAKTU_BLT,
                m.BAIK,
                m.SAT_HASIL_BAIK,
                m.KODE_WASTE,
                m.NAMA_WASTE,
                m.RUSAK,
                m.SAT_HASIL_RUSAK,
                m.OUTPUT,
                m.TARGET,
                m.SAT_TARGET,
                lp.LIMITPLAN,
                lp.PAR_LIMITPLAN,
                CASE
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
            WHERE m.THN = ?
              AND m.BLN_ = ?
              AND m.KDMESIN = ?
    ";

        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= "
              AND m.NOMOR_KK = ?
              AND TRUNC(m.TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";
            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        $sql .= "
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
            NOMOR_LHP,
            NO_URUT_DETAIL,
            NOMOR_KK,
            PRODUK,
            SHIFT_,
            KEGIATAN,
            KTG_LOSSTIME,
            JAM1,
            JAM2,
            WAKTU_BLT_ASLI,
            WAKTU_BLT,
            LIMITPLAN,
            PAR_LIMITPLAN
        FROM (
            SELECT
                NOMOR_LHP, NO_URUT_DETAIL, NOMOR_KK, PRODUK, SHIFT_,
                KEGIATAN, KTG_LOSSTIME, JAM1, JAM2,
                WAKTU_BLT AS WAKTU_BLT_ASLI,
                WAKTU_PLANNED_FIX AS WAKTU_BLT,
                LIMITPLAN, PAR_LIMITPLAN,
                1 AS URUT_DATA
            FROM split_data
            WHERE WAKTU_PLANNED_FIX > 0

            UNION ALL

            SELECT
                NOMOR_LHP, NO_URUT_DETAIL, NOMOR_KK, PRODUK, SHIFT_,
                'OVER - ' || KEGIATAN AS KEGIATAN,
                'UNPLANNED' AS KTG_LOSSTIME,
                JAM1, JAM2,
                WAKTU_BLT AS WAKTU_BLT_ASLI,
                WAKTU_UNPLANNED_FIX AS WAKTU_BLT,
                LIMITPLAN, PAR_LIMITPLAN,
                2 AS URUT_DATA
            FROM split_data
            WHERE WAKTU_UNPLANNED_FIX > 0
        )
        ORDER BY NOMOR_LHP, NOMOR_KK, SHIFT_, NO_URUT_DETAIL, URUT_DATA
    ";

        return $this->db->query($sql, $bind)->result_array();
    }

    private function getDetailQR($tahun, $bulan, $kdmesin, $nomor_kk, $tanggal_kk)
    {
        $sql = "
        SELECT
            NOMOR_LHP,
            NO_URUT_DETAIL,
            NOMOR_KK,
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
        WHERE THN = ?
          AND BLN_ = ?
          AND KDMESIN = ?
    ";

        $bind = array($tahun, $bulan, $kdmesin);

        if (!empty($nomor_kk) && !empty($tanggal_kk)) {
            $sql .= "
          AND NOMOR_KK = ?
          AND TRUNC(TANGGAL_KK) = TRUNC(TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))
        ";
            $bind[] = $nomor_kk;
            $bind[] = $tanggal_kk;
        }

        $sql .= " ORDER BY NOMOR_LHP, NOMOR_KK, SHIFT_, NO_URUT_DETAIL ";

        return $this->db->query($sql, $bind)->result_array();
    }
}
