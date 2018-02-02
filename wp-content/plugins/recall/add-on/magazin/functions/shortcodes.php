<?php
function get_short_basket_rmag(){
	global $rmag_options;
	if($rmag_options['add_basket_button_recall']==1) add_shortcode('add-basket','add_basket_button_product');
	else add_filter('the_content','add_basket_button_product');
}
add_action('init','get_short_basket_rmag');

//кнопку добавления заказа на странице товара
function add_basket_button_product($content){
global $post,$rmag_options;;

	if($post->post_type!=='products') return $content; 

	if(get_post_meta($post->ID,'price-products',1)!==''){ 
		$price = '<div class="price-basket-product">Цена: '.get_post_meta($post->ID,'price-products',1).' руб. ';
		if($rmag_options['products_warehouse_recall']!=1||get_post_meta($post->ID, 'availability_product', 1)!='empty'){ 
			$amount = get_post_meta($post->ID, 'amount_product', 1);
			if($amount>0||$amount==false){
				$price .= '<input type="text" size="2" name="number_product" id="number_product" value="1"><input type="button" class="recall-button add_basket" id="'.$post->ID.'" value="Добавить в корзину">';								
			}
		}
		$price .= '</div>';
	}
	
	$customprice = unserialize(get_post_meta($post->ID, 'custom-price', 1));
	if($customprice){
		$cnt = count($customprice);
		for($a=0;$a<$cnt;$a++){												
			$price .= '<div class="price-basket-product">'.$customprice[$a]['title'].' - '.$customprice[$a]['price'].'р. <input type="text" size="2" name="number_custom_product" id="number-custom-product-'.$a.'" value="1"><input type="button" name="'.$a.'" class="recall-button add_basket" id="'.$post->ID.'" value="Добавить в корзину"></div>';
		}
	}
	
	return $content.$price;
}

function shortcode_mini_basket() {

global $post;
global $rmag_options;
$post_id = $post->ID;
$prod_id = $post_id.'-product';
$sumprice = $_SESSION['sumprice'];
$product_price = $_SESSION[$prod_id];
$allprod = $_SESSION['allprod'];

	$minibasket .= '<div>В вашей корзине:</div>';	
	if($allprod){
	$minibasket .= '<div>Всего товаров: <span class="allprod">'.$allprod.'</span> шт.</div>
	<div>Общая сумма: <span class="sumprice">'.$sumprice.'</span> руб.</div>
	<a href="'.get_permalink($rmag_options['basket_page_rmag']).'">Перейти в корзину</a>';
	}else{
	$minibasket .= '<div class="empty-basket" style="text-align:center;">Пока пусто</div>';
	}
	return $minibasket;
}
add_shortcode('minibasket', 'shortcode_mini_basket');

