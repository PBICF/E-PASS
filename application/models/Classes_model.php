<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Classes_model extends CI_Model {
    
    private string $table = 'CLASSES';

    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        return $this->db->get($this->table)->result_array();
    }

    public function is_valid($class)
    {
        return $this->db->get_where($this->table, array('SCODE' => $class))->num_rows() > 0;
    }
}