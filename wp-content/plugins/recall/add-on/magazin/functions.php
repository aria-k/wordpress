<?phpif (is_admin()):	add_action('admin_head','output_script_admin_recall');	endif;add_filter('wp_enqueue_scripts','script_recall_magazine');function script_recall_magazine(){	wp_enqueue_style( 'rmag-recall', plugins_url('style.css', __FILE__) );}function output_script_admin_recall(){	wp_enqueue_script( 'jquery' );			wp_enqueue_script( 'ajax_admin_magazine_recall', plugins_url('js/admin.js', __FILE__) );		}function attachments_products( $attachments ){	$args = array(		'label' => 'Галлерея товара',		'post_type' => array( 'products' ),		'filetype' => null,		'note' => null,		'button_text' => __( 'Прикрепить изображение или загрузить его', 'attachments' ),		'modal_text' => __( 'Прикрепить изображение или загрузить его', 'attachments' ),		'fields' => array(		) 	);	 	$attachments->register( 'attachments_products', $args );} add_action( 'attachments_register', 'attachments_products' );add_action( 'init', 'register_terms_rec_products' );function register_terms_rec_products(){    $labels = array(         'name' => _x( 'Каталог товаров', 'products' ),        'singular_name' => _x( 'Каталог товаров', 'products' ),        'add_new' => _x( 'Добавить товар', 'products' ),        'add_new_item' => _x( 'Добавить новый товар', 'products' ),        'edit_item' => _x( 'Редактировать', 'products' ),        'new_item' => _x( 'Новое', 'products' ),        'view_item' => _x( 'Просмотр', 'products' ),        'search_items' => _x( 'Поиск', 'products' ),        'not_found' => _x( 'Не найдено', 'products' ),        'not_found_in_trash' => _x( 'Корзина пуста', 'products' ),        'parent_item_colon' => _x( 'Родительский товар', 'products' ),        'menu_name' => _x( 'Товары', 'products' )    );    $args = array(         'labels' => $labels,        'hierarchical' => false,                'supports' => array( 'title', 'editor','custom-fields','thumbnail','comments'),        'taxonomies' => array( 'prodcat','post_tag' ),        'public' => true,        'show_ui' => true,        'show_in_menu' => true,        'menu_position' => 10,        'show_in_nav_menus' => true,        'publicly_queryable' => true,        'exclude_from_search' => false,        'has_archive' => true,        'query_var' => true,        'can_export' => true,        'rewrite' => true,        'capability_type' => 'post'    );    register_post_type( 'products', $args );}add_action( 'init', 'register_taxonomy_prodcat' );function register_taxonomy_prodcat() {    $labels = array(           'name' => _x( 'Категории', 'prodcat' ),        'singular_name' => _x( 'Категории', 'prodcat' ),        'search_items' => _x( 'Поиск', 'prodcat' ),        'popular_items' => _x( 'Популярные категории', 'prodcat' ),        'all_items' => _x( 'Все категории', 'prodcat' ),        'parent_item' => _x( 'Родительская категория', 'prodcat' ),        'parent_item_colon' => _x( 'Родительская категория:', 'prodcat' ),        'edit_item' => _x( 'Редактировать категорию', 'prodcat' ),        'update_item' => _x( 'Обновить категорию', 'prodcat' ),        'add_new_item' => _x( 'Добавить новую категорию', 'prodcat' ),        'new_item_name' => _x( 'Новая категория', 'prodcat' ),        'separate_items_with_commas' => _x( 'Separate страна with commas', 'prodcat' ),        'add_or_remove_items' => _x( 'Добавить или удалить категорию', 'prodcat' ),        'choose_from_most_used' => _x( 'Выберите для использования', 'prodcat' ),        'menu_name' => _x( 'Категории', 'prodcat' )    );    $args = array(         'labels' => $labels,        'public' => true,        'show_in_nav_menus' => true,        'show_ui' => true,        'show_tagcloud' => true,        'hierarchical' => true,        'rewrite' => true,        'query_var' => true    );    register_taxonomy( 'prodcat', array('products'), $args );}// создаем колонку товарных категорийadd_filter('manage_edit-products_columns', 'add_prodcat_column', 10, 1);  function add_prodcat_column( $columns ){      $columns['prodcat'] = 'Категория';     return $columns;  }    // заполняем колонку данными  add_filter('manage_products_posts_custom_column', 'fill_prodcat_column', 5, 2);function fill_prodcat_column($column_name, $post_id) {      if( $column_name != 'prodcat' )          return;        $cur_terms = get_the_terms( $post_id, 'prodcat' );  		foreach((array)$cur_terms as $cur_term){  			echo '<a href="./edit.php?post_type=products&prodcat='. $cur_term->slug .'">'. $cur_term->name .'</a><br />'  ;		}   }// добавляем возможность сортировать колонку  add_filter('manage_edit-products_sortable_columns', 'add_price_sortable_column');  function add_price_sortable_column($sortable_columns){          $sortable_columns['prodcat'] = 'prodcat_prodcat';                return $sortable_columns;  }// создаем колонку ценыadd_filter('manage_edit-products_columns', 'add_price_column', 10, 1);  function add_price_column( $columns ){   	$out = array();      foreach((array)$columns as $col=>$name){          if(++$i==3)               $out['price'] = 'Цена';          $out[$col] = $name;      }       return $out;     }    // заполняем колонку цены  add_filter('manage_products_posts_custom_column', 'fill_price_column', 5, 2); // wp-admin/includes/class-wp-posts-list-table.php  function fill_price_column($column_name, $post_id) {      if( $column_name != 'price' )          return;     		echo get_post_meta($post_id,'price-products',1).' <input type="text" name="priceprod[]" size="4" value=""><input type="hidden" name="product[]" value="'.$post_id.'"> рублей' ;  }// создаем колонку наличия товараadd_filter('manage_edit-products_columns', 'add_availability_column', 10, 1);  function add_availability_column( $columns ){	global $rmag_options;	if($rmag_options['products_warehouse_recall']!=1) return $columns;		$out = array();  		foreach((array)$columns as $col=>$name){  			if(++$i==3)  				 $out['availability'] = 'Наличие';  			$out[$col] = $name;  		}   		return $out;   	  }  	  // заполняем колонку наличия товара  add_filter('manage_products_posts_custom_column', 'fill_availability_column', 5, 2); function fill_availability_column($column_name, $post_id) {	global $rmag_options;	if($rmag_options['products_warehouse_recall']!=1) return $column_name;		if( $column_name != 'availability' )  			return; 					if(get_post_meta($post_id, 'availability_product', 1)!='empty'){			$amount = get_post_meta($post_id,'amount_product',1);			$reserve = get_post_meta($post_id,'reserve_product',1);						if($amount<=0&&$amount!='') echo '<span style="color:red;">в наличии</span> ';			else echo '<span style="color:green;">в наличии</span> ';			if($amount!='') $form_amount = '<input type="text" name="amountprod[]" size="3" value=""> шт.';				else $form_amount = false;			if($amount!=false&&$amount>0) echo '<span style="color:green;">'.$amount.'</span> '.$form_amount;				else if($amount<=0) echo '<span style="color:red;">'.$amount.'</span> '.$form_amount;			if($reserve) echo '<br /><span style="color:orange;">в резерве '.$reserve.'</span>';					}else{			echo '<span style="color:red;">не в наличии</span>';		}}// создаем колонку миниатюрadd_filter('manage_edit-products_columns', 'add_thumb_column', 10, 1);  function add_thumb_column( $columns ){   	$out = array();      foreach((array)$columns as $col=>$name){          if(++$i==3)               $out['thumb'] = 'Миниатюра';          $out[$col] = $name;      }       return $out;     }    // заполняем колонку миниатюр  add_filter('manage_products_posts_custom_column', 'fill_thumb_column', 5, 2); // wp-admin/includes/class-wp-posts-list-table.php  function fill_thumb_column($column_name, $post_id) {      if( $column_name != 'thumb' )          return;     		if(get_the_post_thumbnail($post_id,'thumbnail')) echo get_the_post_thumbnail($post_id,array(50,50)) ;  } add_action('admin_init', 'recall_products_fields', 1);function recall_products_fields() {    add_meta_box( 'products_fields', 'Настройки товара', 'recall_products_fields_box', 'products', 'normal', 'high'  );}function recall_products_fields_box( $post ){global $rmag_options;?>	<p><label><input type="text" name="wprecall[price-products]" value="<?php echo get_post_meta($post->ID, 'price-products', 1); ?>" style="width:20%" /> < Цена товара</label></p><?php	$customprice = unserialize(get_post_meta($post->ID, 'custom-price', 1));	if($customprice){		$cnt = count($customprice);		for($a=0;$a<$cnt;$a++){			$price .= '<p id="custom-price-'.$a.'">Заголовок: <input type="text" class="title-custom-price" name="title-custom-price[]" value="'.$customprice[$a]['title'].'"> Цена: <input type="text" class="custom-price" name="custom-price[]" value="'.$customprice[$a]['price'].'"> <a href="#" class="delete-price" id="'.$a.'">удалить</a></p>';		}	}			//echo '<div id="custom-price-list">'.$price.'</div>	//<input type="button" id="add-custom-price" class="button-secondary" value="Добавить еще цену">';			if($rmag_options['products_warehouse_recall']==1){ ?>			<h4>Наличие товара: <?php $mark_v = get_post_meta($post->ID, 'availability_product', 1); ?></h4>		 <p><label><input type="radio" name="wprecall[availability_product]" value="" <?php checked( $mark_v, '' ); ?>/> в наличии</label> 		 <input type="text" name="wprecall[amount_product]" value="<?php echo get_post_meta($post->ID, 'amount_product', 1); ?>" size="4"/> шт.</p>		 <p><label><input type="radio" name="wprecall[availability_product]" value="empty" <?php checked( $mark_v, 'empty' ); ?> /> нет в наличии или цифровой товар</label></p>		<?php } ?>	<?php	if($rmag_options['sistem_related_products']==1){ 	echo '<h3>Похожие или рекомендуемые товары:</h3>';	$args = array(  		'show_option_all'    => '',  		'show_option_none'   => '',  		'orderby'            => 'ID',  		'order'              => 'ASC',  		'show_last_update'   => 0,  		'show_count'         => 0,  		'hide_empty'         => 0,  		'child_of'           => 0,  		'exclude'            => '',  		'echo'               => 0,  		'selected'           => get_post_meta($post->ID, 'related_products_recall', 1),  		'hierarchical'       => 0,  		'name'               => 'wprecall[related_products_recall]',  		'id'                 => 'name',  		'class'              => 'postform',  		'depth'              => 0,  		'tab_index'          => 0,  		'taxonomy'           => 'prodcat',  		'hide_if_empty'      => false );   	  	echo '<div style="margin:10px 0;">'.wp_dropdown_categories( $args ).' - выберите товарную категорию</div>';			}			if(!class_exists( 'Attachments' )){	$args = array(      'numberposts' => -1,      'order'=> 'ASC',      'post_mime_type' => 'image',      'post_parent' => $post->ID,      'post_status' => null,      'post_type' => 'attachment'      );   	  	$childrens = get_children( $args ); 		$postmeta = get_post_meta($post->ID, 'children_prodimage', 1);	$value = explode(',',$postmeta);	$count_value = count($value);	$id_thumbnail = get_post_thumbnail_id( $post->ID );		echo '	<style>	.image-prod-gallery{float: left; margin: 3px;} .prod-gallery{overflow:hidden;}	</style>	<h3>Изображения галереи</h3>	<p style="margin:10px;"><a class="button insert-media add_media" title="Добавить медиафайл" data-editor="content" href="#"><span class="wp-media-buttons-icon"></span>Добавить изображение</a></p>	<div class="prod-gallery">';	if( $childrens ){		$n=0;				foreach((array) $childrens as $children ){ 							$n++;											for($a=0;$a<=$count_value;$a++){					if($value[$a]==$children->ID) $selected = ' checked=checked';									}      				echo '<div class="image-prod-gallery"><label><img width="100" src="'.wp_get_attachment_thumb_url( $children->ID ).'" class="current">';				echo '<input style="position: absolute; margin-left: -15px; margin-top: 85px;" type="checkbox" id="imageprod-'.$children->ID.'" name="children_prodimage[]" value="'.$children->ID.'"'.$selected.'></label></div>';												$selected = '';							if($id_thumbnail==$children->ID) $thumb = true;					}		if(!$thumb&&has_post_thumbnail($post->ID)){			for($a=0;$a<=$count_value;$a++){				if($value[$a]==$id_thumbnail) $selected = ' checked=checked';								}			echo '<div class="image-prod-gallery"><label><img width="100" src="'.wp_get_attachment_thumb_url( $id_thumbnail ).'" class="current">';			echo '<input style="position: absolute; margin-left: -15px; margin-top: 85px;" type="checkbox" id="imageprod-'.$id_thumbnail.'" name="children_prodimage[]" value="'.$id_thumbnail.'"'.$selected.'></label></div>';						$selected = '';		}					}else{		if(has_post_thumbnail($post->ID)){			for($a=0;$a<=$count_value;$a++){					if($value[$a]==$id_thumbnail) $selected = ' checked=checked';								}			echo '<div class="image-prod-gallery"><label><img width="100" src="'.wp_get_attachment_thumb_url( $id_thumbnail ).'" class="current">';			echo '<input style="position: absolute; margin-left: -15px; margin-top: 85px;" type="checkbox" id="imageprod-'.$id_thumbnail.'" name="children_prodimage[]" value="'.$id_thumbnail.'"'.$selected.'></label></div>';			$selected = '';		}	}	echo '</div>';	}	?>		<input type="hidden" name="wpm_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" /><?php}add_action('save_post', 'wpm_extra_fields_update');function wpm_extra_fields_update( $post_id ){    if ( !wp_verify_nonce($_POST['wpm_fields_nonce'], __FILE__) ) return false;	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;	if ( !current_user_can('edit_post', $post_id) ) return false;		if(isset($_POST['custom-price'])){		$cnt = count($_POST['custom-price']);		for($a=0;$a<$cnt;$a++){			if($_POST['custom-price']){				$customprice[$a]['title'] .= $_POST['title-custom-price'][$a];				$customprice[$a]['price'] .= $_POST['custom-price'][$a];			}		}		$customprice = serialize($customprice);		update_post_meta($post_id, 'custom-price', $customprice);	}else{		delete_post_meta($post_id, 'custom-price');	}		if( $_POST['children_prodimage']=='' ){		delete_post_meta($post_id, 'children_prodimage');		}else{		$_POST['children_prodimage'] = array_map('trim', (array)$_POST['children_prodimage']);		$n=0;		foreach((array) $_POST['children_prodimage'] as $value ){			$n++;				if($n==1) $children_prodimage = $value;					else $children_prodimage .= ','.$value;			}		update_post_meta($post_id, 'children_prodimage', $children_prodimage);	}	return $post_id;}add_filter('the_content','add_block_related_products',70);function add_block_related_products($content){global $rmag_options;if($rmag_options['sistem_related_products']!=1) return $content;global $post;		if($post->post_type=='products'){			$related_prodcat = get_post_meta($post->ID,'related_products_recall',1);			if($related_prodcat){				$args = array(					'numberposts'     => $rmag_options['size_related_products'],					'orderby'         => 'rand',					'post_type'       => 'products',					'tax_query' 	  => array(											array(												'taxonomy'=>'prodcat',												'field'=>'id',												'terms'=> $related_prodcat												)											)  				);								$related_products = get_posts($args);								$related .= '<div class="related-products prodlist">';				$title_related = $rmag_options['title_related_products_recall'];				if($title_related) $related .= '<h3>'.$title_related.'</h3>';				foreach((array)$related_products as $product){				if($product->ID==$post->ID) continue;					$post_content = strip_tags($product->post_content);					if(strlen($post_content) > 200){						$post_content = substr($post_content, 0, 200);						$post_content = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $post_content);					}					$price = get_post_meta($product->ID, 'price-products', 1);					$thumbnail_id = get_post_thumbnail_id($product->ID);					$large_image_url = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail');					$related .='<div class="prod-single slab-list">';					$related .='<a href="'.get_permalink($product->ID).'"><h3 class="title-prod">'.$product->post_title.'</h3></a>';					$related .='<div class="thumb-prod"><img src="'.$large_image_url[0].'"></div>';									$related .='<p class="desc-prod">'.$post_content.'</p>';					$related .='<h4 class="price-prod">Цена: '.$price.' руб.</h4>';					if($rmag_options['products_warehouse_recall']!=1||get_post_meta($product->ID, 'availability_product', 1)!='empty'){						$amount = get_post_meta($product->ID, 'amount_product', 1);						if($amount>0||$amount==false) $related .='<input id="'.$product->ID.'" type="button" class="recall-button add_basket add_to_cart" value="В корзину">';					}else{						$related .='<div class="false-button"></div>';					}					$related .='</div>';				}				$related .= '</div>';								$content .= $related;			}					}	return $content;}add_filter('the_content','add_gallery_product_recall');function add_gallery_product_recall($content){global $post;	if(get_post_type($post->ID)=='products'){		if(get_post_meta($post->ID, 'recall_slider', 1)!=1||!is_single()) return $content;		if(!class_exists( 'Attachments' )){		$postmeta = get_post_meta($post->ID, 'children_prodimage', 1);					if($postmeta){						$values = explode(',',$postmeta);											$gallery = '<div class="rcl-gallery">					<ul class="bxslider">';				foreach((array) $values as $children ){					$large = wp_get_attachment_image_src( $children, 'large' );					$gallery .= '<li><a class="fancybox" href="'.$large[0].'"><img src="'.$large[0].'"></a></li>';					$thumbs[] = $large[0];				}				$gallery .= '</ul>				</div>';				if(count($thumbs)>1){					$gallery .= '<div id="bx-pager">';						foreach($thumbs as $k=>$src ){							$gallery .= '<a data-slide-index="'.$k.'" href=""><img src="'.$src.'" /></a>';						}					$gallery .= '</div>';				}			}			return $gallery.$content;		}else{			$attachments = new Attachments( 'attachments_products' );						if( $attachments->exist() ) :				$num=0;				$gallery = '<div class="rcl-gallery">						<ul class="bxslider">';			while( $attachments->get() ) :				$num++;				$large = wp_get_attachment_image_src( $children, 'large' );				$gallery .= '<li><a class="fancybox" href="'.$attachments->src( 'full' ).'"><img src="'.$attachments->src( 'thumbnail' ).'"></a></li>';				$thumbs[] = $large[0];					endwhile;				$gallery .= '</ul>					</div>';								$gallery .= '<div id="bx-pager">';					foreach($thumbs as $k=>$src ){						$gallery .= '<a data-slide-index="'.$k.'" href=""><img src="'.$src.'" /></a>';					}				$gallery .= '</div>';			endif;			return $gallery.$content;		}	} else {		return $content;	}}add_filter('tag_link','add_post_type_link_tags');function add_post_type_link_tags($link){global $post; 		if($post->post_type=='products') return $link.'?post_type='.$post->post_type;			return $link;}//Выводим кнопку корзины в кратком содержанииadd_filter('the_excerpt', 'excerpt_product_basket');function excerpt_product_basket($excerpt){	global $post;	global $user_ID;	global $rmag_options;	if($post->post_type=='products'){		if(get_post_meta($post->ID,'price-products',1)){			$price = '<div class="price-basket-product" style="text-align:right;">Цена: '.get_post_meta($post->ID,'price-products',1).' руб.'; 			if($rmag_options['products_warehouse_recall']!=1||get_post_meta($post->ID, 'availability_product', 1)!='empty') 				$amount = get_post_meta($post->ID, 'amount_product', 1);				if($amount>0||$amount==false) $price .= '<input type="button" class="recall-button add_basket" id="'.$post->ID.'" value="Добавить в корзину"></div>';		}	$excerpt = $excerpt.$price;		}		return $excerpt;	}//Выводим категорию товараfunction add_wpm_product_meta($content){global $post;	$product_cat = get_the_term_list( $post->ID, 'prodcat', '<p class="product-cat"><b>Категория товара:</b> ', ',', '</p>' );	return $product_cat.$content;}add_filter('the_content','add_wpm_product_meta');//формируем таблицу содержимого заказа для письмаfunction get_email_table_order_rcl($order_data,$inv_id,$sumprise){		$table_order .= '<table class="order-form"><tr><td><b>№ п/п</b></td><td><b>Наименование товара</b></td><td><b>Цена</b></td><td><b>Количество</b></td><td><b>Сумма</b></td><td><b>Статус</b></td></tr>';	foreach((array)$order_data as $order){							switch($order->status){			case 1: $status = 'Не оплачен'; break;			case 2: $status = 'Оплачен'; break;			case 3: $status = 'Отправлен'; break;			case 4: $status = 'Получен'; break;			case 5: $status = 'Закрыт'; break;			case 6: $status = 'Корзина'; break;		}			if($order->inv_id==$inv_id){			$n++;				//$user_login = get_the_author_meta('user_login',$order->user);				$table_order .= '<tr align="center"><td>'.$n.'</td><td>'.get_the_title($order->product).'</td><td>'.$order->price.'</td><td>'.$order->count.'</td><td>'.$order->price*$order->count.'</td><td>'.$status.'</td></tr>';									}	}	$table_order .= '<tr><td colspan="4">Сумма заказа</td><td colspan="2">'.$sumprise.'</td></tr></table>';	return $table_order;}?>