function shortcode_page_basket() {

$sumprice = $_SESSION['sumprice'];
$product_price = $_SESSION[$prod_id];
$allprod = $_SESSION['allprod'];
$n=0;
global $user_ID;

	$args = array('numberposts' => -1,'order' => 'ASC','post_type' => 'products','post_status' => 'published');	
	$products = get_posts($args);
	
	$basket .= '<table class="basket-table"><tr claSS="head-table"><td>№п/п</td><td></td><td>Наименование товара</td><td>Цена</td><td>Кол-во</td><td>Сумма</td><td></td></tr>';
	foreach((array)$products as $product){
		if($_SESSION[$product->ID.'-product']){
		$n++;
		$price = get_post_meta($product->ID,'price-products',1);
		$price = apply_filters('update_price_products_rmag',$price,$product->ID);
		$product_price = $_SESSION[$product->ID.'-product'];
		$numprod = ($product_price/$price);
			$basket .= '<tr class="prodrow-'.$product->ID.'"><td class="number">'.$n.'</td><td>'.get_the_post_thumbnail( $product->ID, array(50,50) ).'</td><td><a href="'.get_permalink($product->ID).'">'.get_the_title($product->ID).'</a></td><td>'.$price.'</td><td class="numprod-'.$product->ID.'">'.$numprod.'</td><td class="sumprod-'.$product->ID.'">'.$product_price.'</td><td class="add_remove"><input type="text" size="2" name="number_product" class="number_product" id="number-product-'.$product->ID.'" value="1"><a class="add-product" id="'.$product->ID.'" href="#">+</a>/<a class="remove-product" id="'.$product->ID.'" href="#">-</a>
			<input class="idhidden" name="idhidden" type="hidden" value="'.$product->ID.'">
			<input class="numhidden-'.$product->ID.'" name="productnum" type="hidden" value="'.$numprod.'">
			</td></tr>';
		}
	}
	$basket .= '<tr><td colspan="4">Итого:</td><td class="allprod">'.$allprod.'</td><td class="sumprice" colspan="2">'.$sumprice.'</td></tr></table>';
	
	if($allprod){
	
		$get_fields_order = get_option( 'custom_orders_field' );
		$get_fields_order = unserialize( $get_fields_order);
				
			if($get_fields_order){
				$number_field=0;
					foreach((array)$get_fields_order as $custom_field){
				
						$slug = str_replace('-','_',$custom_field['slug']);
						if($custom_field['requared']==1){
							$requared = ' <span class="req-star">*</span> ';
							$req = 'required';
						}else{ 
							$requared = '';
							$req = '';
						}
						$order_field .= '<tr><td><label>'.$custom_field['title'].$requared.':</label></th>';
						if($custom_field['type']=='text')
							$order_field .= '<td><input '.$req.' type="text" name="'.$slug.'" class="regular-text" id="'.$slug.'" maxlength="50" value="" /><br/></td>';
						if($custom_field['type']=='textarea')
							$order_field .= '<td><textarea '.$req.' name="'.$slug.'" class="regular-text" id="'.$slug.'" rows="5" cols="50"></textarea></td>';
						if($custom_field['type']=='select'){
							$fields = explode('#',$custom_field['field_select']);
							$count_field = count($fields);
							for($a=0;$a<$count_field;$a++){
								$field_select .='<option value="'.$fields[$a].'">'.$fields[$a].'</option>';
							}
							$order_field .= '<td><select '.$req.' name="'.$slug.'" class="regular-text" id="'.$slug.'">
							'.$field_select.'
							</select></td>';
						}
						if($custom_field['type']=='file') 
							$order_field .='<td><input type="file" name="'.$slug.'" id="'.$slug.'"></td>';
						$order_value[$number_field]['other'] .= $slug;
						if($custom_field['type']=='checkbox'){
							$chek = explode('#',$custom_field['field_select']);
							$count_field = count($chek);
							$order_field .='<td>';
							for($a=0;$a<$count_field;$a++){
								$number_field++;
								$slug_chek = $slug.'_'.$a;
								$order_field .='<input type="checkbox" id="'.$slug_chek.'" name="'.$slug_chek.'" value="'.$chek[$a].'"> '.$chek[$a].'<br />';
								$order_value[$number_field]['chek'] .= $slug_chek;
							}
							$order_field .='</td>';
						}
						if($custom_field['type']=='radio'){
							$radio = explode('#',$custom_field['field_select']);
							$count_field = count($radio);
							$order_field .='<td>';
							for($a=0;$a<$count_field;$a++){
								$number_field++;
								$slug_chek = $slug.'_'.$a;
								$order_field .='<input type="radio" '.checked($a,0,false).' name="'.$slug.'" id="'.$slug_chek.'" value="'.$radio[$a].'"> '.$radio[$a].'<br />';
								$order_value[$number_field]['radio']['name'] .= $slug;
								$order_value[$number_field]['radio']['id'] .= $slug_chek;
							}
							
							$order_field .='</td>';
						}
						
						$order_field .= '</tr>';
						$number_field++;
						
					}					
				}
	
		if($user_ID){
			$basket .= '<div class="confirm" style="text-align:center;">';
			if($order_field) $basket .= '<h3 align="center">Для оформления заказа заполните форму ниже:</h3>
			<div id="regnewuser"  style="display:none;"></div>
			<table class="form-table">
			'.$order_field.'
			</table>';
			$basket .= '<input class="confirm_order recall-button" type="button" value="Подтвердить заказ">	
			</div>
			<div class="redirectform" style="text-align:center;"></div>';
			$basket .= "<script>
			jQuery(function(){
				jQuery('.confirm_order').live('click',function(){";
				foreach((array)$order_value as $value){
						if($value['chek']){
							$basket .=  "if(jQuery('#".$value['chek']."').attr('checked')=='checked') var ".$value['chek']." = jQuery('#".$value['chek']."').attr('value');";
							$reg_request .= "+'&".$value['chek']."='+".$value['chek'];
						}
						if($value['radio']){
							$basket .=  "if(jQuery('#".$value['radio']['id']."').attr('checked')=='checked') var ".$value['radio']['name']." = jQuery('#".$value['radio']['id']."').attr('value');";
							$reg_radio .= "+'&".$value['radio']['name']."='+".$value['radio']['name'];
						}
						if($value['other']){
							$basket .=  "var ".$value['other']." = jQuery('#".$value['other']."').attr('value');";
							$reg_request .= "+'&".$value['other']."='+".$value['other'];
						}
					}
				$basket .= "
					var idprod = new Array();
					var numprod = new Array();
					var postdata = new Array();
					var i=0;
					jQuery('.basket-table').find('.idhidden').each(function(){
						i++;
						idprod[i] = jQuery(this).attr('value');
						numprod[i] = jQuery('.numhidden-'+idprod[i]).attr('value');
						postdata += 'idprod_'+[i]+'='+ idprod[i]+'&numprod_'+[i]+'='+numprod[i]+'&';			
					});
					var dataString_count = postdata+'action=confirm_order_recall&count='+i".$reg_request.$reg_radio.";
					jQuery.ajax({
					type: 'POST',
					data: dataString_count,
					dataType: 'json',
					url: '/wp-admin/admin-ajax.php',
					success: function(data){
						if(data['otvet']==100){
							jQuery('.redirectform').html(data['redirectform']);
							jQuery('.confirm').remove();
							jQuery('.add_remove').empty();
						} else if(data['otvet']==10){
						   jQuery('.redirectform').html(data['amount']);
						} else if(data['otvet']==5){
							jQuery('#regnewuser').html(data['recall']);
							jQuery('#regnewuser').slideDown(1500).delay(5000).slideUp(1500);
						}else {
						   alert('Ошибка проверки данных.');
						}
					} 
					});	
					
					return false;
				});
			});
			</script>";
		}else{
			$get_fields = get_option( 'custom_profile_field' );
			$get_fields = unserialize( $get_fields);
				
				if($get_fields){
				$number_field=0;
					foreach((array)$get_fields as $custom_field){
						if($custom_field['register']==1){				
						$slug = str_replace('-','_',$custom_field['slug']);
						if($custom_field['requared']==1){
							$requared = ' <span class="req-star">*</span> ';
							$req = 'required';
						}else{ 
							$requared = '';
							$req = '';
						}
						$field .= '<tr><td><label for="pass1">'.$custom_field['title'].$requared.':</label></th>';
						if($custom_field['type']=='text')
							$field .= '<td><input '.$req.' type="text" name="'.$slug.'" class="regular-text" id="'.$slug.'" maxlength="50" value="" /><br/></td>';
						if($custom_field['type']=='textarea')
							$field .= '<td><textarea '.$req.' name="'.$slug.'" class="regular-text" id="'.$slug.'" rows="5" cols="50"></textarea></td>';
						if($custom_field['type']=='select'){
							$fields = explode('#',$custom_field['field_select']);
							$count_field = count($fields);
							for($a=0;$a<$count_field;$a++){
								$field_select .='<option value="'.$fields[$a].'">'.$fields[$a].'</option>';
							}
							$field .= '<td><select '.$req.' name="'.$slug.'" class="regular-text" id="'.$slug.'">
							'.$field_select.'
							</select></td>';
						}
						$name_value[$number_field]['other'] .= $slug;
						if($custom_field['type']=='checkbox'){
							$chek = explode('#',$custom_field['field_select']);
							$count_field = count($chek);
							$field .='<td>';
							for($a=0;$a<$count_field;$a++){
								$number_field++;
								$slug_chek = $slug.'_'.$a;
								$field .='<input type="checkbox" id="'.$slug_chek.'" name="'.$slug_chek.'" value="'.$chek[$a].'"> '.$chek[$a].'<br />';
								$name_value[$number_field]['chek'] .= $slug_chek;
							}
							$field .='</td>';
						}
						if($custom_field['type']=='radio'){
							$radio = explode('#',$custom_field['field_select']);
							$count_field = count($radio);
							$field .='<td>';
							for($a=0;$a<$count_field;$a++){
								$number_field++;
								$slug_chek = $slug.'_'.$a;
								$field .='<input type="radio" '.checked($a,0,false).' name="'.$slug.'" id="'.$slug_chek.'" value="'.$radio[$a].'"> '.$radio[$a].'<br />';
								$name_value[$number_field]['radio']['name'] .= $slug;
								$name_value[$number_field]['radio']['id'] .= $slug_chek;
							}
							
							$field .='</td>';
						}
						$field .= '</tr>';
						$number_field++;
						}
					}					
				}
		
			$basket .= '<div class="confirm" style="text-align:left;">
			<h3 align="center">Для оформления заказа заполните форму ниже:</h3>
			<div id="regnewuser"  style="display:none;"></div>
			<table class="form-table">
			<tr><td>Укажите ваше Имя и Фамилию</td><td><input type="text" class="fio_new_user" name="fio_new_user" value=""></td></tr>
			<tr><td>Укажите ваш E-mail <span class="req-star">*</span></td><td><input required type="text" class="email_new_user" name="email_new_user" value=""></td></tr>
			'.$field.'
			'.$order_field.'
			</table>
			<p align="center"><input type="button" class="add_new_user_in_order recall-button" name="add_new_user_in_order" value="Оформить заказ"></p>
		
			</div>';
			$basket .= "<script>
			jQuery(function(){
				jQuery('.add_new_user_in_order').live('click',function(){";
					foreach((array)$order_value as $value){
						if($value['chek']){
							$basket .=  'if(jQuery("#'.$value['chek'].'").attr("checked")=="checked") var '.$value['chek'].' = jQuery("#'.$value['chek'].'").attr("value");';
							$reg_request .= '+"&'.$value['chek'].'="+'.$value['chek'];
						}
						if($value['radio']){
							$basket .=  'if(jQuery("#'.$value['radio']['id'].'").attr("checked")=="checked") var '.$value['radio']['name'].' = jQuery("#'.$value['radio']['id'].'").attr("value");';
							$reg_radio .= '+"&'.$value['radio']['name'].'="+'.$value['radio']['name'];
						}
						if($value['other']){
							$basket .=  'var '.$value['other'].' = jQuery("#'.$value['other'].'").attr("value");';
							$reg_request .= '+"&'.$value['other'].'="+'.$value['other'];
						}
					}
					foreach((array)$name_value as $value){
						if($value['chek']){
							$basket .=  'if(jQuery("#'.$value['chek'].'").attr("checked")=="checked") var '.$value['chek'].' = jQuery("#'.$value['chek'].'").attr("value");';
							$reg_request .= '+"&'.$value['chek'].'="+'.$value['chek'];
						}
						if($value['radio']){
							$basket .=  'if(jQuery("#'.$value['radio']['id'].'").attr("checked")=="checked") var '.$value['radio']['name'].' = jQuery("#'.$value['radio']['id'].'").attr("value");';
							$reg_radio .= '+"&'.$value['radio']['name'].'="+'.$value['radio']['name'];
						}
						if($value['other']){
							$basket .=  'var '.$value['other'].' = jQuery("#'.$value['other'].'").attr("value");';
							$reg_request .= '+"&'.$value['other'].'="+'.$value['other'];
						}
					}
					$basket .= "var idprod = new Array();
					var numprod = new Array();
					var postdata = new Array();
					var i=0;
					jQuery('.basket-table').find('.idhidden').each(function(){
						i++;
						idprod[i] = jQuery(this).attr('value');
						numprod[i] = jQuery('.numhidden-'+idprod[i]).attr('value');
						postdata += 'idprod_'+[i]+'='+ idprod[i]+'&numprod_'+[i]+'='+numprod[i]+'&';			
					});
				
					var fio = jQuery('.confirm .fio_new_user').attr('value');
					var email = jQuery('.confirm .email_new_user').attr('value');
					
					var dataString = postdata+'action=confirm_order_recall&count='+i+'&action=add_new_user_in_order&fio_new_user='+fio+'&email_new_user='+email".$reg_request.$reg_radio.";

					jQuery.ajax({
						type: 'POST',
						data: dataString,
						dataType: 'json',
						url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',
						success: function(data){
							if(data['int']==100){				
								jQuery('#regnewuser').html(data['recall']);
								jQuery('#regnewuser').slideDown(1500);
								if(data['redirect']!=0){
									location.replace(data['redirect']);
								}else{
									jQuery('.form-table').remove();
									jQuery('.add_new_user_in_order').remove();
								}
							} else {
								jQuery('#regnewuser').html(data['recall']);
								jQuery('#regnewuser').slideDown(1500).delay(5000).slideUp(1500);
							}
						} 
					});	  	
					return false;
				});
			});
			</script>";
		}
	} 
	
	return $script.$basket;
}
add_shortcode('basket', 'shortcode_page_basket');

