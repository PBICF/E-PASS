<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (! class_exists('PDF')) {
    require_once(APPPATH . 'libraries/tfpdf/tfpdf.php');
}


final class Second_AC_class_pass extends tFPDF {
    const MARGIN_LEFT = 10;
    const MARGIN_TOP = 10;
    const FONT = 'Arial';
    const HINDI_FONT = 'Krishna';
    const FONT_SIZE_LARGE = 10;
    const FONT_SIZE_MEDIUM = 11;
    const FONT_SIZE_SMALL = 8;

    const SHOW_PREVIEW_IMAGE = false;

    public function __construct($orientation='L', $unit='mm', $size=array(295, 148)) {
        parent::__construct($orientation, $unit, $size);
        
        $this->AddFont(self::HINDI_FONT, '', self::HINDI_FONT . '.ttf', true);
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(TRUE, 10);
        $this->SetFont(self::FONT, 'B', self::FONT_SIZE_SMALL);
    }

    public function set_pass_style() {
        $this->SetAutoPageBreak(FALSE, 0); # No auto page break for fixed layouts
        $this->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_LEFT);
        return $this;
    }

    public function employee_number($employee_number) {
        $this->add_text($employee_number, 62, 28, self::FONT_SIZE_SMALL);
        $this->add_text($employee_number, 152, 18, self::FONT_SIZE_SMALL);

        return $this;
    }

    public function deportment($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 100, 48, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function journey_type($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 35, 37.5, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 126, 28.5, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function pay_rate($pay_rate) {
        $this->add_text($pay_rate, 55, 58, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function station($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 140, 49, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function date($date) {
        $this->add_text($date, 70, 60, self::FONT_SIZE_SMALL);
        $this->add_text($date, 184, 49, self::FONT_SIZE_SMALL);
        $this->add_text($date, 66, 94, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function pessenger($line_1 = '', $line_2 = '') {
        $pessenger = preg_replace('/\s+/', ' ', $line_1 ?? '') . ' ' . preg_replace('/\s+/', ' ',  $line_2 ?? '');
        $this->add_text($pessenger, 48, 64, self::FONT_SIZE_SMALL, self::FONT, 55);
        $this->add_text($pessenger, 114, 54, self::FONT_SIZE_SMALL, self::FONT, 80, 3);
        return $this;
    }

    public function desigination($designation) {
        $this->add_text($designation, 86, 56, self::FONT_SIZE_SMALL);
        $this->add_text($designation, 110, 60, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function from($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 18, 71, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 135, 60, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function destination($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 70, 71, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 186, 60, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function via($via_routes) {
        $this->add_text($via_routes, 44, 78, self::FONT_SIZE_SMALL, self::FONT, 30, 3);
        $this->add_text($via_routes, 108, 66, self::FONT_SIZE_SMALL, self::FONT, 30, 3);
        return $this;
    }

    public function return_from($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 70, 78, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 138, 66, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function return_to($english_text, $hindi_text) {
        $this->mixed_text($hindi_text, $english_text, 44, 84, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 156, 66, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function return_via($via_routes) {
        $this->add_text($via_routes, 40, 88, self::FONT_SIZE_SMALL, self::FONT, 30, 3);
        $this->add_text($via_routes, 168, 71, self::FONT_SIZE_SMALL, self::FONT, 100);
        return $this;
    }

    public function over_railway($railway = 'IR') {
        $this->add_text($railway, 120, 74, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function outward_journey_dt($outward_journey_dt) {
        $this->add_text($outward_journey_dt, 180, 77, self::FONT_SIZE_SMALL);
        return $this;
    }
    
    public function return_journey_dt($return_journey_dt) {
        $this->add_text($return_journey_dt, 66, 101, self::FONT_SIZE_SMALL);
        $this->add_text($return_journey_dt, 176, 82, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function train_type($train_type) {
        $this->add_text($train_type, 0, 0, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function issue_reson($english_text, $hindi_text = '') {
        $this->mixed_text($hindi_text, $english_text, 70, 116, self::FONT_SIZE_SMALL);
        $this->mixed_text($hindi_text, $english_text, 130, 106, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function authority($authority) {
         $this->add_text($authority, 52, 122, self::FONT_SIZE_SMALL);
        $this->add_text($authority, 158, 122, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function break_journey_via($bjset1, $bjset2 = '', $bjset3 = '', $bjset4 = '') {
        $break_journey_via = trim(implode(', ', array_filter(array($bjset1, $bjset2, $bjset3, $bjset4))));
        $this->add_text($break_journey_via, 250, 18, self::FONT_SIZE_SMALL, self::FONT, 40, 3);
        $this->add_text($break_journey_via, 68, 46, self::FONT_SIZE_SMALL, self::FONT, 40, 3);
        return $this;
    }

    public function break_return_journey_via($break_return_journey_via) {
        $this->add_text($break_return_journey_via, 0, 0, self::FONT_SIZE_SMALL);
        return $this;
    }

    public function string($text, $x, $y, $type = '') {
        $this->add_text($text, $x, $y, self::FONT_SIZE_SMALL, self::FONT, 100, 4, $type);
        return $this;
    }
    
    private function add_text($text, $x, $y, $font_size = self::FONT_SIZE_MEDIUM, $font = self::FONT, $w= 100, $h = 4, $type = '', $align = 'L') {
        $this->SetFont($font, $type, $font_size);
        $this->SetXY($x, $y);
        $this->MultiCell($w, $h, $text, 0, $align);
    }

    public function mixed_text($hindi, $english, $x, $y, $fontSize = 10)
    {
        // 1. Hindi text first
        $this->SetFont(self::HINDI_FONT, '', $fontSize);
        $this->SetXY($x, $y);
        $this->Write(5, $hindi);

        // 2. Calculate Hindi text width
        $hindi_width = $this->GetStringWidth($hindi);

        // 3. Add small space padding
        $padding = 0;
        $english_text = ($hindi_width > 0) ? ' / ' . $english : '  ' . $english;

        // 4. English text next — dynamically placed
        $this->SetFont(self::FONT, '', $fontSize);
        $this->SetXY($x + $hindi_width + $padding, $y);
        $this->Write(5, $english_text);
    }

    public function generate(array $params = array()) {
        $this->AddPage('P', 'A3', 0);
        $this->SetDisplayMode('real', 'default');
		
        if (self::SHOW_PREVIEW_IMAGE ) {
            $this->Image(base_url('assets/images/second-class-ac.jpeg'), 0, 0, 295, 143);
        }

		$this->employee_number($params['ENO']);	
		$this->date(date('d/m/Y'));

		$this->deportment($params['DEPTSTR'], $params['DEPTSTR_HINDI']);
		$this->station($params['STATIONSTR'], $params['STATIONSTR_HINDI']);
		$this->journey_type($params['RETURNSTR'], $params['RETURNSTR_HINDI']);
		//$this->pay_rate(''); ++

	    $this->pessenger($params['DEPEND1'], $params['DEPEND2']);
		$this->desigination($params['PDESIG']);
		
		$this->from($params['FRSTN'], $params['FRSTN_HINDI']);
        $this->destination($params['TOSTN'], $params['TOSTN_HINDI']);

		$this->via($params['VIASTNS']);
		$this->return_from($params['TOSTN'], $params['TOSTN_HINDI']);
		$this->return_to($params['FRSTN'], $params['FRSTN_HINDI']);
		$this->return_via($params['RVIASTNS']);

		$this->over_railway($params['RLYSET']);
		$this->outward_journey_dt('---');
		$this->return_journey_dt($params['PVALIDTO']);

		$this->issue_reson($params['TNAME'], $params['TNAMESTR_HINDI']);
		$this->authority($params['SIGNSTR']);

		$this->break_journey_via($params['BJSET1'], $params['BJSET2'], $params['BJSET3'], $params['BJSET4']);
		
		// $this->string($params['STR10'], 220, 92, 'B');
		// $this->string($params['HEADER3'], 220, 96, 'BU');
		// $this->string($params['STR3'], 220, 104, 'B');

		// $this->string($params['STR2'], 220, 108, 'B');
		// $this->string($params['STR1'], 220, 112, 'B');

		// $this->string($params['HEADER2'], 220, 118, 'BU');
        
        return $this->Output('ticket.pdf', 'I');
    }
}