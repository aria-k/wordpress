<?php
function get_rmag_global_unit_wallet(){
	if (!defined('RMAG_PREF')){
		global $wpdb;
		global $rmag_options;
		$rmag_options = unserialize(get_option('primary-rmag-options'));
		define('RMAG_PREF', $wpdb->prefix."rmag_");
	}
}
add_action('init','get_rmag_global_unit_wallet',10);

if (is_admin()):
	add_action('admin_head','output_script_admin_acc_recall');	
endif;

function output_script_admin_acc_recall(){
	wp_enqueue_script( 'jquery' );		
	wp_enqueue_script( 'ajax_admin_account_recall', plugins_url('js/admin.js', __FILE__) );		
}

function statistic_user_pay_page_rcl(){
	$prim = 'manage-rmag';
	if(!function_exists('wpmagazin_options_panel')){
		$prim = 'manage-wpm-options';
		add_menu_page('Recall Commerce', 'Recall Commerce', 'manage_options', $prim, 'global_recall_wpm_options');
		add_submenu_page( $prim, 'Платежные системы', 'Платежные системы', 'manage_options', $prim, 'global_recall_wpm_options');
	}
	
	add_submenu_page( $prim, 'Платежи', 'Платежи', 'manage_options', 'manage-wpm-cashe', 'statistic_add_cashe_wpm_recall');	
}
add_action('admin_menu', 'statistic_user_pay_page_rcl',25);

add_filter('admin_options_rmag','user_account_wpm_options',10);
function user_account_wpm_options($content){
	$style = 'style="display:block;"';
	$options = unserialize(get_option('primary-rmag-options'));
	$content .= '<h2>Платежные системы</h2>
	<div id="options-'.get_key_addon_rcl(pathinfo(__FILE__)).'" '.$style.' class="wrap-recall-options">		
		<div class="option-block">
			<h3>Подключение к платежному агрегатору</h3>
			<label>Используемый тип подключение</label>
			<select name="connect_sale" size="1">';
				$connect_sale = $options['connect_sale'];
				$content .= '<option value="">Не используется</option>	
				<option value="1" '.selected($connect_sale,1,false).'>Robokassa</option>
				<option value="2" '.selected($connect_sale,2,false).'>Интеркасса</option>
			</select>			
		</div>
		
		<div class="option-block">
			<h3>Настройки подключения Робокасса</h3>
			<label>Идентификатор магазина</label>
			<input type="text" name="robologin" value="'.$options['robologin'].'" size="60">
			<label>1 Пароль</label>
			<input type="password" name="onerobopass" value="'.$options['onerobopass'].'" size="60">
			<label>2 Пароль</label>
			<input type="password" name="tworobopass" value="'.$options['tworobopass'].'" size="60">
			<label>Статус аккаунта Робокассы</label>
			<select name="robotest" size="1">';
				$robotest = $options['robotest'];
				$content .= '<option value="1" '.selected($robotest,1,false).'>Тестовый</option>
				<option value="0" '.selected($robotest,0,false).'>Рабочий</option>
			</select>
		</div>
		
		<div class="option-block">
			<h3>Настройки подключения Интеркасса</h3>
			<label>Secret Key</label>
			<input type="password" name="intersecretkey" value="'.$options['intersecretkey'].'" size="60">
			<label>Test Key</label>
			<input type="password" name="intertestkey" value="'.$options['intertestkey'].'" size="60">
			<label>Идентификатор магазина</label>
			<input type="text" name="interidshop" value="'.$options['interidshop'].'" size="60">
			<label>Статус аккаунта Интеркассы</label>
			<select name="interkassatest" size="1">';
				$interkassatest = $options['interkassatest'];
				$content .= '<option value="1" '.selected($interkassatest,1,false).'>Тестовый</option>
				<option value="0" '.selected($interkassatest,0,false).'>Рабочий</option>
			</select>
		</div>
		
		<div class="option-block">
			<h3>Оплата заказа</h3>
			<label>Тип оплаты</label>
			<select name="type_order_payment" size="1">';
				$type_order = $options['type_order_payment'];
				$content .= '<option value="">Средствами с личного счета пользователя</option>	
				<option value="1" '.selected($type_order,1,false).'>Напрямую через платежную систему</option>
				<option value="2" '.selected($type_order,2,false).'>Предложить оба варианта</option>
			</select>
			<small>Если подключение к платежному агрегатору не используется, то выставлять только "Средствами с личного счета пользователя"!</small>
		</div>
		
		<div class="option-block">
			<h3>Сервисные страницы платежных систем</h3>
			
			<p>1. Создайте на своем сайте четыре страницы:</p>
			- пустую для success<br>
			- пустую для result<br>
			- одну с текстом о неудачной оплате (fail)<br>
			- одну с текстом об удачной оплате<br>
			Название и URL созданных страниц могут быть произвольными.<br>
			<p>2. Укажите здесь какие страницы и для чего вы создали. </p>
			<p>3. В настройках своего аккаунта платежной системы укажите URL страницы для fail, success и result</p>
			
			<label>Страница RESULT</label>';
			$args = array(    
				'selected'   => $options['page_result_pay'],   
				'name'       => 'page_result_pay',
				'show_option_none' => '<span style="color:red">Не выбрано</span>',
				'echo'             => 0  
			);  
			$content .= wp_dropdown_pages( $args );
			$content .= '<small>Для Интеркассы: URL взаимодействия</small>';
			
			$content .= '<label>Страница SUCCESS</label>';
			$args = array(    
				'selected'   => $options['page_success_pay'],   
				'name'       => 'page_success_pay',
				'show_option_none' => '<span style="color:red">Не выбрано</span>',
				'echo'             => 0  
			);  
			$content .= wp_dropdown_pages( $args );
			$content .= '<small>Для Интеркассы: URL успешной оплаты</small>';
			
			$content .= '<label>Страница удачной оплаты</label>';
			$args = array(    
				'selected'   => $options['page_successfully_pay'],   
				'name'       => 'page_successfully_pay',
				'show_option_none' => '<span style="color:red">Не выбрано</span>',
				'echo'             => 0  
			);  
			$content .= wp_dropdown_pages( $args );
			
			$content .= '</div>
	</div>';
	return $content;
}

