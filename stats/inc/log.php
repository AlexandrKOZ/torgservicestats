<?php
header('Content-Type: text/html; charset=cp1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
require_once('db.php');
require_once('functions.php');
$log_ip=$_SERVER[REMOTE_ADDR];
$date = date("Y-m-d H:m:s");
$log_date=$date;
$log_action=$_REQUEST[chart_type];
$stmt = $DB_con->prepare("INSERT INTO log (log_ip, log_date, log_action) VALUES (:log_ip, :log_date, :log_action)");
$stmt->execute(array
										(
											':log_ip' => $log_ip,
											':log_date' => $log_date,
											':log_action' => $log_action
										)
									);
?>