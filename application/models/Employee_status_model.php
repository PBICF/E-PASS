<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Employee_status_model extends CI_Model {

    private string $table = 'EMPTYPEMR';

    public function find(int $type)
    {
        return $this->db->get_where($this->table, ['ETYPE' => $type])->row();
    }

    public function all()
    {
        return $this->db->select("
                EMPTYPEMR.ETYPE,
                EMPTYPEMR.EDETAILS
            ", true)
            ->from($this->table)
            ->get()
            ->result_array();
    }    

    public function is_valid($status)
    {
        return $this->db->get_where($this->table, array('ETYPE' => $status))->num_rows() > 0;
    }
}