// создаем допколонку для вывода баланса пользователя
function balance_user_recall_admin_column( $columns ){
 
  return array_merge( $columns,
    array( 'balance_user_recall' => "Баланс" )
  );
 
}
add_filter( 'manage_users_columns', 'balance_user_recall_admin_column' );

function balance_user_recall_content( $custom_column, $column_name, $user_id ){
global $wpdb;

  switch( $column_name ){
    case 'balance_user_recall':
      $user_count = $wpdb->get_var("SELECT count FROM ".RMAG_PREF ."user_count WHERE user = '$user_id'");
	  $balance = '<span class="balance-'.$user_id.'">'.$user_count.'</span><br /><input type="text" class="balanceuser-'.$user_id.'" name="balanceuser-'.$user_id.'" size="4" value=""><input type="button" class="recall-button edit_balance" id="user-'.$user_id.'" value="Ок">' ;
      break;
  }
  return $balance;
 
}
add_filter( 'manage_users_custom_column', 'balance_user_recall_content', 10, 3 );

function statistic_add_cashe_wpm_recall(){
global $wpdb;
	if($_POST['action']=='trash'){
		$cnt = count($_POST['addcashe']);
		for($a=0;$a<$cnt;$a++){
			$id = $_POST['addcashe'][$a];
			if($id) $wpdb->query("DELETE FROM ".RMAG_PREF ."pay_results WHERE ID = '$id'");
		}
	}

	if($_GET['paged']) $page = $_GET['paged'];
	else $page=1;
	
	$inpage = 30;
	$start = ($page-1)*$inpage;
	
	if($_POST['filter-date']){
	
		if($_POST['year']){
			$like = $_POST['year'];
			if($_POST['month']) $like .= '-'.$_POST['month'];
			$like .= '%';
			$get = 'WHERE time_action  LIKE "'.$like.'"';
		}
				
		$get .= ' ORDER BY ID DESC';
		$statistic = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."pay_results ".$get);
		$count_adds = count($statistic);
		
		$all_pr=0;	
		foreach($statistic as $st){
			$all_pr += $st->count;
		}	
		$all_pr = ' на сумму '.$all_pr.' рублей';
		
	}else{	
		if($_GET['user']){
			$get = $_GET['user'];
			$get_data = '&user='.$get;
			$statistic = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."pay_results WHERE user = '$get' ORDER BY ID DESC LIMIT $start,$inpage");
			$count_adds = $wpdb->get_var("SELECT COUNT(ID) FROM ".RMAG_PREF ."pay_results WHERE user = '$get'");
		}elseif($_GET['date']){
			$get = $_GET['date'];
			$get_data = '&date='.$get;
			$statistic = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."pay_results WHERE time_action LIKE '$get%' ORDER BY ID DESC LIMIT $start,$inpage");
			$count_adds = $wpdb->get_var("SELECT COUNT(ID) FROM ".RMAG_PREF ."pay_results WHERE time_action LIKE '$get%'");
		}else{
			$statistic = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."pay_results ORDER BY ID DESC LIMIT $start,$inpage");
			$count_adds = $wpdb->get_var("SELECT COUNT(ID) FROM ".RMAG_PREF ."pay_results");
		}
	
		$cnt = count($statistic);
	}

	$table = '
	<div class="wrap"><h2>Приход средств через платежные системы</h2>
	<h3>Всего переводов: '.$count_adds.$all_pr.'</h3>
	<form action="" method="post" class="alignright">';
	$table .= '<select name="month"><option value="">За все время</option>';
	for($a=1;$a<=12;$a++){
		switch($a){
			case 1: $month = 'январь'; $n = '01'; break;
			case 2: $month = 'февраль'; $n = '02'; break;
			case 3: $month = 'март'; $n = '03'; break;
			case 4: $month = 'апрель'; $n = '04'; break;
			case 5: $month = 'май'; $n = '05'; break;
			case 6: $month = 'июнь'; $n = '06'; break;
			case 7: $month = 'июль'; $n = '07'; break;
			case 8: $month = 'август'; $n = '08'; break;
			case 9: $month = 'сентябрь'; $n = '09'; break;
			case 10: $month = 'октябрь'; $n = $a; break;
			case 11: $month = 'ноябрь'; $n = $a; break;
			case 12: $month = 'декабрь'; $n = $a; break;
		}
		$table .= '<option value="'.$n.'" '.selected($n,$_POST['month'],false).'>'.$month.'</option>';
	}
	$table .= '</select>';
	$table .= '<select name="year">';
	for($a=2013;$a<=2015;$a++){
		$table .= '<option value="'.$a.'" '.selected($a,$_POST['year'],false).'>'.$a.'</option>';
	}
	$table .= '</select>';
	$table .= '<input type="submit" value="Фильтровать" name="filter-date" class="button-secondary">';
	$table .= '</form>';
	
	$table .= '<form action="" method="post">
	<div class="tablenav top">
		<div class="alignleft actions">
		<select name="action">
			<option selected="selected" value="-1">Действия</option>
			<option value="trash">Удалить</option>
		</select>
		<input id="doaction" class="button action" type="submit" value="Применить" name="">
		</div>	
	</div>
	<table class="widefat"><tr><th class="check-column" scope="row"></th><th class="manage-column">№пп</th><th class="manage-column">Пользователь</th><th class="manage-column">ID платежа</th><th class="manage-column">Сумма платежа</th><th class="manage-column">Дата и время</th></tr>';
	
	$n=0;
	foreach((array)$statistic as $add){
		$n++;
		$time = substr($add->time_action, -9);
		$date = substr($add->time_action, 0, 10);
		$table .= '<tr><th class="check-column" scope="row"><input id="delete-addcashe-'.$add->ID.'" type="checkbox" value="'.$add->ID.'" name="addcashe[]"></th><td>'.$n.'</td><td><a href="/wp-admin/admin.php?page=manage-wpm-cashe&user='.$add->user.'">'.get_the_author_meta('user_login',$add->user).'</a></td><td>'.$add->inv_id.'</td><td>'.$add->count.'</td><td><a href="/wp-admin/admin.php?page=manage-wpm-cashe&date='.$date.'">'.$date.'</a>'.$time.'</td></tr>';
	}
	
	$table .= '</table></form>';
	
	$table .= admin_navi_rcl($inpage,$count_adds,$page,'manage-wpm-cashe',$get_data);
	
	$table .= '</div>';

	echo $table;
}

