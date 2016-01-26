<?php
/* 
* based on the PHP script found at http://webapp.org.ua/dev/make-use-of-google-spreadsheets-in-your-php-scripts/
*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

    function download_sheet($sp_key, $worksheet) {

     // construct Google spreadsheet URL:
 	 	$url = "https://spreadsheets.google.com/feeds/cells/{$sp_key}/{$worksheet}/public/basic?alt=json-in-script&callback=_";

        // UA
        $userAgent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9";
        $curl = curl_init();
        // set URL
        curl_setopt($curl, CURLOPT_URL, $url);

        // setting curl options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// return page to the variable
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($curl, CURLOPT_TIMEOUT, 30000); // times out after 4s
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

        // grab URL and pass it to the variable
        $str = curl_exec($curl);
        curl_close($curl);

        // extract pure JSON from response
        //$str  = substr($str, 2, strlen($str) - 4);
        //$data = json_decode($str, true);
		
		// extract pure JSON from response << This way makes it work
		$str = mb_ereg_replace("// API callback\n_\(", "", $str);
		$str = mb_ereg_replace("\)\;$", "", $str);
		$data = json_decode($str, true);

		$id_marker = "https://spreadsheets.google.com/feeds/cells/{$sp_key}/{$worksheet}/public/basic/";
        $entries   = $data["feed"]["entry"];

        $res = array();
        foreach($entries as $entry) {
           $content = $entry["content"];
           $ind = str_replace($id_marker."R", "", $entry["id"]['$t']);
           $ii  = explode("C", $ind);
           $res[$ii[0]-1][$ii[1]-1] = $entry["content"]['$t'];
        }

        return $res;
    }

	$sp_key = "YOUR_GOOGLE_SHEET_KEY";  // 
	//$worksheet = '1';					// this is the number of the workSHEET within. "1" is the first sheet, "2" is the second, and so on.

	switch($_POST['mfr']) {				// change these variables as needed
		case 'nike':
			$worksheet = '1';
			break;	
		case 'reebok':
			$worksheet = '2';
			break;			
		case 'adidas':
			$worksheet = '3';
			break;	
		}

$data = download_sheet($sp_key, $worksheet);

$partnum = $_POST['partnum'];
$qty_ordered = $_POST['qty_ordered'];

function getQty($id, $array) { 
   foreach ($array as $key => $val) {
       if ($val[0] === $id) {
		   return $val[1];
       }
   }
   return null;
}


// check and/or set session var
$rightnow = time();

if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}

if(!isset($_SESSION['inventory'][$worksheet])) {  // if the session var for this mfr/worksheet is not set, create it now

	$_SESSION['inventory'][$worksheet]['inventory_array'] = $data; 	// grab all new values
	$_SESSION['inventory'][$worksheet]['timestamp'] = $rightnow; // set the timestamp
	
} else { // else if it IS set, check the timestamp in it	
	if( ($_SESSION['inventory'][$worksheet]['timestamp'] + 600) < $rightnow) {  // if the last timestamp is over 10 minutes less than the current "time()"
			$_SESSION['inventory'][$worksheet]['inventory_array'] = $data; 	// grab all new values
			$_SESSION['inventory'][$worksheet]['timestamp'] = $rightnow; // re-set the timestamp
			$the_session_id = session_id();
	}		
}

$qty = getQty($partnum, $_SESSION['inventory'][$worksheet]['inventory_array']); // hopefully this way gets it from the session

if($qty >= $qty_ordered) {
	$qtystyle = ' style="color:green;"';
} else {
	$qtystyle = ' style="color:red;"';	
}

if(is_numeric($qty)) {
	echo 'In stock: <b'.$qtystyle.'>'.$qty.'</b>';
} else {
	echo '???';	
}

?>
