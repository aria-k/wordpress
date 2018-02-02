<?php
function get_rmag_global_unit(){
	global $wpdb;
	global $rmag_options;
	$rmag_options = unserialize(get_option('primary-rmag-options'));
	define('RMAG_PREF', $wpdb->prefix."rmag_");
}
add_action('init','get_rmag_global_unit',10);

if (!session_id()) { session_start(); }

require_once("admin-pages.php");
require_once("functions.php");
require_once("functions/shortcodes.php");
require_once("functions/ajax-func.php");
require_once("functions/post-func.php");

add_filter('the_button_wprecall','add_wprecall_order_button',10,2);

function add_wprecall_order_button($button, $author_lk){
global $user_ID, $rcl_options;;
if($user_ID==$author_lk){
	if(!$button) $status = 'active';
	if(!$rcl_options['tab_rmag']) $rcl_options['tab_rmag'] = 'Заказы';
	$button .= ' <a href="#" id="order" class="block_button '.$status.'">'.$rcl_options['tab_rmag'].'</a> ';
}
return $button;
}

add_filter('the_block_wprecall','wp_recall_magazin',10,2);

function wp_recall_magazin($block_wprecall, $author_lk){
global $wpdb;
global $user_ID;
global $rmag_options;

	if($user_ID!=$author_lk) return $block_wprecall;
	if(!$block_wprecall) $status = 'active';
	$magazine_block .= '<div class="order_block recall_content_block '.$status.'">';

	$n=0;
				
	$orders = $wpdb->get_results("SELECT * FROM ".RMAG_PREF ."orders_history WHERE user='$user_ID' ORDER BY ID DESC");
	if($orders){
	$inv_id = 0;
	foreach($orders as $order){
	if($order->status!=6){
			$n++;
			$tr++;
				if($inv_id != $order->inv_id){
				$inv_id = $order->inv_id;
				$tr=1;
				if($n>1){
				$magazine_block .= '</table>';
				$magazine_block .= '</div>';
				}
				$a=0;
				foreach($orders as $sing_order){
					if($inv_id == $sing_order->inv_id){
						$sumprise[$inv_id] += "$sing_order->price"*"$sing_order->count";
						$a++;
					}			
				}
				$magazine_block .= '<div class="order-'.$order->inv_id.'">';
				$magazine_block .= '<h3 style="margin-bottom:5px;">Заказ ID: '.$order->inv_id.'</h3>';
				switch($order->status){
					case 1: $status = 'Не оплачен'; break;
					case 2: $status = 'Оплачен'; break;
					case 3: $status = 'В обработке'; break;
					case 4: $status = 'Отправлен'; break;
					case 5: $status = 'Закрыт'; break;					
				}
				$magazine_block .= '<h4 style="margin-bottom:5px;">Статус заказа: '.$status.'</h4>';			
				if($order->status == 1||$order->status == 5) $magazine_block .= '<input  style="margin:0 10px 10px 0;" class="remove_order recall-button" type="button" name="'.$order->inv_id.'" value="Удалить">';
				if($order->status==1&&function_exists('get_inputs_pay_sistem_rcl')){
				$type_order_payment = $rmag_options['type_order_payment'];
					if($type_order_payment==1||$type_order_payment==2){
						if($rmag_options['connect_sale']==1){ //если используется робокасса
							$out_summ = $sumprise[$inv_id];
							$mrh_login = $rmag_options['robologin']; 
							$mrh_pass1 = $rmag_options['onerobopass']; 
							$shpb = 2;
							$inv_id = $order->inv_id; 
							$shp_item = "2";  
							$culture = "ru"; 

							$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item:shpa=$user_ID:shpb=$shpb"); 

							if($rmag_options['robotest']==1) $formaction = 'http://test.robokassa.ru/Index.aspx';
							else $formaction = 'https://merchant.roboxchange.com/Index.aspx';

							$magazine_block .= "<form id='form-payment-".$inv_id."' style='display: inline;' action='".$formaction."' method=POST>
							".get_inputs_pay_sistem_rcl($inv_id,$out_summ,$shpb,$crc)."
							<input class='recall-button' type=submit value='Оплатить сейчас через платежные системы'>
							</form>";
							if($type_order_payment==2) $magazine_block .= '<input style="margin-left: 10px;" class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплатa c личного счета">';
						}
						if($rmag_options['connect_sale']==2){ //если используется интеркасса
							$inv_id = $order->inv_id;
							
							$ik_am = $sumprise[$inv_id];
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

							$magazine_block .= "<form id='form-payment-".$inv_id."' style='display: inline;' action='https://sci.interkassa.com/' method='POST'>
								".$test_input."
								".get_inputs_pay_sistem_rcl($inv_id,$ik_am,2,$ik_sign)."
								<input type='submit' value='Подтвердить запрос'>
							</form>";
							
							if($type_order_payment==2) $magazine_block .= '<input style="margin-left: 10px;" class="pay_order recall-button" type="button" name="'.$inv_id.'" value="Оплатa c личного счета">';
						}
					}else{
						$magazine_block .= '<input class="pay_order  recall-button" type="button" name="'.$order->inv_id.'" value="Оплатить">';
					}
				}
				$magazine_block .= '<div class="redirectform-'.$order->inv_id.'"></div>';
				$magazine_block .= '<table><tr><td>№ п/п</td><td>Наименование</td><td>Цена</td><td>Количество</td><td>Сумма</td></tr>';
				}
				$magazine_block .= '<tr><td>'.$tr.'</td><td>'.get_the_title($order->product).'</td><td>'.$order->price.'</td><td>'.$order->count.'</td><td>'.$order->price*$order->count.'</td></tr>';
				if($tr == $a) $magazine_block .= '<tr style="font-weight:bold;"><td colspan="4">Сумма заказа</td><td>'.$sumprise[$inv_id].'</td></tr>';
		}
	}
	if($n!=0) $magazine_block .= '</table></div>';
	
	$magazine_block .= "<script type='text/javascript'>
	jQuery(function(){
		jQuery('.value_count_user').click(function(){
			jQuery('.redirectform').empty();
		});
	});
		</script>";	
	}else{
		$magazine_block .= 'У вас пока не оформлено ни одного заказа.';
	}
	$magazine_block .= '</div>';//конец блока заказов
	
	$block_wprecall .= $magazine_block;
	
	return $block_wprecall;
}

