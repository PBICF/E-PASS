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

    public function nextSlno(int $empno): int
    {
        $row = $this->db
            ->select_max('FSLNO', 'max_fslno')
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->get()
            ->row_array();

        return isset($row['max_fslno']) && $row['max_fslno'] !== null
            ? (int) $row['max_fslno'] + 1
            : 1;
    }

    public function insert(int $empno, array $data): void
    {
        $fslno = $this->nextSlno($empno);

        $row = [
            'EMPNO'     => $empno,
            'FSLNO'     => $fslno,
            'NAME'      => $data['name']      ?? null,
            'FRELATION' => $data['frelation'] ?? null,
            'FALLOWED'  => $data['fallowed']  ?? 'N',
        ];

        $db_val = $data['db'] ?? '';
        if (!empty(trim((string)$db_val))) {
            $this->db->set('DB', "TO_DATE('{$db_val}', 'DD/MM/YYYY')", false);
        } else {
            $this->db->set('DB', 'NULL', false);
        }

        $this->db->insert($this->table, $row);

        // --- Audit Log ---
        $audit_data = [
            'EMPNO'       => $empno,
            'FSLNO'       => $fslno,
            'ACTION'      => 'INSERT',
            'NEW_NAME'    => $row['NAME'],
            'NEW_REL'     => $row['FRELATION'],
            'NEW_DB'      => !empty($db_val) ? $db_val : null,
            'NEW_ALLOWED' => $row['FALLOWED'],
            'CHANGED_BY'  => $this->session->userdata('username') ?? 'SYSTEM',
        ];

        if ($audit_data['NEW_DB']) {
            $this->db->set('NEW_DB', "TO_DATE('{$audit_data['NEW_DB']}', 'DD/MM/YYYY')", false);
            unset($audit_data['NEW_DB']);
        }

        $this->db->insert('FAMILY_AUDIT', $audit_data);
    }

    public function update(int $empno, int $fslno, $data)
    {
        // Get old data for audit
        $old_row = $this->db->select("NAME, FRELATION, TO_CHAR(DB, 'DD/MM/YYYY') as DB, FALLOWED")
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->where('FSLNO', $fslno)
            ->get()
            ->row_array();

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

        // --- Audit Log ---
        if ($old_row) {
            $audit_data = [
                'EMPNO'       => $empno,
                'FSLNO'       => $fslno,
                'ACTION'      => 'UPDATE',
                'OLD_NAME'    => $old_row['NAME'],
                'NEW_NAME'    => $data['name'] ?? $old_row['NAME'],
                'OLD_REL'     => $old_row['FRELATION'],
                'NEW_REL'     => $data['frelation'] ?? $old_row['FRELATION'],
                'OLD_ALLOWED' => $old_row['FALLOWED'],
                'NEW_ALLOWED' => $data['fallowed'] ?? $old_row['FALLOWED'],
                'CHANGED_BY'  => $this->session->userdata('username') ?? 'SYSTEM',
            ];

            // Handle Dates in Audit
            $old_db = $old_row['DB'];
            $new_db = $data['db'] ?? $old_db;

            if ($old_db) {
                $this->db->set('OLD_DB', "TO_DATE('{$old_db}', 'DD/MM/YYYY')", false);
            }
            if ($new_db) {
                $this->db->set('NEW_DB', "TO_DATE('{$new_db}', 'DD/MM/YYYY')", false);
            }

            $this->db->insert('FAMILY_AUDIT', $audit_data);
        }
    }

    public function getRelations(int $empno, array $frelation): array
    {
        return $this->db
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->where_in('FSLNO', $frelation)
            ->get()
            ->result_array();
    }
}