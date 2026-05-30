<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_login extends CI_Model
{

    function cek_login($where)
    {
        return $this->db->get_where("V_SSS_USER", $where);
    }



    public function insertLogLogin($id_user, $ip)
    {
        $sql = "
        INSERT INTO DASHOEE_USER_LOG
        (
            ID_DASHOEE_LOG,
            ID_USER,
            IP_ADDRESS,
            DATE_TIME,
            DASHOEE_VERSION
        )
        VALUES
        (
            SEQ_DASHOEE_LOG.NEXTVAL,
            ?,
            ?,
            CURRENT_DATE,
            'WEB'
        )
    ";

        return $this->db->query($sql, [
            $id_user,
            $ip
        ]);
    }

    function updateDataLastLogin($id_users)
    {
        $query = "INSERT INTO tbl_user_log SET id_user = '" . $_SESSION[$GLOBALS['project'] . '-id_user'] . "', act='LOGIN', info='IP:" . $this->get_client_ip() . "'";
        $this->db->query($query);
    }

    function updateDataLastLogout($id_users)
    {
        $query = "INSERT INTO tbl_user_log SET id_user = '" . $_SESSION[$GLOBALS['project'] . '-id_user'] . "', act='LOGOUT', info='IP:" . $this->get_client_ip() . "'";
        $this->db->query($query);
    }

    function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}
