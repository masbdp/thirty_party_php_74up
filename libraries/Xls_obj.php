<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Xls_obj {
		
	public function __construct() {
		
		
		require_once APPPATH.'third_party/PHPExcel/PHPExcel.php';
		
		$xls = new PHPExcel();
		$CI =& get_instance();
		$CI->excel = $xls;
		
	}
	
	
}


