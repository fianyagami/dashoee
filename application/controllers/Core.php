<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Core extends CI_Controller
{
    public $priv = null;
    public $sessionid = null;
    function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->priv = $_SESSION[$GLOBALS['project'] . '-ROLE'];
        $this->sessionid = $_SESSION[$GLOBALS['project'] . '-KA'];
        $this->load->helper(array('form', 'url', 'download', 'my_helper'));
        $this->load->model(array('m_db'));
        if (!isset($_SESSION[$GLOBALS['project'] . '-USERNAME'])) {
            redirect(base_url());
        }
    }

    public function index()
    {
        // $this->load->view('welcome_message');
        $data['title']  = "Page Kosongan";
        $data['template']   = "v_kosongan";
        $data['link']   = base_url() . 'core/kosongan';
        $this->load->view('v_main', $data);
    }


    public function kosongan()
    {
        $data['title']  = "Page Kosongan";
        $data['template']   = "v_kosongan";
        $data['link']   = base_url() . 'core/kosongan';
        // $data["dataCustomer"] = $this->m_db->getDataAll('tbl_master_customer');
        $this->load->view('v_main', $data);
    }

    public function error()
    {
        $data['title']  = "Development Page";
        $data['template']   = "v_eror";
        $data['link']   = base_url() . 'core/error';
        $this->load->view('v_main', $data);
    }
}
