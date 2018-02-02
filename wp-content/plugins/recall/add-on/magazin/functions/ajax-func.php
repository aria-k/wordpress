<?php
/*************************************************
Добавление товара в миникорзину
*************************************************/
function add_in_minibasket_recall(){
	global $rmag_options;
	$id_post = $_POST['id_post'];
	//if($_POST['custom']) $id_custom = $_POST['custom'];
	$prod_id = $id_post.'-product';
	$number = $_POST['number'];
	if(!$number||$number==''||$number==0) $number=1;
	
	if($number>=0){
	
	$sum_price = $_SESSION['sumprice'];
	$product_price = $_SESSION[$prod_id];
	$allprod = $_SESSION['allprod'];
		$allprod = $allprod + $number;
		
		/*if($id_custom>=0){
			$customprice = unserialize(get_post_meta($id_post, 'custom-price', 1));
			$price1 = $customprice[(int)$id_custom]['price']; 
		}else{		
			$price1 = get_post_meta($id_post,'price-products',1);
		}*/
		$price1 = get_post_meta($id_post,'price-products',1);
		
		$price1 = apply_filters('update_price_products_rmag',$price1,$id_post);
		
		$price = $number * $price1;
		$product_price = $price + $product_price;
		$sum_price = $price + $sum_price;
		$_SESSION['sumprice'] = $sum_price;
		$_SESSION[$prod_id] = $product_price;
		$_SESSION['allprod'] = $allprod;
		$log['data_sumprice'] =  $_SESSION['sumprice'];
		$log['sumproduct'] = $_SESSION[$prod_id];
		$log['allprod'] = $allprod;
		$log['empty-content'] = '<div>Всего товаров: <span class="allprod"></span> шт.</div><div>Общая сумма: <span class="sumprice"></span> руб.</div><a href="'.get_permalink($rmag_options['basket_page_rmag']).'">Перейти в корзину</a>';
		$log['recall'] = 100;
	}else{
		$log['recall'] = 200; //Отрицательное значение
	}
	
	echo json_encode($log);	
    exit;
}
add_action('wp_ajax_add_in_minibasket_recall', 'add_in_minibasket_recall');
add_action('wp_ajax_nopriv_add_in_minibasket_recall', 'add_in_minibasket_recall');
/*************************************************
Добавление товара в корзину
*************************************************/
function add_in_basket_recall(){
    $id_post = $_POST['id_post'];
	$prod_id = $id_post.'-product';
	$number = $_POST['number'];
	if(!$number||$number==''||$number==0) $number=1;
	
	if($number>=0){
		
		$sum_price = $_SESSION['sumprice'];
		$product_price = $_SESSION[$prod_id];
		$allprod = $_SESSION['allprod'];
		$allprod = $allprod + $number;	
		
		$price1 = get_post_meta($id_post,'price-products',1);
		
		$price1 = apply_filters('update_price_products_rmag',$price1,$id_post);
		
		$price = $number * $price1;
		$product_price = $price + $product_price;
		$sum_price = $price + $sum_price;
		$num_product = $product_price/$price1;
		$_SESSION['sumprice'] = $sum_price;
		$_SESSION[$prod_id] = $product_price;
		$_SESSION['allprod'] = $allprod;
		$log['data_sumprice'] = $_SESSION['sumprice'];
		$log['sumproduct'] = $_SESSION[$prod_id];
		$log['allprod'] = $allprod;
		$log['id_prod'] = $id_post;
		$log['num_product'] = $num_product;
		$log['recall'] = 100;
	}else{
		$log['recall'] = 200; //Отрицательное значение
	}
	
	echo json_encode($log);
    exit;
}
add_action('wp_ajax_add_in_basket_recall', 'add_in_basket_recall');
add_action('wp_ajax_nopriv_add_in_basket_recall', 'add_in_basket_recall');	
/*************************************************
Уменьшаем товар в корзине
*************************************************/
function remove_out_basket_recall(){
	$id_post = $_POST['id_post'];
	$prod_id = $id_post.'-product';
	$number = $_POST['number'];
	if(!$number||$number==''||$number==0) $number=1;
	
	if($number>=0){
		
		$sum_price = $_SESSION['sumprice'];
		$product_price = $_SESSION[$prod_id];
		$allprod = $_SESSION['allprod'];
		$allprod = $allprod - $number;
		
		$price1 = get_post_meta($id_post,'price-products',1);
		
		$price1 = apply_filters('update_price_products_rmag',$price1,$id_post);
		
		$price = $number * $price1;
		$product_price = $product_price - $price;
		$num_product = $product_price/$price1;
		
		if($num_product>=0){
			$sum_price = $sum_price - $price;
			$_SESSION['sumprice'] = $sum_price;
			$_SESSION[$prod_id] = $product_price;
			$_SESSION['allprod'] = $allprod;
			$log['data_sumprice'] = $_SESSION['sumprice'];
			$log['sumproduct'] = $_SESSION[$prod_id];
			$log['id_prod'] = $id_post;
			$log['allprod'] = $allprod;
			$log['num_product'] = $num_product;
			$log['recall'] = 100;
		}else{
			$log['recall'] = 300;
		}
	}else{
		$log['recall'] = 200; //Отрицательное значение
	}
	
	echo json_encode($log);
    exit;
}
add_action('wp_ajax_remove_out_basket_recall', 'remove_out_basket_recall');
add_action('wp_ajax_nopriv_remove_out_basket_recall', 'remove_out_basket_recall');
/*************************************************
Подтверждение заказа
*************************************************/
function confirm_order_recall(){
	 
	global $user_ID;
	global $wpdb;
	global $rmag_options;
	$time_action = date("Y-m-d H:i:s");
	$count = $_POST['count'];

	if($_POST['count']&&$user_ID){
		
		$get_fields = get_option( 'custom_orders_field' ); 
		$get_fields = unserialize( $get_fields);
		
		$requared = true;
		if($get_fields){
			foreach((array)$get_fields as $custom_field){				
				$slug = str_replace('-','_',$custom_field['slug']);
				if($custom_field['req']==1){
					if($custom_field['type']=='checkbox'){
						$chek = explode('#',$custom_field['field_select']);
						$count_field = count($chek);
						for($a=0;$a<$count_field;$a++){
							$slug_chek = $slug.'_'.$a;
							if($_POST[$slug_chek]=='undefined'){
								$requared = false;
							}else{
								$requared = true;
								break;
							}
						}
					}else{
						if(!$_POST[$slug]) $requared = false;	
					}
				}
			}
		}
		
	if($requared){
		
		if($rmag_options['products_warehouse_recall']==1){ //если включен учет наличия товара
			for($i=1;$i<=$count;$i++){ //проверяем наличие товара
				if($_POST['idprod_'.$i]){
					$amount = get_post_meta($_POST['idprod_'.$i], 'amount_product', 1);
					if($amount>0){
						$new_amount = $amount - $_POST['numprod_'.$i];
						if($new_amount>=0){
							$true_amount[$i] = $_POST['idprod_'.$i];
						}else{
							$false_amount[$i] = $_POST['idprod_'.$i];
						}
					}
				}		
			}
		}
		
		if(!$false_amount){ //если весь товар в наличии, оформляем заказ
		
			$num_max = $wpdb->get_var("SELECT MAX(inv_id) FROM ".RMAG_PREF ."orders_history");
			if($num_max) $inv_id = $num_max+1;
			else $inv_id = rand(0,1000);

			for($i=1;$i<=$count;$i++){
					if($_POST['idprod_'.$i]){
					$price = get_post_meta($_POST['idprod_'.$i],'price-products',1);
					
					$price = apply_filters('update_price_products_rmag',$price,$_POST['idprod_'.$i]);
					
					$amount = get_post_meta($_POST['idprod_'.$i], 'amount_product', 1);
					if($rmag_options['products_warehouse_recall']==1&&$amount){ //формируем резерв товара
						$reserve = get_post_meta($_POST['idprod_'.$i],'reserve_product',1);
						if($reserve) $reserve = $reserve + $_POST['numprod_'.$i];
							else $reserve = $_POST['numprod_'.$i];
						$amount = $amount - $_POST['numprod_'.$i];
						update_post_meta($_POST['idprod_'.$i], 'amount_product', $amount);
						update_post_meta($_POST['idprod_'.$i], 'reserve_product', $reserve);
					}
					$results = $wpdb->insert( RMAG_PREF ."orders_history", 
						array( 
							'inv_id' => "$inv_id", 
							'user' => "$user_ID", 
							'product' => $_POST['idprod_'.$i], 
							'price' => "$price", 
							'count' => $_POST['numprod_'.$i], 
							'time_action' => "$time_action",
							'status' => 1
							)
						);
					}
				}
				
			//$get_fields = get_option( 'custom_orders_field' );
			//$get_fields = unserialize( $get_fields);	
				
			foreach((array)$get_fields as $custom_field){				
				$slug = str_replace('-','_',$custom_field['slug']);
					if($custom_field['type']=='text'&&$_POST[$slug])
						$order_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.$_POST[$slug].'</span></p>';
					if($custom_field['type']=='select'&&$_POST[$slug]||$custom_field['type']=='radio'&&$_POST[$slug])
						$order_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.$_POST[$slug].'</span></p>';
					if($custom_field['type']=='checkbox'){
						$chek = explode('#',$custom_field['field_select']);
						$count_field = count($chek);					
						$n=0;
						for($a=0;$a<$count_field;$a++){
							$slug_chek = $slug.'_'.$a;
							if($_POST[$slug_chek]!='undefined'){
							$n++;
								if($n==1) $chek_field .= $_POST[$slug_chek];
									else $chek_field .= ', '.$_POST[$slug_chek];
							}
						}
						if($n!=0) $order_custom_field .= '<p><b>'.$custom_field['title'].': </b>'.$chek_field.'</p>';
					}					
					if($custom_field['type']=='textarea'&&$_POST[$slug])
						$order_custom_field .= '<p><b>'.$custom_field['title'].':</b></p><p>'.$_POST[$slug].'</p>';
			}
			
			$add_order_details = $wpdb->insert(
				RMAG_PREF ."details_orders",
				array(
				'order_id'=>$inv_id,
				'details_order'=>$order_custom_field)
			);
			
			$order_data = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE inv_id = '$inv_id'");
			
			foreach((array)$order_data as $sing_order){
					$sumprise += "$sing_order->price"*"$sing_order->count";
					$a++;			
			}
			
			$table_order = get_email_table_order_rcl($order_data,$inv_id,$sumprise);
			
			$args = array(
					'role' => 'administrator'
				);
			$users = get_users( $args );

			add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
			$headers = 'From: '.get_bloginfo('name').' <noreaply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
			$subject = 'Сформирован заказ!';
			
			$textmail = '
			<p>Пользователь сформировал заказ в магазине "'.get_bloginfo('name').'".</p>
			<h3>Информация о пользователе:</h3>
			<p><b>Имя</b>: '.get_the_author_meta('display_name',$user_ID).'</p>
			<p><b>Email</b>: '.get_the_author_meta('user_email',$user_ID).'</p>
			<h3>Данные указанные при оформлении:</h3>
			'.$order_custom_field.'
			<p>Заказ №'.$inv_id.' получил статус "Не оплачено".</p>
			<h3>Детали заказа:</h3>
			'.$table_order.'
			<p>Ссылка для управления заказом в админке:</p>  
			<p>'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=manage-rmag&order='.$inv_id.'</p>';
			
			$admin_email = $rmag_options['admin_email_magazin_recall'];
			if($admin_email){
				wp_mail($admin_email, $subject, $textmail, $headers);
			}else{
				foreach((array)$users as $userdata){
					$email = $userdata->user_email;									
					wp_mail($email, $subject, $textmail, $headers);
				}
			}			
			
			$email = get_the_author_meta('user_email',$user_ID);			
			$textmail = '
			<p>Вы сформировали заказ в магазине "'.get_bloginfo('name').'".</p>
			<h3>Информация о пользователе:</h3>
			<p><b>Имя</b>: '.get_the_author_meta('display_name',$user_ID).'</p>
			<p><b>Email</b>: '.$email.'</p>
			<h3>Данные указанные при оформлении:</h3>
			'.$order_custom_field.'
			<p>Заказ №'.$inv_id.' получил статус "Не оплачено".</p>
			<h3>Детали заказа:</h3>
			'.$table_order;				
			wp_mail($email, $subject, $textmail, $headers);
			
			
			if(function_exists('get_inputs_pay_sistem_rcl')){
				$type_order_payment = $rmag_options['type_order_payment'];
				if($type_order_payment==1||$type_order_payment==2){
					if($rmag_options['connect_sale']==1){ //если используется робокасса
						$out_summ = $sumprise;
						$mrh_login = $rmag_options['robologin']; 
						$mrh_pass1 = $rmag_options['onerobopass']; 
						$shpb = 2; //тип платежа. 1 - пополнение личного счета, 2 - оплата заказа
						$shp_item = "2";  
						$culture = "ru"; 

						$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item:shpa=$user_ID:shpb=$shpb"); 

						if($rmag_options['robotest']==1) $formaction = 'http://test.robokassa.ru/Index.aspx';
						else $formaction = 'https://merchant.roboxchange.com/Index.aspx';

						$log['redirectform'] = "
						<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его сейчас или из своего ЛК. Там же вы можете узнать статус вашего заказа.</p>
						<form action='".$formaction."' method=POST>
						".get_inputs_pay_sistem_rcl($inv_id,$out_summ,$shpb,$crc)."
						<input type=submit class='recall-button' value='Оплатить через платежные системы'>
						</form>";
						if($type_order_payment==2) $log['redirectform'] .= '<br /><input class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплатить c личного счета">';
						$log['otvet']=100;
					}
					if($rmag_options['connect_sale']==2){ //если используется интеркасса
						$ik_am = $sumprise;
						$ik_co_id = $rmag_options['interidshop'];
						$ik_pm_no = $inv_id;
						$ik_desc = 'Оплата заказа на сайте';
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

						$log['redirectform'] = "<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его сейчас или из своего ЛК. Там же вы можете узнать статус вашего заказа.</p>
						<form action='https://sci.interkassa.com/' method='POST'>
							".$test_input."
							".get_inputs_pay_sistem_rcl($ik_pm_no,$ik_am,2,$ik_sign)."
							<input type='submit' value='Подтвердить запрос'>
						</form>";										
						if($type_order_payment==2) $log['redirectform'] .= '<br /><input class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплатить c личного счета">';
						$log['otvet']=100;
					}
				}else{			
					$log['redirectform'] = "<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его в любое время в своем личном кабинете. Там же вы можете узнать статус вашего заказа.</p>";
				}
			}else{
				$log['redirectform'] = "<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете следить за статусом своего заказа в своем личном кабинете.</p>";
			}
			
			
			$log['otvet']=100;
			session_destroy();
		} else { //если товар не весь в наличии, формируем сообщение об ошибке и отправляем пользователю
		
			for($i=1;$i<=$count;$i++){
				if($false_amount[$i]){
					$error_amount .= '<p>Наименование товара: <b>'.get_the_title($_POST['idprod_'.$i]).' доступно '.get_post_meta($_POST['idprod_'.$i], 'amount_product', 1).' шт.</b></p>';
				} 
			
			}
			
			$log['otvet']=10;
			$log['amount'] = "<p class='res_confirm' style='margin-top:20px;color:red;border:1px solid #ccc;font-weight:bold;padding:10px;'>Заказ не был создан!<br />Возможно вы пытаетесь зарезервировать большее количество товара, чем есть в наличии.</p>".$error_amount."<p>Пожалуйста уменьшите количество товара в заказе и попробуйте оформить заказ снова.</p>";
			echo json_encode($log);		
			exit;
		}
	}else{
		$log['otvet']=5;
		$log['recall'] = '<p style="text-align:center;color:red;">Пожалуйста, заполните все обязательные поля, отмеченные звездочкой!</p>';	
	}
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);		
    exit;
}
add_action('wp_ajax_confirm_order_recall', 'confirm_order_recall');
add_action('wp_ajax_nopriv_confirm_order_recall', 'confirm_order_recall');
/*************************************************
Смена статуса заказа
*************************************************/
function select_status_order_recall(){
	global $user_ID;
	global $wpdb;
	$order = $_POST['order'];
	$status = $_POST['status'];

	if($_POST['order'])	{
	$res = $wpdb->update( RMAG_PREF ."orders_history", 
				array( 'status' => $status ),
				array( 'inv_id' => $order)
				);
	if($res){			
	switch($status){
		case 1: $status = 'Не оплачен'; break;
		case 2: $status = 'Оплачен'; break;
		case 3: $status = 'В обработке'; break;
		case 4: $status = 'Отправлен'; break;
		case 5: $status = 'Закрыт'; break;
		case 6: $status = 'Корзина'; break;
		}
		
	$log['otvet']=100;
	$log['order']=$order;
	$log['status']=$status;
	}else {
		$log['otvet']=1;
	}
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);	
    exit;
}
add_action('wp_ajax_select_status_order_recall', 'select_status_order_recall');

