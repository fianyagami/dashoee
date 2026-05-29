<?php

class M_db extends CI_Model
{
    //======= Custom Query =======
    function getCustom($q)
    {
        $data = $this->db->query($q)->result();
        return $data;
    }

    //======= Get Data (Rows) =======
    function getDataAll($table)
    {
        $data = $this->db->query("SELECT * FROM $table")->result();
        return $data;
    }

    function getDataLike($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column LIKE $id")->result();
        return $data;
    }

    function getDataEqual($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column = $id")->result();
        return $data;
    }

    function getDataIn($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column IN ($id)")->result();
        return $data;
    }

    function getDataExcept($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column != $id")->result();
        return $data;
    }

    function getDataNot($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column NOT IN $id")->result();
        return $data;
    }

    function getDataWhere2($table, $col1, $id1, $col2, $id2)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $col1=$id1 AND $col2=$id2")->result();
        return $data;
    }

    //======= Get Data (1 Row) =======
    function getDataAllOneRow($table)
    {
        $data = $this->db->query("SELECT * FROM $table")->row_array();
        return $data;
    }

    function getDataOneRow($table, $column, $id)
    {
        $data = $this->db->query("SELECT * FROM $table WHERE $column LIKE $id")->row_array();
        return $data;
    }

    function getDataOneColumn($getCol, $table, $column, $id)
    {
        $data = $this->db->query("SELECT $getCol as val FROM $table WHERE $column = $id")->row_array();
        return $data;
    }

    function getDataOneColumnN($getCol, $table)
    {
        $data = $this->db->query("SELECT $getCol as val FROM $table")->row_array();
        return $data;
    }

    //======= Get Data (Max) =======
    function getDataMax($table, $column)
    {
        $data = $this->db->query("SELECT MAX($column) AS 'maxValue' FROM $table")->row_array();
        return $data;
    }

    function getDataMaxPlus($table, $column)
    {
        $data = $this->db->query("SELECT MAX($column)+1 AS 'maxValue' FROM $table")->row_array();
        return $data;
    }

    function getDataMaxPlusLpad($column, $x, $table)
    {
        // x adalah digit
        $data = $this->db->query("SELECT IFNULL(LPAD(MAX($column)+1, $x, '0'),LPAD('1',3,'0')) AS 'maxValue' FROM $table")->result();
        return $data;
        // Data dalam bentuk 1 data yang dipanggil
    }

    function getDataMaxPlusLpad2($column, $x, $table)
    {
        // x adalah digit
        $data = $this->db->query("SELECT IFNULL(LPAD(MAX($column)+1, $x, '0'),LPAD('1',3,'0')) AS 'maxValue' FROM $table")->row_array();
        return $data;
        // Data dalam bentuk Array
    }

    //======= Get Data (Count) =======
    function getCountData($table)
    {
        $data = $this->db->query("SELECT COUNT(*) as count_data FROM $table");
        return $data->row();
    }

    function getCountDataSet($table, $set)
    {
        $data = $this->db->query("SELECT COUNT(*) as count_data FROM $table $set");
        return $data->row();
    }

    function getCountDataWhere($table, $column, $id)
    {
        $data = $this->db->query("SELECT COUNT(*) as count_data FROM $table WHERE $column='" . $id . "'");
        return $data->row();
    }

    function getCountDataWhereSet($table, $set)
    {
        $data = $this->db->query("SELECT COUNT(*) as count_data FROM $table $set");
        return $data->row();
    }

    //======= Get Data (SUM) =======
    function getSumData($col, $table)
    {
        $data = $this->db->query("SELECT SUM($col) as TOT_DATA FROM $table")->row_array();
        return $data;
    }

    function getSumDataWhere($col, $table, $column, $id)
    {
        $data = $this->db->query("SELECT SUM($col) as TOT_DATA FROM $table WHERE $column='" . $id . "'")->row_array();
        return $data;
    }

    //======= Get Data (Date) =======
    // DATE_FORMAT : 31/03/1991
    // - %d = 31
    // - %m = 03
    // - %M = March 
    // - %b = Mar 
    // - %y = 91
    // - %Y = 1991
    function getDate($column, $table)
    {
        $data = $this->db->query("SELECT  *,DATE_FORMAT($column,'%d') as tgl FROM $table ")->row_array();
        return $data;
    }

    function getYear($column, $table)
    {
        $data = $this->db->query("SELECT  *,DATE_FORMAT($column,'%Y') as tgl FROM $table ")->result();
        return $data;
    }

    function getMonth($column, $table)
    {
        $data = $this->db->query("SELECT  *,DATE_FORMAT($column,'%M') as tgl FROM $table ")->result();
        return $data;
    }



    //======= Insert Data =======
    function insertDataAll($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->affected_rows();
    }

    //======= Update Data =======
    function updateDataAll($table, $col_pk, $id, $data)
    {
        $this->db->where($col_pk, $id);
        $this->db->update($table, $data);
        return $this->db->affected_rows();
    }

    function updateCustomData($table, $set, $where)
    {
        return $this->db->query("UPDATE $table SET $set WHERE $where");
    }

    //======= Delete Data =======
    function deleteData($table, $col_pk, $id)
    {
        $this->db->delete($table, array($col_pk => $id));
        return $this->db->affected_rows();
    }

    function deleteCustomData($table, $where)
    {
        return $this->db->query("DELETE FROM $table WHERE $where");
    }

    function deletBatch($set)
    {
        return $this->db->query("DELETE $set");
    }

    function deleteDataAll($table)
    {
        $this->db->truncate($table);
    }
}


// ==============================
