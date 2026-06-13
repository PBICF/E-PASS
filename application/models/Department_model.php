<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 */
class Department_model extends CI_Model {

    protected $table = 'PASS.DEPTMR';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->get($this->table)->result_array();
    }
}