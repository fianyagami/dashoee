<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masterlimplan_mod extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getMesin()
    {
        $sql = "
            SELECT KDMESIN, NAMA_MESIN
            FROM VOEE_LIMITPLAN
            GROUP BY KDMESIN, NAMA_MESIN
            ORDER BY NAMA_MESIN
        ";

        $query = $this->db->query($sql);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'       => $row->KDMESIN,
                'text'     => $row->NAMA_MESIN,
                'kdmesin'  => $row->KDMESIN,
                'mesin'    => $row->NAMA_MESIN
            );
        }

        return array('results' => $data);
    }

    public function getMesinAktif($q = null)
    {
        $sql  = "SELECT KODE_MESIN AS KDMESIN, TRIM(NAMA_MESIN) AS NAMA_MESIN FROM V_MESIN_AKTIF";
        $bind = array();

        if (!empty($q)) {
            $sql .= " WHERE UPPER(NAMA_MESIN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(NAMA_MESIN)";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'      => $row->KDMESIN,
                'text'    => $row->NAMA_MESIN,
                'kdmesin' => $row->KDMESIN,
                'mesin'   => $row->NAMA_MESIN
            );
        }

        return array('results' => $data);
    }

    public function getKegiatan($q = null)
    {
        $sql  = "SELECT K_HIS, TRIM(KEGIATAN) AS KEGIATAN FROM V_KEGIATAN_PROD WHERE GRUP2 = 'B0001'";
        $bind = array();

        if (!empty($q)) {
            $sql .= " AND UPPER(KEGIATAN) LIKE UPPER(?)";
            $bind[] = "%" . $q . "%";
        }

        $sql .= " ORDER BY TRIM(KEGIATAN)";

        $query = $this->db->query($sql, $bind);

        $data = array();
        foreach ($query->result() as $row) {
            $data[] = array(
                'id'      => $row->K_HIS,
                'text'    => $row->KEGIATAN,
                'k_his'   => $row->K_HIS,
                'kegiatan' => $row->KEGIATAN
            );
        }

        return array('results' => $data);
    }

    public function getData($kdmesin)
    {
        $sql = "
            SELECT
                ID_LIMITPLAN,
                KDMESIN,
                K_HIS,
                KEGIATAN,
                LIMITPLAN,
                LIMITPLAN_MENIT,
                PAR_LIMITPLAN
            FROM VOEE_LIMITPLAN
            WHERE KDMESIN = ?
            ORDER BY KEGIATAN
        ";

        return $this->db->query($sql, array($kdmesin))->result();
    }

    public function save($id_limitplan, $kdmesin, $k_his, $limitplan, $par_limitplan)
    {
        try {
            if (!empty($id_limitplan)) {
                // UPDATE
                $sql = "
                    UPDATE ADMIN.TBL_LIMITPLAN
                    SET KDMESIN       = ?,
                        K_HIS         = ?,
                        LIMITPLAN     = ?,
                        PAR_LIMITPLAN = ?
                    WHERE ID_LIMITPLAN = ?
                ";
                $this->db->query($sql, array(
                    $kdmesin,
                    $k_his,
                    $limitplan,
                    $par_limitplan,
                    $id_limitplan
                ));
            } else {
                // INSERT — cek ID terakhir lalu +1
                $sqlMaxId = "SELECT NVL(MAX(ID_LIMITPLAN), 0) + 1 AS NEW_ID FROM ADMIN.TBL_LIMITPLAN";
                $newId    = $this->db->query($sqlMaxId)->row()->NEW_ID;

                $sql = "
                    INSERT INTO ADMIN.TBL_LIMITPLAN
                        (ID_LIMITPLAN, KDMESIN, K_HIS, LIMITPLAN, PAR_LIMITPLAN)
                    VALUES
                        (?, ?, ?, ?, ?)
                ";
                $this->db->query($sql, array(
                    $newId,
                    $kdmesin,
                    $k_his,
                    $limitplan,
                    $par_limitplan
                ));
            }

            return array('status' => 'success', 'message' => 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }
    }
}
