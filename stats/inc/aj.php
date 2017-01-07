<?php
header('Content-Type: text/html; charset=cp1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
require_once('db.php');
require_once('functions.php');

if ($_REQUEST[action]=='get_files') {
	if ($_REQUEST['date']=='') {
		die ('Не выбрана дата'); 
	}
	else {
		$date_arr=explode('/', $_REQUEST[date]);
		$date="$date_arr[0]$date_arr[1]$date_arr[2]";
		get_files($DB_con, $date);
	}

}

?> 