/*************************************************
Удаление заказа в корзину
*************************************************/
function delete_order_in_trash_recall(){
	global $user_ID;
	global $wpdb;
	global $rmag_options;
	$idorder = $_POST['idorder'];

	if($idorder){
	
	if($rmag_options['products_warehouse_recall']==1){ //списываем резерв товара
	 $status = $wpdb->get_row("SELECT * FROM ".RMAG_PREF ."orders_history WHERE inv_id = '$idorder'");
	 if($status->status==1){ //если товар не был оплачен
		$orders = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE user='$user_ID' AND inv_id='$idorder'");
			foreach((array)$orders as $sumproduct){
				$reserve = get_post_meta($sumproduct->product,'reserve_product',1);
				if($reserve){ //если резев имеется
					$reserve = $reserve - "$sumproduct->count";//уменьшаем резерв
					$amount = get_post_meta($sumproduct->product, 'amount_product', 1);
					$amount = $amount + "$sumproduct->count";//увеличиваем наличие
					update_post_meta($sumproduct->product, 'reserve_product', $reserve);
					update_post_meta($sumproduct->product, 'amount_product', $amount);
				}				 
			}
		}	 
	 }
	 
	//убираем заказ в корзину
	$res = $wpdb->update(RMAG_PREF ."orders_history", 
				array( 'status' => 6 ),
				array( 'user' => "$user_ID", 'inv_id' => "$idorder" )
				);
		if($res){
			$log['otvet']=100;
			$log['idorder']=$idorder;
			//$log['otvet']=100;
		}
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);		
	exit;
}
add_action('wp_ajax_delete_order_in_trash_recall', 'delete_order_in_trash_recall');
add_action('wp_ajax_nopriv_delete_order_in_trash_recall', 'delete_order_in_trash_recall');	
/*************************************************
Полное удаление заказа
*************************************************/
function all_delete_order_recall(){
	global $user_ID;
	global $wpdb;
	$idorder = $_POST['idorder'];

	if($idorder){
	 $res = $wpdb->query("DELETE FROM ". RMAG_PREF ."orders_history WHERE inv_id = '$idorder'");

		if($res){
			$log['otvet']=100;
			$log['idorder']=$idorder;
		}
	} else {
		$log['otvet']=1;
	}
	echo json_encode($log);		
	exit;
}
add_action('wp_ajax_all_delete_order_recall', 'all_delete_order_recall');
add_action('wp_ajax_nopriv_all_delete_order_recall', 'all_delete_order_recall');

