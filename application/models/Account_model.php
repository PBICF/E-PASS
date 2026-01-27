<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model {

    private string $table = 'ACCOUNTMR';
    private array $date_columns = ['PTO_ASON', 'PASS_ASON', 'SECONDA_ASON'];

    public function findPassByEmp(int $empno)
    {
        return $this->db
            ->select("
                ACYEAR AS year, PASS_TOTAL AS total, PASS_AVAILED AS availed,
                (PASS_TOTAL - PASS_AVAILED) AS balance,
                TO_CHAR(PASS_ASON, 'DD/MM/YYYY') AS ason
            ", FALSE)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->get()
            ->result_array();
    }

    public function findPtoByEmp(int $empno)
    {
        return $this->db
            ->select("
                ACYEAR AS year, PTO_TOTAL AS total, PTO_AVAILED AS availed,
                (PTO_TOTAL - PTO_AVAILED) AS balance,
                TO_CHAR(PTO_ASON, 'DD/MM/YYYY') AS ason,
            ", FALSE)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->get()
            ->result_array();
    }

    public function findSecondPassByEmp(int $empno)
    {
        return $this->db
            ->select("
                ACYEAR AS year, SECONDA_TOTAL AS total, SECONDA_AVAILED AS availed,
                (SECONDA_TOTAL - SECONDA_AVAILED) AS balance,
                TO_CHAR(SECONDA_ASON, 'DD/MM/YYYY') AS ason
            ", FALSE)
            ->from($this->table)
            ->where('EMPNO', $empno)
            ->get()
            ->result_array();
    }

    public function get_account($empno) {
        return $this->db
            ->where('EMPNO', $empno)
            ->get($this->table)
            ->result();
    }

    public function update_account($empno, $year, $data)
    {
        $pass_ason    = $data['PASS_ASON']    ?? null;
        $pto_ason     = $data['PTO_ASON']     ?? null;
        $seconda_ason = $data['SECONDA_ASON'] ?? null;

        unset($data['PASS_ASON'], $data['PTO_ASON'], $data['SECONDA_ASON']);

        $this->db->set($data);
        if ($pass_ason && $pass_ason !== '-') {
            $this->db->set(
                'PASS_ASON',
                "TO_DATE('{$pass_ason}', 'DD/MM/YYYY')",
                false
            );
        } else {
            $this->db->set('PASS_ASON', null);
        }

        if ($pto_ason && $pto_ason !== '-') {
            $this->db->set(
                'PTO_ASON',
                "TO_DATE('{$pto_ason}', 'DD/MM/YYYY')",
                false
            );
        } else {
            $this->db->set('PTO_ASON', null);
        }

        if ($seconda_ason && $seconda_ason !== '-') {
            $this->db->set(
                'SECONDA_ASON',
                "TO_DATE('{$seconda_ason}', 'DD/MM/YYYY')",
                false
            );
        } else {
            $this->db->set('SECONDA_ASON', null);
        }

        $this->db
            ->where('EMPNO', $empno)
            ->where('ACYEAR', $year)
            ->update('ACCOUNTMR');
    }

    public function update(int $empno, int $year, array $data)
    {
        foreach ($this->date_columns as $column) {
            if (!empty($data[$column])) {
                $this->db->set(
                    $column,
                    "TO_DATE('{$data[$column]}', 'DD/MM/YYYY')",
                    false
                );
                unset($data[$column]);
            }
        }

        $this->db
            ->where('EMPNO', $empno)
            ->where('ACYEAR', $year)
            ->update($this->table, $data);
    }

    public function debit(int $empno, int $year, string $type, int $amount)
    {
        if($type === 'PTO') {
            $this->db->set('PTO_AVAILED', "PTO_AVAILED + {$amount}", false);
        } elseif ($type === 'PASS') {
            $this->db->set('PASS_AVAILED', "PASS_AVAILED + {$amount}", false);
        } elseif ($type === '2AC') {
            $this->db->set('PASS_AVAILED', "PASS_AVAILED + {$amount}", false);
            $this->db->set('SECONDA_AVAILED', "SECONDA_AVAILED + {$amount}", false);
        }

        $this->db
            ->where('EMPNO', $empno)
            ->where('ACYEAR', $year)
            ->update($this->table);
    }

    /**
     * Return the available balance for the provided type ('PASS' or 'PTO')
     */
    public function get_balance(int $empno, int $year, string $type): int
    {
        if ($type === 'PTO') {
            $row = $this->db
                ->select("COALESCE(PTO_TOTAL - PTO_AVAILED, 0) AS bal", FALSE)
                ->from($this->table)
                ->where('EMPNO', $empno)
                ->where('ACYEAR', $year)
                ->get()
                ->row_array();
        } else if($type === '2AC') {            
            $row = $this->db
                ->select("COALESCE(SECONDA_TOTAL - SECONDA_AVAILED, 0) AS bal", FALSE)
                ->from($this->table)
                ->where('EMPNO', $empno)
                ->where('ACYEAR', $year)
                ->get()
                ->row_array();
        } else {
            $row = $this->db
                ->select("COALESCE(PASS_TOTAL - PASS_AVAILED, 0) AS bal", FALSE)
                ->from($this->table)
                ->where('EMPNO', $empno)
                ->where('ACYEAR', $year)
                ->get()
                ->row_array();
        }

        return isset($row['BAL']) ? (int) $row['BAL'] : 0;
    }

    /**
     * True if the account has at least $required units available for the given type
     */
    public function has_balance(int $empno, int $year, string $type, int $required): bool
    {
        return $this->get_balance($empno, $year, $type) >= $required;
    }
}
