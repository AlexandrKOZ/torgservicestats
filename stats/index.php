<?php
header('Content-Type: text/html; charset=win1251');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
require_once('inc/db.php');
require_once('inc/functions.php');
?>
<html lang="en">
  <head>
    <meta charset="windows-1251">
	<meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Статистика</title> 
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
    <link href="css/jumbotron.css" rel="stylesheet">
	<link href="css/admin.css" rel="stylesheet">
	<link href="css/jquery.bonsai.css" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]--> 
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
<body>
<div id=stats_types>
	<button data-stats-type="discount" data-pushed="0" onclick="return false;" class="btn btn-info stats_params" type="submit">Продажи</button>
	<button data-stats-type="sales_hourly" data-pushed="0" onclick="return false;" class="btn btn-info stats_params" type="submit">Продажи по часам</button>
	<button data-stats-type="bonus_cards" data-pushed="0" onclick="return false;" class="btn btn-info stats_params" type="submit">Бонусные карты</button>
	<a href=files.php target='_blank'>
	<button data-stats-type="discount6" data-pushed="0" onclick="" class="btn btn-info stats_params" type="submit">Файлы</button></a>
</div>

<div id='shops'>
			<?php
			get_shops_list($DB_con);
			?>
</div>
<div id=dates>
	<div class="col-xs-2">
	  <label for="datepicker">Дата начала</label>
	  <input class="form-control" id="datepicker000" type="date" value="<?php echo date("Y-m-d");?>">
	</div>
	
	<div class="col-xs-2">
	  <label for="datepicker1">Дата конца</label>
	  <input class="form-control" id="datepicker111" type="date" value="<?php echo date("Y-m-d");?>">
	</div>
</div>
<br><br><br><br>
<div id='show_chart'>
<button id='show_chart_button' onclick="return false;" class="btn btn-success" type="submit">Показать график</button>
</div>
<div id=items>

<form id=test name=test>
	<ol id='auto-checkboxes' data-name='utype'>
		<li class='collapsed' data-value='0'>Все категории
<?php
units_tree($DB_con);
?>
		</li>
	</ol>
</form>	
</div>
<br><br><br>

<div id=message_div></div>
<div id=log_div></div>
<div id=chart>
</div>
<div id=end>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/frontend.js"></script>
<script src="js/jquery.bonsai.js"></script>
<script src="js/jquery.qubit.js"></script>
</body>
</html>