/*************************************************
Пополнение личного счета пользователя
*************************************************/	
function add_count_user_recall(){
	global $user_ID;
	global $rmag_options;
	if($_POST['count']){
		if($rmag_options['connect_sale']==1){ //если используется робокасса
			$out_summ = $_POST['count'];
			$mrh_login = $rmag_options['robologin']; 
			$mrh_pass1 = $rmag_options['onerobopass']; 
			$inv_id = 0;
			$shpb = 1; //тип платежа. 1 - пополнение личного счета, 2 - оплата заказа
			$shp_item = "2"; 
			//$in_curr = "QiwiR"; 
			$culture = "ru"; 

			$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item:shpa=$user_ID:shpb=$shpb"); 

			if($rmag_options['robotest']==1) $formaction = 'http://test.robokassa.ru/Index.aspx';
			else $formaction = 'https://merchant.roboxchange.com/Index.aspx';

			$log['redirectform'] = "<form action='".$formaction."' method=POST>
			".get_inputs_pay_sistem_rcl($inv_id,$out_summ,$shpb,$crc)."
			<input type=submit class=recall-button value='Подтвердить запрос'></form>";
			$log['otvet']=100;
		}
		if($rmag_options['connect_sale']==2){ //если используется интеркасса
			$ik_am = $_POST['count'];
			$ik_co_id = $rmag_options['interidshop'];
			$ik_pm_no = rand(0,100000000);
			$ik_desc = 'Пополнение личного счета пользователя';
			$ik_x_user_id = $user_ID;
			$test = $rmag_options['interkassatest'];
			$key = $rmag_options['intersecretkey'];

			if($test==1){				
				$ik_pw_via = 'test_interkassa_test_xts';
				$data['ik_pw_via'] = $ik_pw_via;
				$test_input = "<input type='hidden' name='ik_pw_via' value='$ik_pw_via'>";				
			}
							
			$data['ik_am'] = $ik_am;
			$data['ik_co_id'] = $ik_co_id;
			$data['ik_pm_no'] = $ik_pm_no;
			$data['ik_desc'] = $ik_desc;
			$data['ik_x_user_id'] = $ik_x_user_id;		

			ksort ($data, SORT_STRING);
			array_push($data, $key);
			$signStr = implode(':', $data);			
			$ik_sign = base64_encode(md5($signStr, true));

			$log['redirectform'] = "<form action='https://sci.interkassa.com/' method='POST'>
				".$test_input."
				".get_inputs_pay_sistem_rcl($ik_pm_no,$ik_am,1,$ik_sign)."
				<input type='submit' value='Подтвердить запрос'>
			</form>";
			
			$log['otvet']=100;
		}
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);
	exit;
}
add_action('wp_ajax_add_count_user_recall', 'add_count_user_recall');

