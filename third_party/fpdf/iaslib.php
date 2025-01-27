<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Iaslib {
		function create_qrcode(){
			//$this->load->library('qrcode/qrlib');
			include_once(APPPATH . 'libraries/qrcode/qrlib.php');
			 //set it to writable location, a place for temp generated PNG files
			$PNG_TEMP_DIR = 'temporary/qrcode/';
			//html PNG location prefix
			$PNG_WEB_DIR = base_url().'temporary/qrcode/';
			//ofcourse we need rights to create temp dir
			if (!file_exists($PNG_TEMP_DIR))
				mkdir($PNG_TEMP_DIR);
			$filename = $PNG_TEMP_DIR.'app_qrcode.png';  // berisilink

			 $errorCorrectionLevel = 'L';
			if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
				$errorCorrectionLevel = $_REQUEST['level'];

			$matrixPointSize =5;
			if (isset($_REQUEST['size']))
				$matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

			$isi_qrcode =  base_url();

			 QRcode::png($isi_qrcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

			 //display generated file
			//return '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';

			//echo $PNG_WEB_DIR ;

		}


	   // Example Use
	  /*
	  	$bIsConnected = check_internet_connection();
		$sText = ($bIsConnected) ? 'You are connected to the Internet.' : 'You are not connected to the Internet.';
		echo $sText;
       */
	   function check_internet_connection($sCheckHost = 'www.google.com')
		{
			return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
		}

	    function excelDateToDate($readDate){
			$UNIX_DATE = ($readDate - 25569) * 86400;
			$EXCEL_DATE = 25569 + ($UNIX_DATE / 86400);
			$UNIX_DATE = ($EXCEL_DATE - 25569) * 86400;
			return gmdate("Y-m-d", $UNIX_DATE);
		}

		//BEGIN-------------------------------------------EXPORT 2 CSV------------------------//
  		function array2csv(array &$array)
			{
			   if (count($array) == 0) {
				 return null;
			   }
			   ob_start();
			   $df = fopen("php://output", 'w');
			   fputcsv($df, array_keys(reset($array)));
			   foreach ($array as $row) {
				  fputcsv($df, $row);
			   }
			   fclose($df);
			   return ob_get_clean();
			}

		function download_send_headers($filename) {
			// disable caching
			$now = gmdate("D, d M Y H:i:s");
			header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
			header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
			header("Last-Modified: {$now} GMT");

			// force download
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");

			// disposition / encoding on response body
			header("Content-Disposition: attachment;filename={$filename}");
			header("Content-Transfer-Encoding: binary");
		}

		function export_csv($Q,$namafile='smartxp_csv_file'){
				   $this->download_send_headers($namafile.".csv");
				   if($Q->num_rows() > 0){
					 echo   $this->array2csv($Q->result_array());
				   }
			}

      //END-------------------------------------------EXPORT 2 CSV------------------------//
    // ada Field ID
	function ArrJson1($Q){
		$data = '';
		$i=0;
		$lisFields = $Q->list_fields();
		foreach($Q->result() as $line ){
		    $loop_no = $i+1;
			$arrData = array($loop_no);
			foreach ($lisFields as $field)
				{
				   array_push($arrData,$line->$field);
				}
			$data->rows[$i]['id']   = $line->id;
			$data->rows[$i]['data'] =$arrData ;
			$i++;
		}
		$Q->free_result();
		return $data;
	}

	// Tanpa Field ID
	function ArrJson2($Q){
		$data = '';
		$i=0;
		$lisFields = $Q->list_fields();
		foreach($Q->result() as $line ){
		    $loop_no = $i+1;
			$arrData = array();
			foreach ($lisFields as $field)
				{
				   array_push($arrData,$line->$field);
				}
			$data->rows[$i]['id']   =  $loop_no;
			$data->rows[$i]['data'] =$arrData ;
			$i++;
		}
		$Q->free_result();
		return $data;
	}
	function ArrJson3($Q){
		$data = '';
		$i=0;
		$lisFields = $Q->list_fields();
		foreach($Q->result() as $line ){
		    $loop_no = $i+1;
			$arrData = array($loop_no);
			foreach ($lisFields as $field)
				{
				   array_push($arrData,$line->$field);
				}
			$data->rows[$i]['id']   = $loop_no;
			//$data->rows[$i]['id']   = $line->id;
			$data->rows[$i]['data'] =$arrData ;
			$i++;
		}
		$Q->free_result();
		return $data;
	}

	function ArrJson4($Q){
		$data = '';
		$i=0;
		$lisFields = $Q->list_fields();
		foreach($Q->result() as $line ){
		    $loop_no = $i+1;
			$arrData = array();
			array_push($arrData,$loop_no);

			foreach ($lisFields as $field)
				{
				   array_push($arrData,$line->$field);
				}
			$data->rows[$i]['id']   =  $loop_no;
			$data->rows[$i]['data'] =$arrData ;
			$i++;
		}
		$Q->free_result();
		return $data;
	}
	
	function Data2JsonDet($Q,$nid='id'){
		$data = array();
			$nNo= 1 ;
			$arrStr='{}';
			$arrData=array();
			if ($Q->num_rows() > 0){
				$arrStr = '{"rows":[';
				foreach ($Q->result_array() as $row) {
					$data   = $row;
					$arrStr = $arrStr . '{"id":"' . $nNo . '","data":[' . $nNo . ',"' . join('","', $data) . '"]},';
					$nNo    = $nNo + 1;
				}
				$arrStr = substr($arrStr, 0, strlen($arrStr) - 1);
				$arrStr = $arrStr . "]}";
			}
			$Q->free_result();
			return $arrStr;
	}

   // belum dipakai
    function get_whxss($id){
		 $this->config->set_item('global_xss_filtering', FALSE);
		 $xvar =  $this->input->post($id);
		 $this->config->set_item('global_xss_filtering', TRUE);
		 return $xvar;
	}

    // Fungsi untuk meremove separator koma [ membalikan fungsi internal number_format ]  misal 250,000  => 250000
    function remove_koma($cnilai){ 
	if ($cnilai == '') { return 0 ;}
	return str_replace(',','',$cnilai);
	}

	function left($string,$count) {$string = substr($string,0,$count);return $string;}

	function right($string,$count) {$string = substr($string, -$count, $count);return $string;}

	function getLastMonth($nMonth){		 $nLastMonth = $nMonth -1 ;
		 if ($nMonth==1){$nLastMonth = 12;}
		 if ($nLastMonth <= 9){$nLastMonth ='0'.$nLastMonth;}

		 return $nLastMonth ;		 }
	function getLastYear($nMonth,$nYear){
		 $nLastYear = $nYear ;
		 if ($nMonth==12){$nLastYear = $nYear -1;}
		 return $nLastYear ;
		 }

	function getLastDate($nMonth,$nYear){return date('Y-m-d',strtotime('-1 second',strtotime($nMonth.'/01/'.$nYear.' 00:00:00')));}
    function getLast2Date($nMonth,$nYear){return date('Y-m-d',strtotime('-1 second',strtotime($nMonth.'/01/'.$nYear.' 00:00:00')));}

    function xl2timestamp($xl_date)
		{
		$timestamp = ($xl - 25569) * 86400;
		return $timestamp;
		}
    //---------------Untuk merubah enter menjadi <br> supaya tidak error di grid javascript
    function enter2br($text){
		return str_replace ( "\r\n", "<br>", $text ) ;

		}

	function getMonthName($nMonth){    	 $cMonth ='None';    	switch($nMonth){    	   case 1 :
    	     $cMonth = 'JAN';
    	     break;
    	   case 2 :
    	     $cMonth = 'FEB';
    	     break;
    	   case 3 :
    	     $cMonth = 'MAR';
    	     break;
    	   case 4 :
    	     $cMonth = 'APR';
    	     break;
    	   case 5 :
    	     $cMonth = 'MAY';
    	     break;
    	   case 6 :
    	     $cMonth = 'JUN';
    	     break;
    	   case 7 :
    	     $cMonth = 'JUL';
    	     break;
    	   case 8 :
    	     $cMonth = 'AUG';
    	     break;
    	   case 9 :
    	     $cMonth = 'SEP';
    	     break;
    	   case 10 :
    	     $cMonth = 'OCT';
    	     break;
    	   case 11 :
    	     $cMonth = 'NOV';
    	     break;
    	   case 12 :
    	     $cMonth = 'DEC';
    	     break;

    	}

    	return $cMonth;    	}

	public function getTanggal($tgl){
	/*	$oDate = strtotime($tgl);
		$sDate = date("m d y",$oDate);
		*/
		if ($tgl == '0000-00-00' or $tgl == '') {
			$bulan = '';
		} else {
			$bulan =  intval(substr($tgl,8,2)).' ' .ucwords(strtolower($this->getNamaBulan(intval(substr($tgl,5,2))))). ' '.intval(substr($tgl,0,4)) ;
		}
		return $bulan ;
	}

    function getNamaBulan($nMonth){    	 $cMonth ='None';    	switch($nMonth){    	   case 1 :
    	     $cMonth = 'JANUARI';
    	     break;
    	   case 2 :
    	     $cMonth = 'FEBRUARI';
    	     break;
    	   case 3 :
    	     $cMonth = 'MARET';
    	     break;
    	   case 4 :
    	     $cMonth = 'APRIL';
    	     break;
    	   case 5 :
    	     $cMonth = 'MEI';
    	     break;
    	   case 6 :
    	     $cMonth = 'JUNI';
    	     break;
    	   case 7 :
    	     $cMonth = 'JULI';
    	     break;
    	   case 8 :
    	     $cMonth = 'AGUSTUS';
    	     break;
    	   case 9 :
    	     $cMonth = 'SEPTEMBER';
    	     break;
    	   case 10 :
    	     $cMonth = 'OKTOBER';
    	     break;
    	   case 11 :
    	     $cMonth = 'NOVEMBER';
    	     break;
    	   case 12 :
    	     $cMonth = 'DESEMBER';
    	     break;

    	}

    	return $cMonth;    	}


    	function kekata($x) {
		$x = abs($x);
		$angka = array("", "satu", "dua", "tiga", "empat", "lima",
		"enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($x <12) {
		$temp = " ". $angka[$x];
		} else if ($x <20) {
		$temp = $this->kekata($x - 10). " belas";
		} else if ($x <100) {
		$temp = $this->kekata($x/10)." puluh". $this->kekata($x % 10);
		} else if ($x <200) {
		$temp = " seratus" . $this->kekata($x - 100);
		} else if ($x <1000) {
		$temp = $this->kekata($x/100) . " ratus" . $this->kekata($x % 100);
		} else if ($x <2000) {
		$temp = " seribu" . $this->kekata($x - 1000);
		} else if ($x <1000000) {
		$temp = $this->kekata($x/1000) . " ribu" . $this->kekata($x % 1000);
		} else if ($x <1000000000) {
		$temp = $this->kekata($x/1000000) . " juta" . $this->kekata($x % 1000000);
		} else if ($x <1000000000000) {
		$temp = $this->kekata($x/1000000000) . " milyar" . $this->kekata(fmod($x,1000000000));
		} else if ($x <1000000000000000) {
		$temp = $this->kekata($x/1000000000000) . " trilyun" . $this->kekata(fmod($x,1000000000000));
		}
		return $temp;
	}

	function terbilang($x, $style=4) {
		if($x<0) {
		$hasil = "minus ". trim($this->kekata($x));
		} else {
		$hasil = trim($this->kekata($x));
		}
		switch ($style) {
		case 1:
		$hasil = strtoupper($hasil);   // Upper Cash
		break;
		case 2:
		$hasil = strtolower($hasil);    // Loower Cash
		break;
		case 3:
		$hasil = ucwords($hasil);
		break;
		default:
		$hasil = ucfirst($hasil);
		break;
		}
		return $hasil;
	}

	// Terbilang Bahasa ingris
	function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . $this->convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . $this->convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= $this->convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

}

?>