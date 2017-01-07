<?php
//TODO
header('Content-Type: text/html; charset=cp1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
$time_start = microtime(true);
require_once('db.php');
require_once('functions.php');
echo("<br>");
//print_r($_REQUEST);
if (!$_REQUEST[chart_type]) {
	die ('Не выбран тип отчета');  
}
if (!$_REQUEST[shop_list]) {
	die ('Не выбран магазин'); 
}

if ($_REQUEST[start_time]=='') {
	die ('Не выбрано время начала'); 
}

if ($_REQUEST[end_time]=='') {
	die ('Не выбрано время конца'); 
}
if ($_REQUEST[utype]=='') {
	die ('Не выбрана группа товаров');
}

$utype_str = $_REQUEST[utype];
$utype_arr=explode('=', $utype_str);
$utype=$utype_arr[1];
	$stmt = $DB_con->prepare("SELECT utype_name FROM units_types WHERE utype_code ='$utype'");
	$stmt->execute(array());
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	$utype_name=$check_data[0][utype_name];
	if ($utype==0) {
		$utype_name = 'Все категории';
	}
$utypes_arr = get_childs ($utype, $DB_con);
$utypes_arr = parse_childs($utypes_arr);
if ($utype>0) {
		$utypes_arr[]=$utype;
	}
	
	//print ("SELECT unit_code, unit_name, unit_type  FROM units WHERE unit_type IN (".implode(',',$utypes_arr).") GROUP BY unit_code ORDER BY unit_code");
	$stmt = $DB_con->prepare("SELECT unit_code, unit_name, unit_type  FROM units WHERE unit_type IN (".implode(',',$utypes_arr).") GROUP BY unit_code ORDER BY unit_code");
	$stmt->execute(array());
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	for ($i=0; $i<count($check_data); $i++) {
		$units_array[$i]=$check_data[$i][unit_code];
	}
	

$s_date_arr=explode('-', $_REQUEST[start_time]);
$s_date="$s_date_arr[1]/$s_date_arr[2]/$s_date_arr[0] 00:00:00";
//echo "<br>".$s_date."<br>";
$s_date=strtotime($s_date);
//echo $s_date." strtotime <br>";
$s_date=$s_date+25200;

$s_date_normal="$s_date_arr[0]-$s_date_arr[1]-$s_date_arr[2] 00:00:00";

$e_date_arr=explode('-', $_REQUEST[end_time]);
$e_date="$e_date_arr[1]/$e_date_arr[2]/$e_date_arr[0] 23:59:59";
//echo "<br>".$e_date."<br>";
$e_date=strtotime($e_date);
//echo $e_date." strtotime <br>";
$e_date=$e_date+25200;
$e_date_normal="$e_date_arr[0]-$e_date_arr[1]-$e_date_arr[2] 23:59:59";

$shop_id = $_REQUEST[shop_list];
$shops_array=array();
if ($shop_id==999) {
	$stmt = $DB_con->prepare("SELECT shop_id FROM shops");
	$stmt->execute(array());
	$check1_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	for ($i=0; $i<count($check1_data); $i++) {
		$shops_array[$i]=$check1_data[$i][shop_id];
	}
}
else {
	$shops_array=explode(',', $shop_id);
	}

$date_range=$e_date-$s_date;
//echo $date_range;

	
	// print ("
			
			// SELECT 
			// sale_money_count-(sale_money_count*(sale_discount/100)) AS total, sale_discount,
			// HOUR(main.sale_date) AS sale_hour,
			// DAY(main.sale_date) AS sale_day,
			// MONTH(main.sale_date) AS sale_month,
			// YEAR(main.sale_date) AS sale_year
			// FROM
			// sales as main
			// WHERE 
			// main.sale_date>='$s_date_normal'
			// AND main.sale_date<='$e_date_normal'
			// AND main.sale_cashbox IN (".implode(',',$shops_array).")
			// AND sale_code IN (".implode(',',$units_array).")
			
		// ");
	
	
	
	
/////1	
	$stmt = $DB_con->prepare("
			SELECT AVG(total) AS total, sale_hour FROM (
			
			SELECT 
			sum(sale_money_count*(1-(sale_discount/100))) AS total,


			HOUR(main.sale_date) AS sale_hour
			FROM
			sales as main
			WHERE 
			main.sale_date>=:s_date
			AND main.sale_date<=:e_date
			AND main.sale_cashbox IN (".implode(',',$shops_array).")
			AND sale_code IN (".implode(',',$units_array).")
			GROUP BY HOUR(main.sale_date), DAY(main.sale_date), MONTH(main.sale_date), YEAR(main.sale_date) ORDER BY null
			
			) AS main0
			GROUP BY main0.sale_hour
		");
	
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
////2	
	$stmt = $DB_con->prepare("
			SELECT AVG(discount10) AS discount10, sale_hour FROM (
			
			SELECT 
			sum(sale_money_count*(1-(sale_discount/100))) AS discount10,
			HOUR(sale_date) AS sale_hour
			FROM
			sales
			WHERE 
			sale_date>=:s_date
			AND sale_date<=:e_date
			AND sale_cashbox IN (".implode(',',$shops_array).")
			AND sale_code IN (".implode(',',$units_array).")
			AND sale_discount=10
			GROUP BY HOUR(sale_date), DAY(sale_date), MONTH(sale_date), YEAR(sale_date) ORDER BY null
			
			) AS main
			GROUP BY main.sale_hour
		");
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data1=$stmt->fetchAll(PDO::FETCH_ASSOC);
////3	
	$stmt = $DB_con->prepare("
			SELECT AVG(discount30) AS discount30, sale_hour FROM (
			
			SELECT 
			sum(sale_money_count*(1-(sale_discount/100))) AS discount30,
			HOUR(sale_date) AS sale_hour
			FROM
			sales
			WHERE 
			sale_date>=:s_date
			AND sale_date<=:e_date
			AND sale_cashbox IN (".implode(',',$shops_array).")
			AND sale_code IN (".implode(',',$units_array).")
			AND sale_discount=30
			GROUP BY HOUR(sale_date), DAY(sale_date), MONTH(sale_date), YEAR(sale_date) ORDER BY null
			
			) AS main
			GROUP BY main.sale_hour
		");
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data2=$stmt->fetchAll(PDO::FETCH_ASSOC);
///////////END OF QUERIES
for ($i=0; $i<count($check_data); $i++) {
	$sale_hour = $check_data[$i][sale_hour];
	for ($c=0; $c<count($check_data1); $c++) {
		$sale_hour1 = $check_data1[$c][sale_hour];
		if ($sale_hour==$sale_hour1) {
			$check_data[$i]['discount10']=$check_data1[$c]['discount10'];
		}
	}
	
	for ($c=0; $c<count($check_data2); $c++) {
		$sale_hour2 = $check_data2[$c][sale_hour];
		if ($sale_hour==$sale_hour2) {
			$check_data[$i]['discount30']=$check_data2[$c]['discount30'];
		}
	}
}


	
if (count($check_data)<1) {
	die ("<script>alert('Нет данных за выбранный промежуток времени');</script>");
}
$chart_money_total=0;
$chart_discount_total=0;
for ($i=0; $i<count($check_data); $i++) {
	$chart_money = (int)$check_data[$i][total];
	$chart_money_total = $chart_money_total + $chart_money;
	$chart_discount = (int)$check_data[$i][discount_total];
	$chart_discount_total = $chart_discount_total+$chart_discount;
}
$chart_discount_total=formatMoney($chart_discount_total, true);
$chart_money_total=formatMoney($chart_money_total, true);
$params="
		['День', 'Объем продаж ($utype_name)'],
		  ";
$params1="
		['День', 'Продажи со скидкой 10% ($utype_name)', 'Продажи со скидкой 30% ($utype_name)'],
		  ";
for ($i=0; $i<count($check_data); $i++) {
	
	if ($check_data[$i]["sale_hour"]<10) {
		$check_data[$i]["sale_hour"]="0".$check_data[$i]["sale_hour"];
	}
	
	$chart_money = (int)$check_data[$i][total];
	$chart_discount10 = (int)$check_data[$i][discount10];
	$chart_discount30 = (int)$check_data[$i][discount30];
	$chart_date=$check_data[$i]["sale_hour"];
	if ($i==(count($check_data)-1)) {
		$params.="['$chart_date', $chart_money]";
		$params1.="['$chart_date', $chart_discount10, $chart_discount30]";
	}
	else {
		$params.="['$chart_date', $chart_money],";
		$params1.="['$chart_date', $chart_discount10, $chart_discount30],";
	}
}


	//echo ($params1);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
//echo $execution_time.' sec';	
echo ("
		<script type='text/javascript'>
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawChart);
		google.charts.setOnLoadCallback(drawChart1);
		
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          $params
        ]);

        var options = {
		pointsVisible: 'true',
          title: 'Продажи (Средние значения по часам)',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
	  
	  function drawChart1() {
        var data = google.visualization.arrayToDataTable([
          $params1
        ]);

        var options = {
			pointsVisible: 'true',
          title: 'Продажи со скидкой (Средние значения по часам)',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('curve_chart1'));

        chart.draw(data, options);
      }
	  $('html, body').animate({
			scrollTop: $(\"#end\").offset().top
		}, 500);
	  
	  </script>
	  <div id='curve_chart' style='width: 100%; height: 60%'></div>
	  <div id='curve_chart1' style='width: 100%; height: 60%'></div>
	");
?>