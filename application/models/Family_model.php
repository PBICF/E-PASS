<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Relation_model $relation
 * @property CI_DB_query_builder $db
 */
class Family_model extends CI_Model {
    
    private string $table = 'FAMILY';
    private array $date_columns = ['DB'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Relation_model', 'relation');
    }

    public function find(int $empno)
    {
        return $this->db->select("
                FAMILY.*,
                RELMR.RELNAME as RELATION,
                TO_CHAR(DB, 'DD/MM/YYYY') AS DB
            ", FALSE)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->join('RELMR', 'RELMR.RELCODE = FAMILY.FRELATION', 'left')
            ->get()
            ->result_array();
    }

    public function findMembers(int $empno, array $relations = [])
    {
        return $this->db->select("
                FAMILY.*,
                RELMR.RELNAME as RELATION,
                TO_CHAR(DB, 'DD/MM/YYYY') AS DB
            ", FALSE)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->where_in('FSLNO', $relations)
            ->join('RELMR', 'RELMR.RELCODE = FAMILY.FRELATION', 'left')
            ->order_by('FRELATION', 'ASC')
            ->get()
            ->result_array();
    }

    public function update(int $empno, int $fslno, $data)
    {
        foreach($data as $column => $value) {
            if(in_array($column, $this->date_columns) || in_array(strtoupper($column), $this->date_columns)) {
                if(trim($value) === '') {
                    $this->db->set($column, 'NULL', false);
                } else {
                    $this->db->set(
                        $column,
                        "TO_DATE('{$value}', 'DD/MM/YYYY')",
                        false
                    );
                }
            } else {
                $this->db->set(strtoupper($column), $value);
            }
        }
        
        $this->db
            ->where('EMPNO', $empno)
            ->where('FSLNO', $fslno)
            ->update($this->table);
    }

    public function getRelations(int $empno, array $frelation): array
    {
        return $this->db
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->where_in('FRELATION', $frelation)
            ->get()
            ->result_array();
    }
}