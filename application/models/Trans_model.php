<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Employee_model $employee
 * @property Account_model $account
 * @property Pass_type_model $pass_type
 */
class Trans_model extends CI_Model {

    private string $table = 'TRANS';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model', 'employee');
        $this->load->model('Account_model', 'account');
        $this->load->model('Pass_type_model', 'pass_type');
    }

    public function all()
    {
        return $this->db->get($this->table)->result_array();
    }

    public function find(int $passno)
    {
        return $this->db->get_where($this->table, ['PASSNO' => $passno])->row_array();
    }

    public function find_by_empno(int $empno)
    {
        return $this->db->get_where($this->table, ['EMPNO' => $empno])->result_array();
    }

    public function next_pass_number($employee_no)
    {
        // First day of current month
        $start = date('Y-m-01');

        $this->db->select('MAX(TRANS.PASSNO) AS max_passno', false)
                ->from('TRANS')
                ->join('EMP', 'EMP.ECLASS = TRANS.TCLASS')
                ->where('EMP.EMPNO', $employee_no)
                ->where("TRANS.TDATE >= DATE '{$start}'", null, false)
                ->where("TRANS.TDATE < ADD_MONTHS(DATE '{$start}', 1)", null, false);

        $row = $this->db->get()->row();

        return ($row && $row->MAX_PASSNO)
            ? ((int)$row->MAX_PASSNO + 1)
            : 1;
    }


    public function create_pass(array $data)
    {
        $today = date('d/m/Y');
        $employee = $this->employee->find($data['empno']);
        $validity_from = format_date($today);
        $validity_to = format_date($data['validity_to']);
        $after_balance = $data['AFTER_BALANCE'] ?? 0;
        $different_return_via = isset($data['different_return_via']) ? 'Y' : 'N';
        $is_companion = isset($data['COMPANIONIND']) ? 'Y' : 'N';

        $viastns = strtoupper(implode(',', filter_array($data['via'])));
        $rviastns = $different_return_via === 'Y' ? strtoupper(implode(',', filter_array($data['return_via']))) : strtoupper(implode(',', filter_array($data['via'])));
        $travellers = $this->employee->family->findMembers($data['empno'], $data['members']);
        
        list($depend1, $depend2) = build_relation_string($travellers);
        $break_journey = array_chunk($data['break_journey'], 5);
        list($bjset1, $bjset2, $bjset3, $bjset4) = array_pad($break_journey, 4, []);

        $should_debit = $this->pass_type->should_debit_account($data['pass_type']);

        $this->db->trans_begin();

        $this->db
            ->set('TDATE', "TO_DATE('{$today}', 'DD/MM/YYYY')", false)
            ->set('ENO', $data['empno'])
            ->set('PASSNO', $data['pass_no'])
            ->set('FRSTN', $data['from_station_name'])
            ->set('TOSTN', $data['to_station_name'])
            ->set('TTYPE', $data['pass_type'])
            ->set('VALIDFR', "TO_DATE('{$validity_from}', 'DD/MM/YYYY')", false)
            ->set('VALIDTO', "TO_DATE('{$validity_to}', 'DD/MM/YYYY')", false)
            ->set('TCLASS', $employee['ECLASS'])
            ->set('DEPEND1', $depend1)
            ->set('DEPEND2', $depend2)
            ->set('VIASTNS', $viastns)
            ->set('RVIASTNS', $rviastns)
            ->set('RLYSET', 'IR')
            ->set('BJSET1', implode(',', filter_array($bjset1)))
            ->set('BJSET2', implode(',', filter_array($bjset2)))
            ->set('BJSET3', implode(',', filter_array($bjset3)))
            ->set('BJSET4', implode(',', filter_array($bjset4)))
            ->set('RETURNIND', $data['single_return'])
            ->set('FRSTNCD', strtoupper($data['from_station_code']))
            ->set('TOSTNCD', strtoupper($data['to_station_code']))
            ->set('COMPANIONIND', $is_companion)
            ->set('ACYEAR', $data['account_year'])
            ->set('DIFF_RVIA_IND', $different_return_via)
            ->set('AFTER_BALANCE', $after_balance)
            ->set('FRSTN_HINDI', $data['from_station_name_hindi'])
            ->set('TOSTN_HINDI', $data['to_station_name_hindi'])
            ->set('USER_NAME', $data['office_use_only'])
            ->set('REMARKS1', $data['remarks1'])
            ->set('REMARKS2', $data['remarks2'])
            ->set('USER_REMARKS', $data['office_use_only'])
            ->set('CELLNO', $employee['CELLNO'])
            ->set('R1', $data['home_foreign']);

        foreach(filter_array($data['via']) as $index => $station) {
            $this->db->set('VIA' . ($index + 1), empty($station) ? null : strtoupper($station));
        }

        if($different_return_via === 'N') {
            foreach(array_reverse(filter_array($data['via'])) as $index => $station) {
                $this->db->set('RVIA' . ($index + 1), empty($station) ? null : strtoupper($station));
            }
        } else {
            foreach($data['return_via'] as $index => $station) {
                $this->db->set('RVIA' . ($index + 1), empty($station) ? null : strtoupper($station));
            }
        }

        foreach(filter_array($data['break_journey']) as $index => $station) {
            $this->db->set('B' . ($index + 1), empty($station) ? null : strtoupper($station));
        }

        $this->db->insert($this->table);

        if ($should_debit) {
            $this->account->debit(
                $data['empno'], 
                $data['account_year'], 
                $data['account_type'], 
                $data['single_return']
            );
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

}