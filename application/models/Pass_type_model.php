<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pass_type_model extends CI_Model {

    private string $table = 'TYPEMR';

    public function all()
    {
        return $this->db
            ->select("
                TYPEMR.TCODE,
                TYPEMR.TNAME
            ", FALSE)
            ->from($this->table)
            ->where('TCODE !=', 0)
            ->get()
            ->result_array();
    }

    public function should_debit_account(int $pass_type_code): bool
    {
        $result = $this->db
            ->select('AC_UPDATE')
            ->from($this->table)
            ->where('TCODE', $pass_type_code)
            ->get()
            ->row_array();

        return isset($result['AC_UPDATE']) && $result['AC_UPDATE'] === 'Y';
    }

    public function does_exist(int $tcode): bool
    {
        return $this->db
            ->where('TCODE', $tcode)
            ->count_all_results($this->table) > 0;
    }
    
}