add_shortcode('productlist','short_product_list');

function short_product_list($atts, $content = null){
	global $post,$wpdb;
	global $rmag_options;
	//print_r($rmag_options);
	$content = do_shortcode( shortcode_unautop( $content ) );
	if ( '</p>' == substr( $content, 0, 4 )
	and '<p>' == substr( $content, strlen( $content ) - 3 ) )
	$content = substr( $content, 4, strlen( $content ) - 7 );
			
	extract(shortcode_atts(array(
	'num' => false,
	'inpage' => 10,
	'type' => 'list',
	'inline' => 3,
	'cat' => false,
	'desc'=> 200,
	'tag'=> false,
	'include' => false,
	'orderby'=> 'post_date',
    'order'=> 'DESC'
	),
	$atts));

	if(!$num){ 
		$count_prod = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_type='products' AND post_status='publish'");	
	}else{
		$inpage = $num;
	}
	
	$rclnavi = new RCL_navi($inpage,$count_prod,'&filter='.$orderby);

	if($cat) 
	$args = array(
	'numberposts'     => $inpage,
	'offset'          => $rclnavi->offset,
    'orderby'         => $orderby,
    'order'           => $order,
    'post_type'       => 'products',
	'tag'			  => $tag,
	'include'         => $include,
	'tax_query' 	  => array(
							array(
								'taxonomy'=>'prodcat',
								'field'=>'id',
								'terms'=> explode(',',$cat)
								)
							)  
	);
	else
		$args = array(
		'numberposts'     => $inpage,
		'offset'          => $rclnavi->offset,
		'category'        => '',
		'orderby'         => $orderby,
		'order'           => $order,
		'include'         => $include,
		'tag'			  => $tag,
		'exclude'         => '',
		'meta_key'        => '',
		'meta_value'      => '',
		'post_type'       => 'products',
		'post_mime_type'  => '',
		'post_parent'     => '',
		'post_status'     => 'publish'	  
		);

	$products = get_posts($args);

	$n=0;
	
	if($type=='slab') $prodlist .='<div class="prodlist">';
		else  $prodlist .='<table class="prodlist">';
	
		foreach((array)$products as $product){
			$n++;
			$thumbnail_id = get_post_thumbnail_id($product->ID);
			$large_image_url = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail');
			$price = get_post_meta($product->ID, 'price-products', 1);
			$post_content = strip_tags($product->post_content);
			if(strlen($post_content) > $desc){
				$post_content = substr($post_content, 0, $desc);
				$post_content = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $post_content);
			}
			if($type=='slab'){	
				$prodlist .='<div class="prod-single slab-list">';
				$prodlist .='<a href="'.get_permalink($product->ID).'"><h3 class="title-prod">'.$product->post_title.'</h3></a>';
				$prodlist .='<div class="thumb-prod"><img src="'.$large_image_url[0].'"></div>';				
				$prodlist .='<p class="desc-prod">'.$post_content.'</p>';
				if($price) $prodlist .='<h4 class="price-prod">Цена: '.$price.' руб.</h4>';
				else $prodlist .='<h4 style="color:red;text-transform: uppercase;" class="price-prod">Бесплатно!</h4>';
				if($rmag_options['products_warehouse_recall']!=1&&$price!==''||get_post_meta($product->ID, 'availability_product', 1)!='empty'&&$price!==''){
					$amount = get_post_meta($product->ID, 'amount_product', 1);
					if($amount>0||$amount==false) $prodlist .='<input id="'.$product->ID.'" type="button" class="recall-button add_basket add_to_cart" value="В корзину">';
				}else{
					$prodlist .='<div class="false-button"></div>';
				}
				$prodlist .='</div>';
				$cnt = $n%$inline;
				if($cnt==0) $prodlist .='<div class="clear"></div>';
			}
			
			if($type=='list'){	
				$prodlist .='<tr class="prod-single list-list">';				
				$prodlist .='<td class="thumb-prod" width="110"><img width="100" src="'.$large_image_url[0].'"></td>';
				$prodlist .='<td><a href="'.get_permalink($product->ID).'"><h3 class="title-prod">'.$product->post_title.'</h3></a>';								
				$prodlist .='<p class="desc-prod">'.$post_content.'</p>';
				if($price) $prodlist .='<h4 class="price-prod">Цена: '.$price.' руб.</h4>';
				else $prodlist .='<h4 style="color:red;text-transform: uppercase;" class="price-prod">Бесплатно!</h4>';
				if($rmag_options['products_warehouse_recall']!=1&&$price!==''||get_post_meta($product->ID, 'availability_product', 1)!='empty'&&$price!==''){
					$amount = get_post_meta($product->ID, 'amount_product', 1);
					if($amount>0||$amount==false) $prodlist .='<td><input id="'.$product->ID.'" type="button" class="recall-button add_basket add_to_cart" value="В корзину"></td>';
				}
				$prodlist .='</tr>';
			}
			if($type=='rows'){
				 if($n%2) $prodlist .='<tr class="prod-single rows-list parne">';
						else  $prodlist .='<tr class="prod-single rows-list">';	
				$prodlist .='<td><a href="'.get_permalink($product->ID).'"><h3 class="title-prod">'.$product->post_title.'</h3></a></td>';								
				$prodlist .='<td><h4 class="price-prod">Цена: '.$price.' руб.</h4></td>';
				if($rmag_options['products_warehouse_recall']!=1||get_post_meta($product->ID, 'availability_product', 1)!='empty'){
				$amount = get_post_meta($product->ID, 'amount_product', 1);
					if($amount>0||$amount==false) $prodlist .='<td><input id="'.$product->ID.'" type="button" class="recall-button add_basket add_to_cart" value="В корзину"></td>';
				}
				$prodlist .='</tr>';
			}			
		}
			
		if($type=='slab') $prodlist .='</div>'; 
			else 
			$prodlist .='</table>';
		
		
	if(!$num) $prodlist .= $rclnavi->navi();
	
	return $prodlist;
	
	wp_reset_query();
}

