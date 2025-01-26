<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Fpdf_obj {
		
	public function __construct() {
		
		require_once APPPATH.'third_party/fpdf/fpdf181.php';
		$pdf = new FPDF181();
		$CI =& get_instance();
		$CI->fpdf = $pdf;
		
	}
	
	
}