/*************************************************
Регистрация пользователя после оформления заказа
*************************************************/
function add_new_user_in_order(){
	global $rmag_options;
	global $wpdb;
	$fio_new_user = $_POST['fio_new_user'];	
	$email_new_user = $_POST['email_new_user'];

//print_r($_POST);	
	
	$get_fields = get_option( 'custom_profile_field' );
	$get_fields = unserialize( $get_fields);
	
	$requared = true;
	if($get_fields){
		foreach((array)$get_fields as $custom_field){				
			$slug = str_replace('-','_',$custom_field['slug']);
			if($custom_field['requared']==1&&$custom_field['register']==1){
				if($custom_field['type']=='checkbox'){
					$chek = explode('#',$custom_field['field_select']);
					$count_field = count($chek);
					for($a=0;$a<$count_field;$a++){
						$slug_chek = $slug.'_'.$a;
						if($_POST[$slug_chek]=='undefined'){
							$requared = false;
						}else{
							$requared = true;
							break;
						}
					}
				}else{
					if(!$_POST[$slug]) $requared = false;	
				}
			}
		}
	}

	$get_order_fields = get_option( 'custom_orders_field' );
	$get_order_fields = unserialize( $get_order_fields);

		if($get_order_fields){
			foreach((array)$get_order_fields as $custom_field){				
				$slug = str_replace('-','_',$custom_field['slug']);
				if($custom_field['req']==1){
					if($custom_field['type']=='checkbox'){
						$chek = explode('#',$custom_field['field_select']);
						$count_field = count($chek);
						for($a=0;$a<$count_field;$a++){
							$slug_chek = $slug.'_'.$a;
							if($_POST[$slug_chek]=='undefined'){
								$requared = false;
							}else{
								$requared = true;
								break;
							}
						}
					}else{
						if(!$_POST[$slug]) $requared = false;	
					}
				}
			}
		}
	
	if($email_new_user&&$requared){
		$res_email = email_exists( $email_new_user );
		$res_login = username_exists($email_new_user);
		$correctemail = is_email($email_new_user);
		$valid = validate_username($email_new_user);
		if($res_login||$res_email||!$correctemail||!$valid){
		
			if(!$valid||!$correctemail){
				$res['int']=1;
				$res['recall'] .= '<p style="text-align:center;color:red;">Вы ввели некорректный email!</p>';
			}
			if($res_login||$res_email){
				$res['int']=1;
				$res['recall'] .= '<p style="text-align:center;color:red;">Этот email уже используется!</p>';
			}		
							
		}else{			
			
			$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
			
			$time_action = date("Y-m-d H:i:s");
			
			$userdata = array(
				'user_pass' => $random_password //обязательно
				,'user_login' => $email_new_user //обязательно
				,'user_nicename' => ''
				,'user_email' => $email_new_user
				,'display_name' => $fio_new_user
				,'nickname' => $email_new_user
				,'first_name' => $fio_new_user
				,'rich_editing' => 'true'  // false - выключить визуальный редактор для пользователя.
			);

			$user_id = wp_insert_user( $userdata );
			
			$wpdb->insert( $wpdb->prefix .'user_action', array( 'user' => $user_id, 'time_action' => '' ));


			//$user_id = wp_create_user($login,$pass,$email);
			
		if($user_id){
		
			if($get_fields){			
				foreach((array)$get_fields as $custom_field){				
					$slug = str_replace('-','_',$custom_field['slug']);
					if($custom_field['type']=='checkbox'){
						$chek = explode('#',$custom_field['field_select']);
						$count_field = count($chek);
						for($a=0;$a<$count_field;$a++){
							$slug_chek = $slug.'_'.$a;
							if($_POST[$slug_chek]!='undefined') update_usermeta($user_id, $slug_chek, $_POST[$slug_chek]);
						}
					}else{
						if($_POST[$slug]) update_usermeta($user_id, $slug, $_POST[$slug]);	
					}
				}
			}																										
					
					//Сразу авторизуем пользователя
					$creds = array();
					$creds['user_login'] = $email_new_user;
					$creds['user_password'] = $random_password;
					$creds['remember'] = true;
					$user = wp_signon( $creds, false );
					
					//Начинаем обработку его заказа
					
					$count = $_POST['count'];

					if($_POST['count']){
					$num_max = $wpdb->get_var("SELECT MAX(inv_id) FROM ".RMAG_PREF ."orders_history");
					if($num_max) $inv_id = $num_max+1;
					else $inv_id = rand(0,1000);

					for($i=1;$i<=$count;$i++){
							if($_POST['idprod_'.$i]){
							$price = get_post_meta($_POST['idprod_'.$i],'price-products',1);
							
							$price = apply_filters('update_price_products_rmag',$price,$_POST['idprod_'.$i]);
							
							$results = $wpdb->insert( RMAG_PREF ."orders_history", 
								array( 
									'inv_id' => "$inv_id", 
									'user' => "$user_id", 
									'product' => $_POST['idprod_'.$i], 
									'price' => "$price", 
									'count' => $_POST['numprod_'.$i], 
									'time_action' => "$time_action",
									'status' => 1
									)
								);
							}
						}
						
					if($get_order_fields){
						foreach((array)$get_order_fields as $order_field){				
							$slug = str_replace('-','_',$order_field['slug']);
								if($order_field['type']=='text'&&$_POST[$slug])
									$order_custom_field .= '<p><b>'.$order_field['title'].':</b> <span>'.$_POST[$slug].'</span></p>';
								if($order_field['type']=='select'&&$_POST[$slug]||$order_field['type']=='radio'&&$_POST[$slug])
									$order_custom_field .= '<p><b>'.$order_field['title'].':</b> <span>'.$_POST[$slug].'</span></p>';
								if($order_field['type']=='checkbox'){
									$chek = explode('#',$order_field['field_select']);
									$count_field = count($chek);					
									$n=0;
									for($a=0;$a<$count_field;$a++){
										$slug_chek = $slug.'_'.$a;
										if($_POST[$slug_chek]!='undefined'){
										$n++;
											if($n==1) $chek_field .= $_POST[$slug_chek];
												else $chek_field .= ', '.$_POST[$slug_chek];
										}
									}
									if($n!=0) $order_custom_field .= '<p><b>'.$order_field['title'].': </b>'.$chek_field.'</p>';
								}					
								if($order_field['type']=='textarea'&&$_POST[$slug])
									$order_custom_field .= '<p><b>'.$order_field['title'].':</b></p><p>'.$_POST[$slug].'</p>';
						}
						
						$add_order_details = $wpdb->insert(
							RMAG_PREF ."details_orders",
							array(
							'order_id'=>$inv_id,
							'details_order'=>$order_custom_field)
						);
					}
					
					foreach((array)$get_fields as $custom_field){				
						$slug = str_replace('-','_',$custom_field['slug']);
							if($custom_field['type']=='text'&&get_the_author_meta($slug,$user_id))
								$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_the_author_meta($slug,$user_id).'</span></p>';
							if($custom_field['type']=='select'&&get_the_author_meta($slug,$user_id)||$custom_field['type']=='radio'&&get_the_author_meta($slug,$user_id))
								$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_the_author_meta($slug,$user_id).'</span></p>';
							if($custom_field['type']=='checkbox'){
								$chek = explode('#',$custom_field['field_select']);
								$count_field = count($chek);					
								$n=0;
								for($a=0;$a<$count_field;$a++){
									$slug_chek = $slug.'_'.$a;
									if(get_the_author_meta($slug_chek,$user_id)){
									$n++;
										if($n==1) $chek_field .= get_the_author_meta($slug_chek,$user_id);
											else $chek_field .= ', '.get_the_author_meta($slug_chek,$user_id);
									}
								}
								if($n!=0) $show_custom_field .= '<p><b>'.$custom_field['title'].': </b>'.$chek_field.'</p>';
							}					
							if($custom_field['type']=='textarea'&&get_the_author_meta($slug,$user_id))
								$show_custom_field .= '<p><b>'.$custom_field['title'].':</b></p><p>'.get_the_author_meta($slug,$user_id).'</p>';
					}
					
					$order_data = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE inv_id = '$inv_id'");
			
					foreach((array)$order_data as $sing_order){
							$sumprise += "$sing_order->price"*"$sing_order->count";
							$a++;			
					}
					
					$table_order = get_email_table_order_rcl($order_data,$inv_id,$sumprise);
					
					$args = array(
						'role' => 'administrator'
					);
					$users = get_users( $args );
					
					add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
					$headers = 'From: '.get_bloginfo('name').' <noreaply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
					$subject = 'Сформирован заказ!';				
					$textmail = '
					<p>Пользователь сформировал заказ в магазине "'.get_bloginfo('name').'".</p>
					<h3>Информация о пользователе:</h3>
					<p><b>Имя</b>: '.$fio_new_user.'</p>
					<p><b>Email</b>: '.$email_new_user.'</p>
					'.$show_custom_field.'
					<h3>Данные указанные при оформлении заказа:</h3>
					'.$order_custom_field.'
					<p>Заказ №'.$inv_id.' получил статус "Не оплачено".</p>
					<h3>Детали заказа:</h3>
					'.$table_order.'
					<p>Ссылка для управления заказом в админке:</p> 
					<p>'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=manage-wpmorders&order='.$inv_id.'</p>		
					';
					
					$admin_email = $rmag_options['admin_email_magazin_recall'];
					if($admin_email){
						wp_mail($admin_email, $subject, $textmail, $headers);
					}else{
						foreach((array)$users as $userdata){
							$email = $userdata->user_email;									
							wp_mail($email, $subject, $textmail, $headers);
						}
					}
										
					$subject = 'Ваши данные для авторизации в личном кабинете';
					$textmail = '
					<p>Вы сформировали заказ в магазине "'.get_bloginfo('name').'".</p>
					<h3>Информация о покупателе:</h3>
					<p><b>Имя</b>: '.$fio_new_user.'</p>
					<p><b>Email</b>: '.$email_new_user.'</p>
					'.$show_custom_field.'
					<h3>Данные указанные при оформлении заказа:</h3>
					'.$order_custom_field.'
					<p>Заказ №'.$inv_id.' получил статус "Не оплачено".</p>
					<h3>Детали заказа:</h3>
					'.$table_order.'					
					<p>Для вас был создан личный кабинет покупателя, где вы сможете следить за сменой статусов ваших заказов, формировать новые заказы и оплачивать их доступными способами</p>					
					<p>Ваши данные для авторизации в вашем личном кабинете:</p>									
					<p>Логин: '.$email_new_user.'</p>
					<p>Пароль: '.$random_password.'</p>					
					<p>В дальнейшем используйте свой личный кабинет для новых заказов на нашем сайте.</p>
					<p>_________________________________________________________________</p>
					<p>Это письмо было сформировано автоматически, не надо отвечать на него.</p>';								
					wp_mail($email_new_user, $subject, $textmail, $headers);
					
					//$log['redirectform'] = "<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его в любое время в своем личном кабинете. Там же вы можете узнать статус вашего заказа.</p>";
					//$log['otvet']=100;
					session_destroy();
					}
					
					if(function_exists('get_inputs_pay_sistem_rcl')){
						$type_order_payment = $rmag_options['type_order_payment'];
						if($type_order_payment==1||$type_order_payment==2){
							if($rmag_options['connect_sale']==1){ //если используется робокасса
								$out_summ = $sumprise;
								$mrh_login = $rmag_options['robologin']; 
								$mrh_pass1 = $rmag_options['onerobopass']; 
								$shp_item = "2"; 
								$shpb = 2;
								$culture = "ru"; 

								$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item:shpa=$user_id:shpb=$shpb"); 

								if($rmag_options['robotest']==1) $formaction = 'http://test.robokassa.ru/Index.aspx';
								else $formaction = 'https://merchant.roboxchange.com/Index.aspx';

								$res['recall'] = "
								<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его сейчас или из своего ЛК. Там же вы можете узнать статус вашего заказа.</p>";
								if($type_order_payment==2) $res['recall'] .= "
								<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Вы можете пополнить свой личный счет на сайте из своего личного кабинета и в будущем оплачивать свои заказы через него</p>
								<p align='center'><a href='".get_redirect_url_rcl(get_author_posts_url($user_id),'order')."'>Перейти в свой личный кабинет</a></p>";
								$res['recall'] .= "<form action='".$formaction."' method=POST>
								".get_inputs_pay_sistem_rcl($inv_id,$out_summ,$shpb,$crc,$user_id)."
								<p align='center'><input class='recall-button' type=submit value='Оплатить сейчас через платежные системы'></p></form>";
								//if($type_order_payment==2) $log['recall'] .= '<br /><input class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплата c личного счета">';
								$res['redirect']=0;
								$res['int']=100;
							}
							if($rmag_options['connect_sale']==2){ //если используется интеркасса
								$ik_am = $sumprise;
								$ik_co_id = $rmag_options['interidshop'];
								$ik_pm_no = $inv_id;
								$ik_desc = 'Оплата заказа на сайте';
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

								

								$res['recall'] = "
								<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Ваш заказ был создан!<br />Заказу присвоен статус - \"Неоплачено\"<br />Вы можете оплатить его сейчас или из своего ЛК. Там же вы можете узнать статус вашего заказа.</p>";
								if($type_order_payment==2) $res['recall'] .= "
								<p class='res_confirm' style='border:1px solid #ccc;font-weight:bold;padding:10px;'>Вы можете пополнить свой личный счет на сайте из своего личного кабинета и в будущем оплачивать свои заказы через него</p>
								<p align='center'><a href='".get_redirect_url_rcl(get_author_posts_url($user_id),'order')."'>Перейти в свой личный кабинет</a></p>";
								$res['recall'] .= "<form action='https://sci.interkassa.com/' method='POST'>
									".$test_input."
									".get_inputs_pay_sistem_rcl($ik_pm_no,$ik_am,2,$ik_sign,$user_id)."
									<p align='center'><input class='recall-button' type=submit value='Оплатить сейчас через платежные системы'></p>
									</form>";
								//if($type_order_payment==2) $log['recall'] .= '<br /><input class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплата c личного счета">';
								$res['redirect']=0;
								$res['int']=100;
							}
						}else{						
							$res['int']=100;
							$res['redirect']= get_author_posts_url($user_id);					
							$res['recall']='<p style="text-align:center;color:green;">Ваш заказ был создан!<br />Проверьте свою почту.</p>';
						}
					}else{
						$res['int']=100;
						$res['redirect']= get_author_posts_url($user_id);					
						$res['recall']='<p style="text-align:center;color:green;">Ваш заказ был создан!<br />Проверьте свою почту.</p>';
					}
				}						
		}
	}else{
		$res['int']=1;
		$res['recall'] = '<p style="text-align:center;color:red;">Пожалуйста, заполните все обязательные поля, отмеченные звездочкой!</p>';		
		} 
	echo json_encode($res);
	exit;
}
add_action('wp_ajax_add_new_user_in_order', 'add_new_user_in_order');
add_action('wp_ajax_nopriv_add_new_user_in_order', 'add_new_user_in_order');


