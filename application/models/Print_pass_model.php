<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_pass_model extends CI_Model {

    protected $table = 'PRINT_PASS';
    /**
     * Get a single pass record by pass number
     *
     * @param string|int $passno
     * @return array|bool
     */
    public function _get_pass(int $passno) {
        return $this->db->select("
                PASSNO, TTYPE, PVALIDFR, PVALIDTO, VIASTNS, RVIASTNS, RLYSET, BJSET1, BJSET2, BJSET3,
                BJSET4, DEPEND1, DEPEND2, DEPEND3, DEPEND4, DEPEND5, REMARKS1, REMARKS2, FRSTN, FRSTN_HINDI, TOSTN, TOSTN_HINDI,
                RETURNSTR, RETURNSTR_HINDI, ENO, PNAME, PDESIG, PUNIT, PTKTNO, POFFICE, TNAME, TNAMESTR_HINDI, PCLASS, COMPANIONIND,
                DEPTSTR, DEPTSTR_HINDI, STATIONSTR, STATIONSTR_HINDI, SIGNSTR, OJ_LABEL, RJ_LABEL, COMPANION_STR, STR10, HEADER3, STR3,
                HEADER2, STR2, HEADER1, STR1, DEPSTR1, DEPSTR2, DEPSTR3, DEPSTR4, DEPSTR5, DEPSTR6, DEPSTR7, DEPSTR8, DEPSTR9,
                PVALIDTO_SINGLE, PVALIDTO_RETURN, IND1
            ")
            ->where('PASSNO', $passno)
            ->get($this->table)
            ->row_array();
    }

    public function get_pass(int $passno) {
        return $this->db->select("*")
            ->where('PASSNO', $passno)
            ->get($this->table)
            ->row_array();
    }

    public function get_passes_by_empno(int $empno) {
        return $this->db->select('PASSNO, ENO, TTYPE, FRSTN, TOSTN, PVALIDFR, PVALIDTO, PCLASS, TCANCEL, DEPEND1, DEPEND2')
            ->where('ENO', $empno)
            ->order_by('PASSNO', 'ASC')
            ->get($this->table)
            ->result_array();
    }
}
