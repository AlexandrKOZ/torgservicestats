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
<div class="col-xs-2">
	<label for="datepicker">Дата</label>
	<input class="form-control" id="datepicker" type="text">
</div>
<br><br><br><br>
<button id='show_files_button' onclick="return false;" class="btn btn-success" type="submit">Показать</button>
<div id=data>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/frontend.js"></script>
<script src="js/jquery.bonsai.js"></script>
<script src="js/jquery.qubit.js"></script>
</body>
</html>