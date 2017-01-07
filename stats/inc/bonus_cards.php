<?php
header('Content-Type: text/html; charset=cp1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
require_once('db.php');
require_once('functions.php');
//print_r($_REQUEST);
echo("<br>");
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
$utypes_arr[]=$utype;

	$stmt = $DB_con->prepare("SELECT unit_code, unit_name, unit_type  FROM units WHERE unit_type IN (".implode(',',$utypes_arr).") GROUP BY unit_code");
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
	


$year=date("Y");
$age18 = $year-18;
$age25 = $year-25;
$age35 = $year-35;
$age45 = $year-45;
$age60 = $year-60;
$ageMax= $year-120;
$date_range=$e_date-$s_date;	
if ($date_range>86398) {
	// print ("
				// SELECT sum(sales.sale_money_count) AS total, cards.card_owner_social_status  FROM sales, cards WHERE sale_date>='$s_date_normal' AND sale_date<='$e_date_normal' AND sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND sales.sale_client_card_id=cards.card_number
		// ");
	
	$stmt = $DB_con->prepare("SELECT sum(sales.sale_money_count) AS total, cards.card_owner_social_status  FROM sales, cards WHERE sale_date>=:s_date AND sale_date<=:e_date AND sale_cashbox IN (".implode(',',$shops_array).") AND sale_code IN (".implode(',',$units_array).") AND sales.sale_client_card_id=cards.card_number GROUP BY cards.card_owner_social_status");
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$check_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	
	
	
	
	
	$stmt = $DB_con->prepare("
	
						SELECT
							(
							  SELECT
								SUM(sales_sub1.sale_money_count)
							  FROM
								sales as sales_sub1,
								cards as cards_sub1
							  WHERE
								sales_sub1.sale_date >= :s_date AND sales_sub1.sale_date <= :e_date AND sales_sub1.sale_client_card_id = cards_sub1.card_number 
								 AND
								  sales_sub1.sale_cashbox IN (".implode(',',$shops_array).") AND sales_sub1.sale_code IN (".implode(',',$units_array).")
								AND cards_sub1.card_number IN(
								SELECT
								  cards_sub2.card_number
								FROM
								  cards as cards_sub2
								WHERE
								  YEAR(cards_sub2.card_owner_birth_date) >= '$age25' AND YEAR(cards_sub2.card_owner_birth_date) <= '$age18'
							  )
							) AS age1825,

							(
							  SELECT
								SUM(sales_sub3.sale_money_count)
							  FROM
								sales as sales_sub3,
								cards as cards_sub3
							  WHERE
								sales_sub3.sale_date >= :s_date AND sales_sub3.sale_date <= :e_date AND sales_sub3.sale_client_card_id = cards_sub3.card_number 
								AND
								  sales_sub3.sale_cashbox IN (".implode(',',$shops_array).") AND sales_sub3.sale_code IN (".implode(',',$units_array).")
								AND cards_sub3.card_number IN(
								SELECT
								  cards_sub4.card_number
								FROM
								  cards as cards_sub4
								WHERE
								  YEAR(cards_sub4.card_owner_birth_date) >= '$age35' AND YEAR(cards_sub4.card_owner_birth_date) <= '$age25'
							  )
							) AS age2535,


							(
							  SELECT
								SUM(sales_sub5.sale_money_count)
							  FROM
								sales as sales_sub5,
								cards as cards_sub5
							  WHERE
								sales_sub5.sale_date >= :s_date AND sales_sub5.sale_date <= :e_date AND sales_sub5.sale_client_card_id = cards_sub5.card_number 
								AND
								  sales_sub5.sale_cashbox IN (".implode(',',$shops_array).") AND sales_sub5.sale_code IN (".implode(',',$units_array).")
								AND cards_sub5.card_number IN(
								SELECT
								  cards_sub6.card_number
								FROM
								  cards as cards_sub6
								WHERE
								  YEAR(cards_sub6.card_owner_birth_date) >= '$age45' AND YEAR(cards_sub6.card_owner_birth_date) <= '$age35'
							  )
							) AS age3545,


							(
							  SELECT
								SUM(sales_sub7.sale_money_count)
							  FROM
								sales as sales_sub7,
								cards as cards_sub7
							  WHERE
								sales_sub7.sale_date >= :s_date AND sales_sub7.sale_date <= :e_date AND sales_sub7.sale_client_card_id = cards_sub7.card_number 
								AND
								  sales_sub7.sale_cashbox IN (".implode(',',$shops_array).") AND sales_sub7.sale_code IN (".implode(',',$units_array).")
								AND cards_sub7.card_number IN(
								SELECT
								  cards_sub8.card_number
								FROM
								  cards as cards_sub8
								WHERE
								  YEAR(cards_sub8.card_owner_birth_date) >= '$age60' AND YEAR(cards_sub8.card_owner_birth_date) <= '$age45'
							  )
							) AS age4560,


							(
							  SELECT
								SUM(sales_sub9.sale_money_count)
							  FROM
								sales as sales_sub9,
								cards as cards_sub9
							  WHERE
								sales_sub9.sale_date >= :s_date AND sales_sub9.sale_date <= :e_date AND sales_sub9.sale_client_card_id = cards_sub9.card_number 
								AND
								  sales_sub9.sale_cashbox IN (".implode(',',$shops_array).") AND sales_sub9.sale_code IN (".implode(',',$units_array).")
								AND cards_sub9.card_number IN(
								SELECT
								  cards_sub10.card_number
								FROM
								  cards as cards_sub10
								WHERE
								  YEAR(cards_sub10.card_owner_birth_date) >= '$ageMax' AND YEAR(cards_sub10.card_owner_birth_date) <= '$age60'
							  )
							) AS age60	
	
	
				");
	$stmt->execute(array
				(	
					':s_date' => $s_date_normal,
					':e_date' => $e_date_normal
				)
			);
	$age_data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	
	
	
}

if (count($age_data)<1) {
	die ("<script>alert('Нет данных за выбранный промежуток времени');</script>");
}
$chart_info="['Социальная группа', 'Объем покупок в рублях'],";
for ($i=0; $i<count($check_data); $i++) {
	if ($check_data[$i][card_owner_social_status]=='0') {
		$s_status = "Не указано";
	}
	if ($check_data[$i][card_owner_social_status]=='1') {
		$s_status = "Пенсионер";
	}
	if ($check_data[$i][card_owner_social_status]=='2') {
		$s_status = "Работающий";
	}
	if ($check_data[$i][card_owner_social_status]=='3') {
		$s_status = "Студент";
	}
	if ($check_data[$i][card_owner_social_status]=='4') {
		$s_status = "Предприниматель";
	}
	if ($check_data[$i][card_owner_social_status]=='5') {
		$s_status = "Домохозяйка";
	}
	$money = $check_data[$i][total];
	$money_total = $money_total + $money;
	if ($i==(count($check_data)-1)) {
		$chart_info.="['$s_status', $money]";
	}
	else {
		$chart_info.="['$s_status', $money],";
	}
	
}

$money_total = formatMoney($money_total, true);

$age1=$age_data[0]['age1825'];
$age2=$age_data[0]['age2535'];
$age3=$age_data[0]['age3545'];
$age4=$age_data[0]['age4560'];
$age5=$age_data[0]['age60'];


$chart_info1="['Возрастная группа', 'Объем покупок в рублях'],";
$chart_info1.="['18-25 лет', $age1],";
$chart_info1.="['25-35 лет', $age2],";
$chart_info1.="['35-45 лет', $age3],";
$chart_info1.="['45-60 лет', $age4],";
$chart_info1.="['60+ лет', $age5]";
//echo $chart_info;
//echo "<br>";
//echo $chart_info1;
echo ("
		<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
		<script type='text/javascript'>
		  google.charts.load('current', {'packages':['corechart']});
		  google.charts.setOnLoadCallback(drawChart);
		  google.charts.setOnLoadCallback(drawChart1);
		  function drawChart() {

			var data = google.visualization.arrayToDataTable([
			  $chart_info
			]);

			var options = {
			  title: 'Социальные группы (Итого по картам: $money_total р.)'
			};

			var chart = new google.visualization.PieChart(document.getElementById('piechart'));

			chart.draw(data, options);
		  }
		  
		  function drawChart1() {

			var data = google.visualization.arrayToDataTable([
			  $chart_info1
			]);

			var options = {
			  title: 'Возрастные группы'
			};

			var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

			chart.draw(data, options);
		  }
		  
		  
		</script>
	<div id='piechart' style='width: 40%; height: 50%; float: left;'></div>
	<div id='piechart1' style='width: 40%; height: 50%; float: left;'></div>
	");
?>