add_shortcode('pricelist', 'shortcode_pricelist_recall');
function shortcode_pricelist_recall($atts, $content = null){
	global $post;
	$content = do_shortcode( shortcode_unautop( $content ) );
	if ( '</p>' == substr( $content, 0, 4 )
	and '<p>' == substr( $content, strlen( $content ) - 3 ) )
	$content = substr( $content, 4, strlen( $content ) - 7 );
	
	extract(shortcode_atts(array(
	'catslug' => '',
	'tagslug'=> '',
	'catorder'=>'id',
	'prodorder'=>'post_date'
	),
	$atts));		
	
	if($catslug) 
	$args = array(
	'numberposts'     => -1,
    'orderby'         => $prodorder,
    'order'           => '',
    'post_type'       => 'products',
	'tag'			  => $tagslug,
	'include'         => $include,
	'tax_query' 	  => array(
							array(
								'taxonomy'=>'prodcat',
								'field'=>'slug',
								'terms'=> $catslug
								)
							)  
	);
	else
	$args = array(
    'numberposts'     => -1,
    'orderby'         => $prodorder,
    'order'           => '',
	'tag'			  => $tagslug,
    'exclude'         => '',
    'meta_key'        => '',
    'meta_value'      => '',
    'post_type'       => 'products',
    'post_mime_type'  => '',
    'post_parent'     => '',
    'post_status'     => 'publish'	  
	);
	
	$products = get_posts($args);
	
	$catargs = array(   
		'orderby'      => $catorder  
		,'order'        => 'ASC'  
		,'hide_empty'   => true    
		,'slug'         => $catslug
		,'hierarchical' => false   
		,'pad_counts'   => false  
		,'get'          => ''  
		,'child_of'     => 0  
		,'parent'       => ''  
	);  
	  
	$prodcats = get_terms('prodcat', $catargs);
	
		$n=0;
		
		$pricelist ='<table class="pricelist">
			<tr><td>№</td><td>Наименование товара</td><td>Метка товара</td><td>Цена</td></tr>';
		foreach((array)$prodcats as $prodcat){
		
			$pricelist .='<tr><td colspan="4" align="center"><b>'.$prodcat->name.'</b></td></tr>';
			
			foreach((array)$products as $product){
			
				if( has_term($prodcat->term_id, 'prodcat', $product->ID)){
				
				$n++;
				
				if( has_term( '', 'post_tag', $product->ID ) ){
					$tags = get_the_terms( $product->ID, 'post_tag' );  
					foreach((array)$tags as $tag){  
						$tags_prod .= $tag->name;  
					} 
				}
				
				$pricelist .='<tr>';				
				$pricelist .='<td>'.$n.'</td>';
				$pricelist .='<td><a target="_blank" href="'.get_permalink($product->ID).'">'.$product->post_title.'</a>';
				$pricelist .='<td>'.$tags_prod.'</td>';	
				$pricelist .='<td>'.get_post_meta($product->ID, 'price-products', 1).' руб</td>';				
				$pricelist .='</tr>';
				
				}
				unset ($tags_prod);
			}
			
			$n=0;
			
		}	
			
			$pricelist .='</table>';
				
	return $pricelist;
	
}

