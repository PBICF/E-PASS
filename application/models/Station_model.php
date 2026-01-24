<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Station_model extends CI_Model {

    private string $table = 'STATIONS';

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        return $this->db->select('STATIONS.*')
            ->from($this->table)
            ->get()
            ->result_array();
    }

    public function get_by_code(string $code) {
        return $this->db->select('STATIONS.*')
            ->from($this->table)
            ->where('SCODE', $code)
            ->get()
            ->row_array();
    }
}