/*************************************************
Меняем баланс пользователя из админки
*************************************************/
function edit_balance_user_recall(){
	global $user_ID;
	global $wpdb;
	$user = $_POST['user'];
	$balance = $_POST['balance'];

	if($_POST['balance']!==''){
		
		$oldusercount = $wpdb->get_var("SELECT count FROM ".RMAG_PREF ."user_count WHERE user='$user'");
		
		$new_cnt = $balance - $oldusercount;
		if($new_cnt<0) $type = 1;
		else $type = 2;
		
			if(isset($oldusercount)){
				$wpdb->update(RMAG_PREF .'user_count', 
					array( 'count' => $balance ),
					array( 'user' => $user )
				);

			}else{
				$wpdb->insert( RMAG_PREF .'user_count', 
							array( 'user' => "$user", 'count' => "$balance" )
							);
			}
			$new_cnt = abs((int)$new_cnt);
			do_action('admin_edit_user_count_rcl',$user,$new_cnt,'Изменение баланса',$type);

	$log['otvet']=100;
	$log['user']=$user;
	$log['balance']=$balance;
	
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);	
    exit;
}
add_action('wp_ajax_edit_balance_user_recall', 'edit_balance_user_recall');

add_action( 'widgets_init', 'widget_user_count' );

function widget_user_count() {
	register_widget( 'Widget_user_count' );
}

class Widget_user_count extends WP_Widget {

	function Widget_user_count() {
		$widget_ops = array( 'classname' => 'widget-user-count', 'description' => 'Личный счёт пользователя' );		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'widget-user-count' );		
		$this->WP_Widget( 'widget-user-count', 'Личный счёт', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );

		

		// Display the widget title 
					
