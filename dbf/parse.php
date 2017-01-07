<?php
header('Content-Type: text/html; charset=cp1251');
require_once('db.php');

function convert_to ( $source, $target_encoding )
    {
    // detect the character encoding of the incoming file
    $encoding = mb_detect_encoding( $source, "auto" );
       
    // escape all of the question marks so we can remove artifacts from
    // the unicode conversion process
    $target = str_replace( "?", "[question_mark]", $source );
       
    // convert the string to the target encoding
    $target = mb_convert_encoding( $target, $target_encoding, $encoding);
       
    // remove any question marks that have been introduced because of illegal characters
    $target = str_replace( "?", "", $target );
       
    // replace the token string "[question_mark]" with the symbol "?"
    $target = str_replace( "[question_mark]", "?", $target );
   
    return $target;
    }

function parse_dbf($filename, $DB_con) {
			$shop_name=substr($filename, 4, 2);
			$shop_name=convert_to($shop_name, 'UTF8');
			$file_size=filesize("./files/$filename");
			try {
					$DB_con->beginTransaction();
					$stmt = $DB_con->query("SELECT * FROM shops WHERE shop_name='$shop_name'");
					$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
					if (count($check_data)<1) {
							$stmt = $DB_con->query("INSERT INTO shops (shop_name) VALUES ('$shop_name')");
							$lastId = $DB_con->lastInsertId('shops_count');
						}
					else {
							$lastId = $check_data[0][shop_id];
					}
					//print_r($check_data);
					//echo (count($check_data)."|$check_data[0][shop_id]|$lastId|$shop_name|\n");
					$stmt = $DB_con->query("SELECT * FROM files WHERE file_name='$filename'");
					$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
					if (count($check_data)>0) { //Этот файл уже добавлен в базу
							$err_log_string=date("d.m.Y H:i:s")." ".$filename."ALREADY EXIST \n";
							file_put_contents("sales_log.txt", $err_log_string, FILE_APPEND);
							unlink("./files/$filename");
							$DB_con->commit();
							return 'file_already_parsed';
						}
					$db = dbase_open("./files/$filename", 0);
					if($db)
					{
						$record_numbers = dbase_numrecords($db);
						for ($i = 1; $i <= $record_numbers; $i++)
						{
							$row = dbase_get_record_with_names($db, $i);
							if ($row[OPER]=='+s-' OR $row[OPER]=='+%-')  continue;
							flush();
							$date = strtotime($row[DATEO]);
							$date=date("U", $date);
							$date=$date+$row[TIMEO];
							$normal_date = date("Y-m-d H:m:s", $date);
							$stmt = $DB_con->prepare("INSERT INTO sales (sale_code, sale_name, sale_price, sale_quantity, sale_operation_type, sale_cashbox, sale_checknum, sale_cashless, sale_client_card_id, sale_date_unix, sale_money_count, sale_date, sale_discount) VALUES (:sale_code, :sale_name, :sale_price, :sale_quantity, :sale_operation_type, :sale_cashbox, :sale_checknum, :sale_cashless, :sale_client_card_id, :sale_date_unix, :sale_money_count, :sale_date, :sale_discount)");
							$stmt->execute(array
										(
											':sale_code' => $row[CODE],
											':sale_name' => $row[NAME],
											':sale_price' => $row[PRICE],
											':sale_quantity' => $row[KOL],
											':sale_operation_type' => $row[OPER],
											':sale_date_unix' => $date,
											':sale_cashbox' => $lastId,
											':sale_checknum' => $row[CHECKNUM],
											':sale_cashless' => $row[BEZNAL],
											':sale_client_card_id' => $row[CLIENT],
											':sale_money_count' => $row[SUMD],
											':sale_date' => $normal_date,
											':sale_discount' => $row[SKIDPROC]
										)
									);
						}
						$stmt = $DB_con->query("INSERT INTO files (file_name, file_size) VALUES ('$filename', '$file_size')");
						$DB_con->commit();
						$dbf_date=date("d-m-Y");
						mkdir ("./dbf_dump/$dbf_date");
						copy("./files/$filename", "./dbf_dump/$dbf_date/$filename");
						unlink("./files/$filename");
						$success_log=date("d.m.Y H:i:s")." ".$filename." SUCCESS \n";
						file_put_contents("sales_log.txt", $success_log, FILE_APPEND);
						return true;
					}
			}	catch(PDOException $e) {
					$DB_con->rollBack();
					$PDO_err = $e->getTraceAsString();
					$err_log_string=date("d.m.Y H:i:s")." ".$filename."\n".$e."\n\n".$PDO_err."\n\n\n";
					file_put_contents("sales_log.txt", $err_log_string, FILE_APPEND);
					copy("./files/$filename", "./crashed/$filename");
					unlink("./files/$filename");
					return false;
				}
	} 
$dir=opendir("./files");
while (($file = readdir($dir)) !== false) {
		if ($file=='.' OR $file=='..') continue;
		echo parse_dbf($file, $DB_con)."_";
	}	
?>