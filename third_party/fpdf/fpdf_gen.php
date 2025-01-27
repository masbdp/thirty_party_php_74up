<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Fpdf_gen {
		
	public function __construct() {
		
		require_once APPPATH.'libraries/fpdf.php';
		
		$pdf = new FPDF();
		$CI =& get_instance();
		$CI->fpdf = $pdf;
		
	}
	
	
}


