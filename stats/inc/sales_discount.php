<?php
//TODO
header('Content-Type: text/html; charset=cp1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
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
if ($date_range==86399) {
	$stmt = $DB_con->prepare("			
			SELECT 
			sum(sale_money_count-(sale_money_count*(sale_discount/100))) AS total,

			(SELECT sum(sub1.sale_money_count-(sub1.sale_money_count*(sub1.sale_discount/100))) FROM sales AS sub1 WHERE sub1.sale_discount <> 0 AND sub1.sale_date>=:s_date AND sub1.sale_date<=:e_date AND sub1.sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND (HOUR(main.sale_date) = HOUR(sub1.sale_date)) ORDER BY sub1.sale_date) as discount_total,

			(SELECT sum(sub3.sale_money_count-(sub3.sale_money_count*(sub3.sale_discount/100))) FROM sales AS sub3 WHERE sub3.sale_discount=30 AND sub3.sale_date>=:s_date AND sub3.sale_date<=:e_date AND sub3.sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND (HOUR(main.sale_date) = HOUR(sub3.sale_date))  ORDER BY sub3.sale_date) as discount30,

			(SELECT sum(sub2.sale_money_count-(sub2.sale_money_count*(sub2.sale_discount/100))) FROM sales AS sub2 WHERE sale_discount=10 AND sale_date>=:s_date AND sale_date<=:e_date AND sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND  (HOUR(main.sale_date) = HOUR(sub2.sale_date))  ORDER BY sub2.sale_date) as discount10,
			
			HOUR(main.sale_date) AS sale_day
			FROM
			sales as main
			WHERE
			main.sale_date>=:s_date
			AND main.sale_date<=:e_date
			AND main.sale_cashbox IN (".implode(',',$shops_array).")
			AND sale_code IN (".implode(',',$units_array).")
			GROUP BY HOUR(main.sale_date) ORDER BY main.sale_date
		");
	
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	//print_r($check_data);
}
if ($date_range>86400) {
	//print ("SELECT sum(sale_money_count-(sale_money_count*(sale_discount/100))) AS total, sum(sale_money_count*(sale_discount/100)) AS discount_total, DAY(sale_date), MONTH(sale_date) FROM sales WHERE sale_date>=$s_date_normal AND sale_date<=$e_date_normal AND sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") GROUP BY DAY(sale_date), MONTH(sale_date) ORDER BY sale_date");
	
	$stmt = $DB_con->prepare("
			
			SELECT 
			sum(sale_money_count-(sale_money_count*(sale_discount/100))) AS total,

			(SELECT sum(sub1.sale_money_count-(sub1.sale_money_count*(sub1.sale_discount/100))) FROM sales AS sub1 WHERE sub1.sale_discount <> 0 AND sub1.sale_date>=:s_date AND sub1.sale_date<=:e_date AND sub1.sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND (DAY(main.sale_date) = DAY(sub1.sale_date)) AND (MONTH(main.sale_date) = MONTH(sub1.sale_date)) AND (YEAR(main.sale_date) = YEAR(sub1.sale_date)) ORDER BY sub1.sale_date) as discount_total,

			(SELECT sum(sub3.sale_money_count-(sub3.sale_money_count*(sub3.sale_discount/100))) FROM sales AS sub3 WHERE sub3.sale_discount=30 AND sub3.sale_date>=:s_date AND sub3.sale_date<=:e_date AND sub3.sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND (DAY(main.sale_date) = DAY(sub3.sale_date)) AND (MONTH(main.sale_date) = MONTH(sub3.sale_date)) AND (YEAR(main.sale_date) = YEAR(sub3.sale_date)) ORDER BY sub3.sale_date) as discount30,

			(SELECT sum(sub2.sale_money_count-(sub2.sale_money_count*(sub2.sale_discount/100))) FROM sales AS sub2 WHERE sale_discount=10 AND sale_date>=:s_date AND sale_date<=:e_date AND sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND  (DAY(main.sale_date) = DAY(sub2.sale_date)) AND (MONTH(main.sale_date) = MONTH(sub2.sale_date)) AND (YEAR(main.sale_date) = YEAR(sub2.sale_date)) ORDER BY sub2.sale_date) as discount10,



			MONTH(main.sale_date) AS sale_month,
			DAY(main.sale_date) AS sale_day,
			YEAR(main.sale_date) AS sale_YEAR,
			DAYNAME(main.sale_date) AS sale_dayname
			FROM
			sales as main
			WHERE 
			main.sale_date>=:s_date
			AND main.sale_date<=:e_date
			AND main.sale_cashbox IN (".implode(',',$shops_array).")
			AND sale_code IN (".implode(',',$units_array).")
			GROUP BY DAY(main.sale_date), MONTH(main.sale_date), YEAR(main.sale_date) ORDER BY main.sale_date
			
		");
	
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	// print ("<pre>");
	// print_r($check_data);
	// print("</pre>");
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
	if ($check_data[$i]["sale_dayname"]=='Monday') {
		$weekday = '(пн)';
	}
	if ($check_data[$i]["sale_dayname"]=='Tuesday') {
		$weekday = '(вт)';
	}
	if ($check_data[$i]["sale_dayname"]=='Wednesday') {
		$weekday = '(ср)';
	}
	if ($check_data[$i]["sale_dayname"]=='Thursday') {
		$weekday = '(чт)';
	}
	if ($check_data[$i]["sale_dayname"]=='Friday') {
		$weekday = '(птн)';
	}
	if ($check_data[$i]["sale_dayname"]=='Saturday') {
		$weekday = '(сб)';
	}
	if ($check_data[$i]["sale_dayname"]=='Sunday') {
		$weekday = '(вс)';
	}
	if ($check_data[$i]["sale_dayname"]=='') {
		$weekday = '';
	}
	
	if ($check_data[$i]["sale_day"]<10) {
		$check_data[$i]["sale_day"]="0".$check_data[$i]["sale_day"];
	}
	if ($check_data[$i]["sale_month"]=='') {
			$chart_date = $check_data[$i]["sale_day"];
		}
	else {
		$chart_date = $check_data[$i]["sale_day"].".".$check_data[$i]["sale_month"];
	}
	$chart_money = (int)$check_data[$i][total];
	$chart_discount10 = (int)$check_data[$i][discount10];
	$chart_discount30 = (int)$check_data[$i][discount30];
	if ($i==(count($check_data)-1)) {
		$params.="['$chart_date $weekday', $chart_money]";
		$params1.="['$chart_date $weekday', $chart_discount10, $chart_discount30]";
	}
	else {
		$params.="['$chart_date $weekday', $chart_money],";
		$params1.="['$chart_date $weekday', $chart_discount10, $chart_discount30],";
	}
}


	//echo ($params);
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
          title: 'Продажи (Итого за период: $chart_money_total р.)',
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
          title: 'Продажи со скидкой (Итого за период: $chart_discount_total р.)',
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