/*************************************************
Оплата заказа средствами с личного счета
*************************************************/
function pay_order_in_count_recall(){
	global $user_ID;
	global $wpdb;
	global $rmag_options;
	$inv_id = $_POST['idorder'];

	if(!$inv_id||!$user_ID){
		$log['otvet']=1;
		echo json_encode($log);
		exit;	
	}
	
	$order_data = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE user='$user_ID' AND inv_id='$inv_id'");

	$summa_order = 0;
	foreach((array)$order_data as $sumproduct){
		$summa_product = "$sumproduct->count"*$sumproduct->price;
		$summa_order = $summa_order + $summa_product; 
	}
	
	$oldusercount = $wpdb->get_row("SELECT * FROM ".RMAG_PREF ."user_count WHERE user='$user_ID'");

	if(!$oldusercount){
		$log['otvet']=1;
		$log['recall'] = $summa_order;
		echo json_encode($log);
		exit;		
	}
			
	$newusercount = "$oldusercount->count" - $summa_order;
				
	if($newusercount<0){
		$log['otvet']=1;
		$log['recall'] = $summa_order;
		echo json_encode($log);
		exit;				
	}
		
	$wpdb->update(RMAG_PREF .'user_count', 
		array( 'count' => "$newusercount" ),
		array( 'user' => "$user_ID" )
	);
		
	$result = $wpdb->update( RMAG_PREF ."orders_history", 
	array( 'status' => 2),
	array( 'inv_id' => $inv_id)
	);
											
	if(!$result){
		$log['otvet']=1;
		$log['recall'] = 'Ошибка запроса!';
		echo json_encode($log);
		exit;
	}
	
	if($rmag_options['products_warehouse_recall']==1){ //списываем резерв товара
		foreach((array)$order_data as $sumproduct){
			$reserve = get_post_meta($sumproduct->product,'reserve_product',1);
			if($reserve){ //если резев имеется
				$reserve = $reserve - "$sumproduct->count";
				update_post_meta($sumproduct->product, 'reserve_product', $reserve);
			}							
		}
	}							
		
	//Если работает реферальная система и партнеру начисляются проценты с покупок его реферала
	if(function_exists('add_referall_incentive_order')) 
		add_referall_incentive_order($user_ID,$summa_order);
					
	$get_fields = get_option( 'custom_profile_field' );
	$get_fields = unserialize( $get_fields);	
							
	foreach((array)$get_fields as $custom_field){				
		$slug = str_replace('-','_',$custom_field['slug']);
		if($custom_field['type']=='text'&&get_the_author_meta($slug,$user_ID))
			$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_the_author_meta($slug,$user_ID).'</span></p>';
		if($custom_field['type']=='select'&&get_the_author_meta($slug,$user_ID)||$custom_field['type']=='radio'&&get_the_author_meta($slug,$user_ID))
		$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_the_author_meta($slug,$user_ID).'</span></p>';
		if($custom_field['type']=='checkbox'){
			$chek = explode('#',$custom_field['field_select']);
			$count_field = count($chek);					
			$n=0;
			for($a=0;$a<$count_field;$a++){
				$slug_chek = $slug.'_'.$a;
				if(get_the_author_meta($slug_chek,$user_ID)){
					$n++;
					if($n==1) $chek_field .= get_the_author_meta($slug_chek,$user_ID);
					else $chek_field .= ', '.get_the_author_meta($slug_chek,$user_ID);
				}
			}
			if($n!=0) $show_custom_field .= '<p><b>'.$custom_field['title'].': </b>'.$chek_field.'</p>';
		}					
		if($custom_field['type']=='textarea'&&get_the_author_meta($slug,$user_ID))
			$show_custom_field .= '<p><b>'.$custom_field['title'].':</b></p><p>'.get_the_author_meta($slug,$user_ID).'</p>';
	}	
							
	//$order_data = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE inv_id = '$inv_id'");
						
	foreach((array)$order_data as $sing_order){
		$sumprise += "$sing_order->price"*"$sing_order->count";
		$a++;			
	}
	
	$table_order = get_email_table_order_rcl($order_data,$inv_id,$sumprise);		
						
	$args = array(
		'role' => 'administrator'
	);
	$users = get_users( $args );

	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	$headers = 'From: '.get_bloginfo('name').' <noreaply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
	$subject = 'Заказ оплачен!';				
	$textmail = '
	<p>Пользователь оплатил заказ в магазине "'.get_bloginfo('name').'" средствами со своего личного счета.</p>
	<h3>Информация о пользователе:</h3>
	<p><b>Имя</b>: '.get_the_author_meta('display_name',$user_ID).'</p>
	<p><b>Email</b>: '.get_the_author_meta('user_email',$user_ID).'</p>
	'.$show_custom_field.'
	<p>Заказ №'.$inv_id.' получил статус "Оплачено".</p>
	<h3>Детали заказа:</h3>
	'.$table_order.'
	<p>Ссылка для управления заказом в админке:</p>  
	<p>'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=manage-rmag&order='.$inv_id.'</p>';
	
	$admin_email = $rmag_options['admin_email_magazin_recall'];
	if($admin_email){
		wp_mail($admin_email, $subject, $textmail, $headers);
	}else{
		foreach((array)$users as $userdata){
			$email = $userdata->user_email;									
			wp_mail($email, $subject, $textmail, $headers);
		}
	}
	
	$email = get_the_author_meta('user_email',$user_ID);				
	$textmail = '
	<p>Вы оплатили заказ в магазине "'.get_bloginfo('name').'" средствами со своего личного счета.</p>
	<h3>Информация о покупателе:</h3>
	<p><b>Имя</b>: '.get_the_author_meta('display_name',$user_ID).'</p>
	<p><b>Email</b>: '.get_the_author_meta('user_email',$user_ID).'</p>
	'.$show_custom_field.'
	<p>Заказ №'.$inv_id.' получил статус "Оплачено".</p>
	<h3>Детали заказа:</h3>
	'.$table_order.'
	<p>Ваш заказ оплачен и поступил в обработку. Вы можете следить за сменой его статуса из своего личного кабинета</p>  
	<p>Это письмо было создано автоматически, не надо отвечать на него</p>';				
	wp_mail($email, $subject, $textmail, $headers);

	do_action('payorder_user_count_rcl',$user_ID,$sumprise,'Оплата заказа №'.$inv_id.' средствами с личного счета',1);	
		
	$log['recall'] = "<p style='color:green;font-weight:bold;padding:10px; border:2px solid green;'>Ваш заказ успешно оплачен! Соответствующее уведомление было выслано администрации сервиса.</p>";
	$log['count'] = $newusercount;
	$log['idorder']=$inv_id;
	$log['otvet']=100;
	echo json_encode($log);
	exit;	
}
add_action('wp_ajax_pay_order_in_count_recall', 'pay_order_in_count_recall');
?>