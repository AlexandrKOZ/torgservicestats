<?php
//require_once("db.php");


function formatMoney($number, $fractional=false) { 
    if ($fractional) { 
        $number = sprintf('%.2f', $number); 
    } 
    while (true) { 
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
        if ($replaced != $number) { 
            $number = $replaced; 
        } else { 
            break; 
        } 
    } 
    return $number; 
} 


function get_files($DB_con, $form_date) {
	$stmt = $DB_con->query("SELECT * FROM files WHERE file_name LIKE '%$form_date%' ORDER BY file_name ASC");
	$files_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	echo ("Всего файлов: <b>".count($files_data)."</b><br>");
	for ($i=0; $i<count($files_data); $i++) {
		//print_r($files_data[$i]);
		$file_bytes = $files_data[$i][file_size];
		//echo $file_bytes;
		$file_kbytes = $file_bytes/1024;
		$file_kbytes = intval($file_kbytes);
		print ($files_data[$i][file_name]." $file_kbytes kb"."<br>"); 
	}
}

function get_shops_list ($DB_con) {
		$stmt = $DB_con->query("SELECT * FROM shops");
		$shops_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($i=0; $i<count($shops_data); $i++) {
			$shop_id = $shops_data[$i][shop_id];
			$shop_real_name = $shops_data[$i][shop_real_name];
			echo ("
					<button shop-id='$shop_id' pushed='0' onclick='return false;' class='btn btn-warning shops_select' type='submit'>$shop_real_name</button>
				"); 
		}
		echo ("
					<button shop-id='999' pushed='0' onclick='return false;' class='btn btn-warning shops_select all_shops' type='submit'>Все магазины</button>
				");
}


function parseTree($tree, $root = 0) {
    $return = array();
    foreach($tree as $child => $parent) {
        if($parent == $root) {
            unset($tree[$child]);
            $return[] = array(
                'name' => $child,
                'children' => parseTree($tree, $child)
            );
        }
    }
    return empty($return) ? null : $return;    
}


function printTree($tree, $DB_con) {
    if(!is_null($tree) && count($tree) > 0) {
        echo '<ol>';
        foreach($tree as $node) {
			$code = $node[name];
			$name = $utypes_data[0][utype_name];
			$stmt = $DB_con->query("SELECT utype_name, utype_code, utype_parent FROM units_types WHERE utype_code='$code'"); 
			$utypes_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
			//print_r($utypes_data);
            echo "<li class=utype data-value='$code'>".$utypes_data[0][utype_name];
            printTree($node['children'], $DB_con);
            echo '</li>';
        }
        echo '</ol>';
    }
}

function units_tree ($DB_con) {
		$stmt = $DB_con->query("SELECT utype_code, utype_parent FROM units_types"); 
		$utypes_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
		for ($i=0; $i<count($utypes_data); $i++) {
			$code = $utypes_data[$i][utype_code];
			$parent = $utypes_data[$i][utype_parent];
			$arr["$code"]=$parent;
		}
		// print ("<pre>");
		// print_r($arr);
		// print ("</pre>");
		$result = parseTree($arr);
		printTree($result, $DB_con);
}


function get_childs ($utype, $DB_con) {
	$stmt = $DB_con->query("SELECT utype_code, utype_name, utype_parent FROM units_types WHERE utype_parent=$utype ORDER BY utype_code"); 
	$utypes_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	for ($i=0; $i<count($utypes_data); $i++) {
		$utype_code = $utypes_data[$i][utype_code];
		$utype_name = $utypes_data[$i][utype_name];
		$utype_codes[]=array(
				'code' => $utype_code,
                'children' => get_childs($utype_code, $DB_con)
            );
	}
	return $utype_codes;
}


$GLOBALS[types]=array();
function parse_childs ($arr) {
	if(!is_null($arr) && count($arr) > 0) {
        foreach($arr as $node) {
			// print("<pre>");
			// print_r($node);
			// print ("</pre>");
			// print("<br>");
			$GLOBALS[types][]=$node[code];
			parse_childs($node[children]);
        }
    }
	sort ($GLOBALS[types]);
	// print ("<pre>");
	// print_r($GLOBALS[types]);
	// print ("</pre>");
	return $GLOBALS[types];
}


















?>