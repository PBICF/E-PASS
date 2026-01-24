<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Relation_model extends CI_Model {

    private string $table = 'RELMR';

    public function find(int $code)
    {
        return $this->db->get_where($this->table, ['RELCODE' => $code])->row();
    }

    public function all()
    {
        return $this->db->select("
                RELMR.RELCODE,
                RELMR.RELNAME
            ", true)
            ->from($this->table)
            ->get()
            ->result_array();
    }
}