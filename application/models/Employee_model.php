<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property Employee_status_model $status
 * @property Family_model $family
 */
class Employee_model extends CI_Model {

    private string $table = 'EMP';
    private array $date_columns = ['DTBIRTH', 'DTAPPT', 'DTRETT'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_status_model', 'status');
        $this->load->model('Family_model', 'family');
    }

    public function find(int $empno)
    {
        return $this->db->select("
                EMP.*,
                EMPTYPEMR.EDETAILS,
                TO_CHAR(EMP.DTBIRTH, 'DD/MM/YYYY') AS DTBIRTH,
                TO_CHAR(EMP.DTAPPT, 'DD/MM/YYYY') AS DTAPPT,
                TO_CHAR(EMP.DTRETT, 'DD/MM/YYYY') AS DTRETT,
            ", false)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->join('EMPTYPEMR', 'EMPTYPEMR.ETYPE = EMP.EMPTYPE', 'left')
            ->get()
            ->row_array();
    }

    public function update(int $empno, array $data)
    {
        foreach ($this->date_columns as $col) {
            if (!array_key_exists($col, $data)) {
                continue;
            }

            if (trim((string)$data[$col]) === '') {
                $this->db->set($col, 'NULL', false);
            } else {
                $this->db->set(
                    $col,
                    "TO_DATE('{$data[$col]}','DD/MM/YYYY')",
                    false
                );
            }

            unset($data[$col]);
        }
        return $this->db->where('EMPNO', $empno)->update($this->table, $data);
    }
}