		global $wpdb;
		global $user_ID;
		global $rmag_options;
		if ($user_ID){
			echo $before_widget;
			$user_count = $wpdb->get_row("SELECT * FROM ".RMAG_PREF ."user_count WHERE user = '$user_ID'");
			if($user_count){

				if ( $title )
					echo $before_title . $title . $after_title;
			
				echo '<div class="usercount" style="text-align:center;">'.$user_count->count.' рублей</div>';
			} else {
				if ( $title )
					echo $before_title . $title . $after_title;
			
				echo '<div class="usercount" style="text-align:center;">0 рублей</div>';
				
			}
			
			echo apply_filters('count_widget_rcl',$content);
			
			if($rmag_options['connect_sale']!='') echo "<p align='right'><a class='go_to_add_count' href='#'>Пополнить</a></p>
			<div class='count_user'>
					<h3>Пополнить личный счет</h3>
					<div>
					<p style='margin-bottom: 10px;'><label>Введите требуемую сумму в рублях</label></p>
							<input class='value_count_user' size='4' type='text' value=''>
							<input class='add_count_user recall-button' type='button' value='Отправить'>
					</div>
					<div class='redirectform' style='margin:10px 0;text-align:center;'></div>
					</div>";
					echo $after_widget;
		}		
				
	}

	//Update the widget 	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array( 'title' => 'Личный счёт:');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Заголовок:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
	<?php
	}
} 

function get_inputs_pay_sistem_rcl($inv_id,$out_summ,$type_pay,$crc,$user_id=null){
	global $rmag_options,$user_ID;
	if(!$user_ID) $user_ID = $user_id;
	if($rmag_options['connect_sale']==1){
		$inputs = "
			<input type='hidden' name='MrchLogin' value='".$rmag_options['robologin']."'>
			<input type='hidden' name='OutSum' value='$out_summ'>
			<input type='hidden' name='InvId' value='$inv_id'>
			<input type='hidden' name='shpb' value='$type_pay'>
			<input type='hidden' name='shpa' value='$user_ID'>
			<input type='hidden' name='SignatureValue' value='$crc'>
			<input type='hidden' name='Shp_item' value='2'>			
			<input type='hidden' name='Culture' value='ru'>";
	}
	if($rmag_options['connect_sale']==2){
		if($type_pay==1) $desc = 'Пополнение личного счета';
		else $desc = 'Оплата заказа на сайте';
		
		$inputs = "
			<input type='hidden' name='ik_co_id' value='".$rmag_options['interidshop']."'>
			<input type='hidden' name='ik_am' value='$out_summ'>
			<input type='hidden' name='ik_pm_no' value='$inv_id'>
			<input type='hidden' name='ik_desc' value='$desc'>
			<input type='hidden' name='ik_x_user_id' value='$user_ID'>
			<input type='hidden' name='ik_sign' value='$crc'>";
	}
	return $inputs;
}

add_filter('file_scripts_rcl','get_scripts_user_account_rcl');
function get_scripts_user_account_rcl($script){

	$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";				

	$script .= "
		/* Пополняем личный счет пользователя */
			jQuery('.add_count_user').live('click',function(){
					var count = jQuery('.value_count_user');
					var addcount = count.val();
					var dataString = 'action=add_count_user_recall&count='+addcount;

					jQuery.ajax({
						".$ajaxdata."
						success: function(data){
							if(data['otvet']==100){
								jQuery('.redirectform').html(data['redirectform']);			
							} else {
							   alert('Ошибка проверки данных.');
							}
						} 
					});				
					return false;
				});	
		/* Оплачиваем заказ средствами из личного счета */
			jQuery('.pay_order').live('click',function(){
				var idorder = jQuery(this).attr('name');
				var dataString = 'action=pay_order_in_count_recall&idorder='+ idorder;

				jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['otvet']==100){
						jQuery('.order_block').find('.pay_order').each(function() {
							if(jQuery(this).attr('name')==data['idorder']) jQuery(this).remove();
						});
						jQuery('.redirectform').html(data['recall']);
						jQuery('.redirectform-'+data['idorder']).html(data['recall']);
						jQuery('.usercount').html(data['count']+' рублей');
						jQuery('.order-'+data['idorder']+' .remove_order').remove();
						jQuery('#form-payment-'+data['idorder']).remove();
						jQuery('.order-'+data['idorder']+' h4').remove();
					}else{
						alert('Недостаточно средств на счету! Сумма заказа: '+data['recall']);
					}
				} 
				});	  	
				return false;
			});	
		jQuery('.go_to_add_count').click(function(){ 
			jQuery('.count_user').slideToggle();
			return false; 		
		});	
	";
	return $script;
}

require_once("payments.php");
?>