add_shortcode('slider-products','slider_products_rcl');
function slider_products_rcl($atts, $content = null){
	
	extract(shortcode_atts(array(
	'num' => 5,
	'cat' => '',
	'exclude' => $exclude,
	'orderby'=> 'post_date',
	'title'=> '',
	'desc'=> 280,
    'order'=> 'DESC'
	),
	$atts));
	
	
	
	if($cat) 
	$args = array(
	'numberposts'     => $num,
    'orderby'         => $orderby,
    'order'           => $order,
	'exclude'         => $exclude,
    'post_type'       => 'products',
	'tax_query' 	  => array(
							array(
								'taxonomy'=>'prodcat',
								'field'=>'id',
								'terms'=> array($cat)
								)
							)  
	);	
	else
	$args = array(
    'numberposts'     => $num,
    'orderby'         => $orderby,
    'order'           => $order,
    'exclude'         => $exclude,
    'post_type'       => 'products',
    'post_status'     => 'publish'
	);
	
	$posts = get_posts($args);
	
	$plslider .= '<ul class="slider-products">';
	foreach($posts as $post){ 	
		if( has_post_thumbnail($post->ID)){ 
			$thumb_id = get_post_thumbnail_id($post->ID);
			$large_url = wp_get_attachment_image_src( $thumb_id, 'full');
			$plslider .= '<li><a href="'.get_permalink($post->ID).'">';
			$plslider .= '<img src='.$large_url[0].'>';
			$post_content = $post->post_content;
			if($desc > 0 && strlen($post_content) > $desc){
				$post_content = strip_tags(substr($post_content, 0, $desc));
				$post_content = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $post_content);
			}
			$plslider .= '<div class="content-slide">';
			if($title!='no') $plslider .= '<h3>'.$post->post_title.'</h3>';
			if($desc > 0 )$plslider .= '<p>'.$post_content.'</p>';
			$plslider .= '</div>';
			$plslider .= '</a></li>';
	    } 
	}	
	$plslider .= '</ul>';

	return $plslider;
}
?>