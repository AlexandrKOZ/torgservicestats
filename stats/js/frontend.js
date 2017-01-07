jQuery(document).ready(function($){
	var shops = []; 
	
	
	
	
	$(function(){
		$("#datepicker").datepicker();
		$("#datepicker").datepicker("option", "dateFormat", "dd/mm/yy");
	});
	
	$(function(){
		$("#datepicker1").datepicker();
		$("#datepicker1").datepicker("option", "dateFormat", "dd/mm/yy");
	});
	
	
	
	$('.stats_params').click(function(){
		var pushed = $(this).attr('data-pushed');
		if (pushed == 0) {
			$('.stats_params').removeClass('btn-danger');
			$('.stats_params').addClass('btn-info');
			$(this).removeClass('btn-info');
			$(this).addClass('btn-danger');
			window.chart_type = $(this).attr('data-stats-type');
		}		
	})

	$('#auto-checkboxes').bonsai({
	  expandAll: false,
	  createInputs: 'radio' // takes values from data-name and data-value, and data-name is inherited
	});
	
	$('.shops_select').click(function(){
		var pushed = $(this).attr('pushed');
		var shop_id = $(this).attr('shop-id')
		if (pushed == 0) {
			if (shop_id==999) {
				shops = [];
				$('.shops_select').removeClass('btn-danger');
				$('.shops_select').addClass('btn-warning');
				$('.shops_select').attr('pushed', '0');
			}
			else {
				$('.all_shops').attr('pushed', '0');
				$('.all_shops').removeClass('btn-danger');
				$('.all_shops').addClass('btn-warning');
				var index = shops.indexOf('999');
				if (index > -1) {
					shops.splice(index, 1);
				}
			}
			$(this).removeClass('btn-warning');
			$(this).addClass('btn-danger');
			$(this).attr('pushed', '1');
			shops.push(shop_id);
		}
		else {
			$(this).removeClass('btn-danger');
			$(this).addClass('btn-warning');
			$(this).attr('pushed', '0');
			var index = shops.indexOf(shop_id);
			if (index > -1) {
				shops.splice(index, 1);
			}
		}
		var e = shops.toString();	
		window.shop_list = e;		
	})
	
	
$('#show_files_button').click(function()	{
		var date = $('#datepicker').val();
		var action = 'get_files';
		$("#data").load("inc/aj.php", {action:action, date:date});
})	
	

$('#show_chart_button').click(function()	{
		event.preventDefault();		
		$('#message_div').html('<img src=img/loading.gif>');
		var utype = $('#test').serialize();
		var utype = decodeURI(utype);
		var start_time = $('#datepicker000').val();
		var end_time = $('#datepicker111').val();
		var chart_type = window.chart_type;
		var shop_list = window.shop_list;
		var file = 'inc/sales_discount.php';
		if (window.chart_type=='bonus_cards') {
			var file='inc/bonus_cards.php';
		}
		if (window.chart_type=='discount') {
			var file='inc/sales_discount.php';
		}
		if (window.chart_type=='sales_hourly') {
			var file='inc/sales_hourly.php';
		}
		//alert (file);
		var action = 'get_chart';
		$("#message_div").load(file, {action:action, chart_type:chart_type, shop_list:shop_list, start_time:start_time, end_time:end_time, utype:utype});
		$("#log_div").load("inc/log.php", {chart_type:chart_type});
})
})
