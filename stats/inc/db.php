<?php
//connection1
try 
{
	$DB_con = new PDO("mysql:host=192.168.1.64;dbname=sale;charset=cp1251", 'user', 'user');
	$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) 
{
	echo $e->getMessage();
}
//<<connection
date_default_timezone_set('Asia/Novokuznetsk');
?>