add_filter('file_scripts_rcl','get_scripts_magazine_rcl');
function get_scripts_magazine_rcl($script){

	$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";				

	$script .= "
		jQuery('.slider-products').bxSlider({
			auto:true,
			pause:10000
		});
		/* Удаляем заказ пользователя в корзину */
			jQuery('.remove_order').live('click',function(){
				var idorder = jQuery(this).attr('name');
				var dataString = 'action=delete_order_in_trash_recall&idorder='+ idorder;

				jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['otvet']==100){
						jQuery('.order-'+data['idorder']).remove();
					}
				} 
				});	  	
				return false;
			});
		/* Увеличиваем количество товара в корзине */
			jQuery('.add-product').live('click',function(){
				var id_post = jQuery(this).attr('id');		
				var number = jQuery('#number-product-'+id_post).val();
				var dataString = 'action=add_in_basket_recall&id_post='+ id_post+'&number='+ number;
				jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['recall']==100){
						jQuery('.sumprice').empty().html(data['data_sumprice']);
						jQuery('.value_count_user').attr('value', data['data_sumprice']);
						jQuery('.sumprod-'+data['id_prod']).empty().html(data['sumproduct']);
						jQuery('.numprod-'+data['id_prod']).empty().html(data['num_product']);
						jQuery('.numhidden-'+data['id_prod']).attr('value', data['num_product']);
						jQuery('.allprod').empty().html(data['allprod']);
						
					}
					if(data['recall']==200){
						alert('Отрицательное значение!');
					}
				} 
				});	  	
				return false;
			});
		/* Уменьшаем товар количество товара в корзине */
			jQuery('.remove-product').live('click',function(){
				var id_post = jQuery(this).attr('id');
				var number = jQuery('#number-product-'+id_post).val();
				var num = parseInt(jQuery('.numprod-'+id_post).html());
				if(num>0){
					var dataString = 'action=remove_out_basket_recall&id_post='+ id_post+'&number='+ number;
					jQuery.ajax({
					".$ajaxdata."
					success: function(data){
						if(data['recall']==100){
							jQuery('.sumprice').empty().html(data['data_sumprice']);
							jQuery('.sumprod-'+data['id_prod']).empty().html(data['sumproduct']);
							var numprod = data['num_product'];
							if(numprod>0){
								jQuery('.numprod-'+data['id_prod']).empty().html(numprod);
								jQuery('.numhidden-'+data['id_prod']).attr('value', numprod);
							}else{
								var numberproduct = 0;
								jQuery('.prodrow-'+data['id_prod']).remove();
								jQuery('.basket-table').find('.number').each(function() {	
									numberproduct ++;
									jQuery(this).html(numberproduct);
								});
							}					
							jQuery('.allprod').empty().html(data['allprod']);
						}
						if(data['recall']==200){
							alert('Отрицательное значение!');
						}
						if(data['recall']==300){
							alert('Вы пытаетесь удалить из корзины больше товара чем там есть!');
						}
					} 
					});	
				}
				return false;
			});			
		/* Кладем товар в корзину */	
			jQuery('.add_basket').live('click',function(){
				var id_post = jQuery(this).attr('id');
				var id_custom_prod = jQuery(this).attr('name');
				if(id_custom_prod){
					var number = jQuery('#number-custom-product-'+id_custom_prod).val();
				}else{
					var number = jQuery('#number_product').val();
				}
				var dataString = 'action=add_in_minibasket_recall&id_post='+ id_post+'&number='+number+'&custom='+id_custom_prod;
				jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['recall']==100){
						jQuery('.empty-basket').replaceWith(data['empty-content']);
						jQuery('.sumprice').html(data['data_sumprice']);
						jQuery('.allprod').html(data['allprod']);
						alert('Добавлено в корзину!');
					}
					if(data['recall']==200){
						alert('Отрицательное значение!');
					}
				} 
				});	  	
				return false;
			});
	";
	return $script;
}
?>