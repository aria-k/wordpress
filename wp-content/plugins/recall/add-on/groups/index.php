<?php
add_filter('buttons_edit_post_rcl','add_author_edit_func_postgroup_rcl',20,2);
function add_author_edit_func_postgroup_rcl($form_button,$post){
global $user_ID,$post;
	if($user_ID&&!$form_button){
		if($post->post_type == 'post-group') $post_terms = get_the_terms( $post->ID, 'groups' );
		if($post_terms){
			foreach((array)$post_terms as $term){
				$term_id = $term->term_id;
			}
		}
		if($term_id == get_the_author_meta('admin_group_'.$term_id,$user_ID)){
			$form_button = "<div class='post-edit-button'>					
				<input id='delete-post' type='image' name='delete_post' src='".get_bloginfo('wpurl')."/wp-content/plugins/recall/img/delete.png' value='".$post->ID."'></div>
				<div class='post-edit-button'>					
				<input type='image' id='edit-post' name='update_post' src='".get_bloginfo('wpurl')."/wp-content/plugins/recall/img/redactor.png' value='".$post->ID."'></div>";
		}
	}
	return $form_button;
}

add_action( 'init', 'register_terms_rec_post_group' );
function register_terms_rec_post_group() {

	$labels = array( 
			'name' => 'Записи групп',
			'singular_name' => 'Записи групп',
			'add_new' => 'Добавить запись',
			'add_new_item' => 'Добавить новую запись',
			'edit_item' => 'Редактировать',
			'new_item' => 'Новое',
			'view_item' => 'Просмотр',
			'search_items' => 'Поиск',
			'not_found' => 'Не найдено',
			'not_found_in_trash' => 'Корзина пуста',
			'parent_item_colon' => 'Родительская запись',
			'menu_name' => 'Записи групп',
	);

	$args = array( 
		'labels' => $labels,
		'hierarchical' => false,        
		'supports' => array( 'title', 'editor','custom-fields', 'comments', 'thumbnail'),
		'taxonomies' => array( 'groups','post_tag' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 10,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post'
	);

	register_post_type( 'post-group', $args );
}
add_action( 'init', 'register_taxonomy_groups' );

function register_taxonomy_groups() {

	$labels = array( 
		 'name' => 'Группы',
		'singular_name' => 'Группы',
		'search_items' => 'Поиск',
		'popular_items' => 'Популярные Группы',
		'all_items' => 'Все категории',
		'parent_item' => 'Родительская группа',
		'parent_item_colon' => 'Родительская группа:',
		'edit_item' => 'Редактировать группу',
		'update_item' => 'Обновить группу',
		'add_new_item' => 'Добавить новую группу',
		'new_item_name' => 'Новая группа',
		'separate_items_with_commas' => 'Separate страна with commas',
		'add_or_remove_items' => 'Добавить или удалить группу',
		'choose_from_most_used' => 'Выберите для использования',
		'menu_name' => 'Группы',
	);

	$args = array( 
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,

		'rewrite' => true,
		'query_var' => true
	);

	register_taxonomy( 'groups', array('post-group'), $args );
}

add_action('init','init_can_public_groups_rcl');
function init_can_public_groups_rcl(){
	global $rcl_options,$user_ID;
	
	$group_can_public = $rcl_options['public_group_access_recall'];
	if($group_can_public){
		$userdata = get_userdata( $user_ID );
		if($userdata->user_level>=$group_can_public){
			$public_groups = true;
		}else{
			$public_groups = false;
		}
	}else{
		$public_groups = true;
	}
	
	if($public_groups){
		add_filter('posts_button_rcl','add_post_group_button_public_rcl',30,2);
		add_filter('posts_block_rcl','add_post_group_block_public_rcl',30,2);
	}
}

function add_post_group_button_public_rcl($button,$author_lk){
	global $user_ID;
	if(!$button) $status = 'active';	
	$button .= ' <a href="#" id="posts_group" class="child_block_button '.$status.'">Записи групп</a> ';
	return $button;
}

function add_post_group_block_public_rcl($posts_block,$author_lk){
	global $user_ID,$wpdb,$rcl_options;
	if(!$posts_block) $status = 'active';
	$posts_block .= '<div class="posts_group_block recall_child_content_block '.$status.'">';
	
	$posts_block .= get_postslist_rcl('post-group','Записи групп',$author_lk);

	$posts_block .= '</div>';
	return $posts_block;
}

add_filter('admin_options_wprecall','get_admin_groups_page_content');
function get_admin_groups_page_content($content){
	global $rcl_options;
	$content .='<h2>Настройки групп</h2>
	<div id="options-'.get_key_addon_rcl(pathinfo(__FILE__)).'" class="wrap-recall-options">		
			<div class="option-block">
				<h3>Группы</h3>
				<label>Название вкладки в ЛК</label>';
				if(!$rcl_options['tab_group']) $rcl_options['tab_group'] = 'Группы';
				$content .='<input type="text" name="tab_group" value="'.$rcl_options['tab_group'].'" size="10">
				<small>Впишите свою надпись на кнопке переключения вкладки в личном кабинете</small>
				<label>Создание групп разрешено</label>';
				$access_recall = $rcl_options['public_group_access_recall'];
				$content .= '<select name="public_group_access_recall" size="1">
					<option value="">Всем пользователям</option>
					<option value="10" '.selected($access_recall,10,false).'>Администраторам</option>
					<option value="7" '.selected($access_recall,7,false).'>Редакторам</option>
					<option value="2" '.selected($access_recall,2,false).'>Авторам</option>
					<option value="1" '.selected($access_recall,1,false).'>Участникам</option>
				</select>
				<label>Публикация в группе</label>';
				$public_access = $rcl_options['user_public_access_group'];
				$content .= '
				<select name="user_public_access_group" size="1">		
					<option value="10" '.selected($public_access,10,false).'>Только Администраторам</option>
					<option value="7" '.selected($public_access,7,false).'>Редакторам и старше</option>
					<option value="2" '.selected($public_access,2,false).'>Авторам и старше</option>
					<option value="1" '.selected($public_access,1,false).'>Участникам и старше</option>
					<option value="0" '.selected($public_access,0,false).'>Всем зарегистрированным пользователям</option>
				</select>
				<label>Модерация публикаций в группе</label>
				<select name="moderation_public_group" size="1">
					<option value="">Публиковать сразу</option>
					<option value="1" '.selected($rcl_options['moderation_public_group'],1,false).'>Отправлять на модерацию</option>
				</select>
				<small><b>Если используется модерация:</b> Чтобы пользователь мог видеть свою публикацию до того, как она пройдет модерацию, необходимо, чтобы он имел на сайте права не ниже Автора</small>
			</div>
	</div>';
	return $content;
}

add_filter('the_button_wprecall','get_wprecall_groups_button',8,2);
function get_wprecall_groups_button($button,$author_lk){
	global $rcl_options;
	if(!$button) $status = 'active';
	if(!$rcl_options['tab_group']) $rcl_options['tab_group'] = 'Группы';
	$button .= ' <a href="#" id="groups" class="block_button '.$status.'">'.$rcl_options['tab_group'].'</a> ';
	return $button;
}

add_filter('the_block_wprecall','recall_groups_block',8,2);
function recall_groups_block($block_wprecall, $author_lk){
	
	global $wpdb;
	global $user_ID;
	global $rcl_options;

	$admin_groups = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'admin_group_%' AND user_id = '$author_lk'");
	$user_groups = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'user_group_%' AND user_id = '$author_lk'");//print_r($admin_groups);		
	if($admin_groups){
		$ad_groups .= '<ul class="group-list">';		
		foreach((array)$admin_groups as $ad_group){				 
			$ad_term = get_term($ad_group->meta_value, 'groups');	
			if($ad_term->term_id){
				$ad_groups .= '<li id="list-'.$ad_term->term_id.'">';
				$ad_groups .= '<a href="'.get_term_link((int)$ad_term->term_id,'groups' ).'">'.$ad_term->name. '</a>';
				$ad_groups .= '</li>';
			}
		}		
		$ad_groups .= '</ul>';
	}
		
		
	if($user_groups){
		$us_groups .= '<ul class="group-list">';		
		foreach((array)$user_groups as $group){
				
			$term = get_term($group->meta_value, 'groups');
			if($term->term_id){ 
			$us_groups .= '<li id="list-'.$term->term_id.'"><a href="'. get_term_link( (int)$term->term_id, 'groups' ) .'">'.$term->name.'</a></li>';
			}
		}		
		$us_groups .= '</ul>';
	}
	
	if(!$block_wprecall) $status = 'active';	
	
	$groups_block .= '<div class="groups_block recall_content_block '.$status.'">';
	$group_can_public = $rcl_options['public_group_access_recall'];
	if($group_can_public){
		$userdata = get_userdata( $user_ID );
		if($userdata->user_level>=$group_can_public){
			$public_groups = true;
		}else{
			$public_groups = false;
		}
	}else{
		$public_groups = true;
	}
				
	if($user_ID==$author_lk&&$public_groups) $groups_block .= '<p align="right"><input type="button" class="show_form_add_group recall-button" value="Создать группу"></p><div class="add_new_group"><h3>Создать группу</h3>
	<form action="" method="post" enctype="multipart/form-data">
	<p>Название:</p>
	<input type="text" required maxlength="140" size="30" class="title_groups" name="title_groups" value="">
	<p>Описание группы</p>
	<textarea required name="group_desc" id="group_desc" rows="2" style="width:90%;"></textarea>
	<p>Статус группы:</p>
	<input type="checkbox" class="status_groups" name="status_groups" value="1"> - Приватная группа. Доступ в группу только по одобренной заявке пользователя.
	<p>Аватар группы: <input required type="file" name="image_group" class="field"/></p>
	<p align="right"><input type="submit" class="recall-button" name="addgroups" value="Создать"></p>
	</form></div>';
	if($admin_groups) $groups_block .= '<h3>Созданные группы:</h3>'.$ad_groups;
	if($user_groups) $groups_block .= '<h3>Вступил в группы:</h3>'.$us_groups;
	$groups_block .= '</div>';
	
	$block_wprecall .= $groups_block;
		
	return $block_wprecall;
}

//Удаляем всех пользователей и админа группы и ее аватарку при ее удалении из БД
add_action('delete_term', 'delete_users_group_rcl',10,3);
function delete_users_group_rcl($term, $tt_id, $taxonomy){
	if($taxonomy!='groups') return false;
	global  $wpdb;
	$imade_id = get_option('image_group_'.$tt_id);
	delete_option('image_group_'.$tt_id);
	wp_delete_attachment($imade_id,true);
	$wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key = 'admin_group_".$tt_id."'");
	$wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key = 'user_group_".$tt_id."'");
	$wpdb->query("DELETE FROM ".RCL_PREF."groups_options WHERE group_id = '".$tt_id."'");
}

//add_action('wp_head','delete_remove_groups');
function delete_remove_groups(){
	global $wpdb,$user_ID;
	if($user_ID!=1) return false; 
	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."usermeta WHERE meta_key LIKE 'admin_group_%'");
	//print_r($datas);
	foreach($datas as $data){
		$term = term_exists((int)$data->meta_value,'groups');
		if(!$term){
			//echo 'Delete: '.$data->meta_value.'<br>';
			$imade_id = get_option('image_group_'.$data->meta_value);
			delete_option('image_group_'.$data->meta_value);
			wp_delete_attachment($imade_id,true);
			$wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key = 'admin_group_".$data->meta_value."'");
			$wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key = 'user_group_".$data->meta_value."'");
			$wpdb->query("DELETE FROM ".RCL_PREF."groups_options WHERE group_id = '".$data->meta_value."'");
		}else{
			//echo 'Groups: '.$data->meta_value.'<br>';
		}
	}
}

//Публикация в группе
function public_in_group_recall(){
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	global $user_ID,$rcl_options,$wpdb;

	if(!$user_ID) return false;
	
		$thumb = $_POST['thumb'];
		$post_title = $_POST['post_title'];		
		$post_content = $_POST['post_content'];	
		$term_id = $_POST['term_id'];
		$post_tags = $_POST['post_tags'];
		$gr_tag = $_POST['group-tag'];
		$moderation = $rcl_options['moderation_public_group'];
		if($moderation==1) $post_status = 'pending';
		else $post_status = 'publish';
		
		if($rcl_options['nomoder_rayt']){
			$all_r = get_all_rayt_user(0,$user_ID);
			if($all_r >= $rcl_options['nomoder_rayt']) $post_status = 'publish';
		}

		$my_post = array(
		 'post_title' => "$post_title",
		 'post_content' => "$post_content",
		 'post_status' => $post_status,
		 'post_type' => 'post-group',
		 'tags_input'     => $post_tags,
		 'post_author' => "$user_ID",
		 'tax_input'   => array('groups' => array($term_id))
	  );

	  $id_post = wp_insert_post( $my_post );
	  
	  wp_set_object_terms( $id_post, (int)$term_id, 'groups' );
	  
		if($gr_tag){

			$term = term_exists( $gr_tag, 'groups',$term_id );
			if(!$term){
					$term = wp_insert_term(
					  $gr_tag,
					  'groups',
					  array(
						'description'=> '',
						'slug' => '',
						'parent'=> $term_id
					  )
					);
			}
			wp_set_object_terms( $id_post, array((int)$term['term_id'],(int)$term_id), 'groups' );
		}
	  
	if ($id_post) {
	
		$temp_gal = unserialize(get_the_author_meta('tempgallery',$user_ID));
		if($temp_gal){
			$cnt = count($temp_gal);
			foreach((array)$temp_gal as $key=>$gal){ 
				if($thumb[$gal['ID']]==1) add_post_meta($id_post, '_thumbnail_id', $gal['ID']);	
				wp_update_post( array('ID'=>$gal['ID'],'post_parent'=>$id_post) );
			}
			if($_POST['add-gallery-rcl']==1) add_post_meta($id_post, 'recall_slider', 1);
			delete_usermeta($user_ID,'tempgallery');
			
			if(!$thumb){
				$args = array( 
				'post_parent' => $id_post,
				'post_type'   => 'attachment', 
				'numberposts' => 1,
				'post_status' => 'any',
				'post_mime_type'=> 'image'
				);
				$child = get_children($args);
				if($child){ foreach($child as $ch){add_post_meta($id_post, '_thumbnail_id',$ch->ID);} }
			}
		}
	
	} else {
		wp_die('Error');
	}
	
	if($post_status == 'pending'){
		wp_redirect('/?post_type=post-group&p='.$id_post.'&preview=true');  exit;	
	}else{
		$link = get_permalink($id_post);
		wp_redirect( $link );
	}
			
}

function public_in_group_recall_activate ( ) {
  if ( isset( $_POST['public_in_group_recall'] ) ) {
    add_action( 'wp', 'public_in_group_recall' );
  }
}
add_action('init', 'public_in_group_recall_activate');

function get_link_group_tag_rcl($content){
	global $post;
	if($post->post_type!='post-group') return $content;

	$group_data = get_the_terms( $post->ID, 'groups' );
	
	foreach((array)$group_data as $data){
		if($data->parent==0) $group_id = $data->term_id;
		else $tag = $data;
	}
	
	if(!$tag) return $content;
	
	$cat = '<p>Категория в группе: <a href="'. get_term_link( (int)$group_id, 'groups' ) .'?group-tag='.$tag->slug.'">'. $tag->name .'</a></p>';
	
	return $cat.$content;
}
function init_get_link_group_tag(){
	if(is_single()) add_filter('the_content','get_link_group_tag_rcl',80);
	else add_filter('the_excerpt','get_link_group_tag_rcl',80);
}
add_action('wp','init_get_link_group_tag');

//Создаем новую группу
function add_new_group_recall(){

	global $user_ID,$wpdb;
	$option = array();
	
	if($_POST['status_groups']) $option['private'] = 1;
	
	$args = array(  
		'alias_of'=>''  
		,'description'=>$_POST['group_desc'] 
		,'parent'=>0  
		,'slug'=>''  
	);  
	
	$ret = wp_insert_term( $_POST['title_groups'], 'groups', $args );
	
	foreach((array)$ret as $r){
		if ($ret && !is_wp_error($ret)){
			update_usermeta($user_ID,'admin_group_'.$r, $r);
			$option['admin'] = $user_ID;
		}
		break;
	}
			
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');	
			
	$image = wp_handle_upload( $_FILES['image_group'], array('test_form' => FALSE) );
	if($image['file']){
		$attachment = array(
			'post_mime_type' => $image['type'],
			'post_title' => 'image_group_'.$r,
			'post_content' => $image['url'],		
			'guid' => $image['url'],
			'post_parent' => '',
			'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $image['file'] );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $image['file'] );		
		wp_update_attachment_metadata( $attach_id, $attach_data );	
		$option['avatar'] = $attach_id;
	}

	$option = serialize($option);
		$wpdb->insert(
			RCL_PREF.'groups_options',
			array('group_id'=>$r,'option_value'=>$option)
		);	
	
	wp_redirect(get_term_link( (int)$r, 'groups' )); exit;
	
}

function add_new_group_recall_activate ( ) {
  if ( isset($_POST['addgroups']) ) {
    add_action( 'wp', 'add_new_group_recall' );
  }
}
add_action('init', 'add_new_group_recall_activate');

function chek_access_private_group_posts($query){
	global $wpdb,$user_ID,$post,$wp_query; $groups = false;
	if($query->is_search){	
		/*foreach((array)$wp_query->posts as $k=>$p){
			if($p->post_type=='post-group'){
				//$wp_query->posts[$k]->post_content = close_content_closed_group();
				unset($wp_query->posts[$k]);
			}
		}
		foreach((array)$wp_query->post as $k=>$p){
			if($p->post_type=='post-group'){
				unset($wp_query->post[$k]);
			}
		}
		print_r($wp_query);*/
	}
	if($query->is_tax&&$query->query['groups']){
		$term = get_term_by('slug', $query->query['groups'], 'groups');
		$term_id = $term->term_id; $groups = true;
	}
	if($query->is_single&&$query->query['post_type']=='post-group'&&$query->query['name']){
		if(!$post) $post_id = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name='".$query->query['name']."'");
		else $post_id = $post->ID;
		$cur_terms = get_the_terms( $post_id, 'groups' );		
		foreach((array)$cur_terms as $cur_term){
			if($cur_term->parent!=0) continue;
			$term_id = $cur_term->term_id; break;
		}
		$groups = true;
	}
	if($groups){
		
		if($_GET['group-tag']!=''){
			$query->set( 'groups', $_GET['group-tag'] );
		}
		if($_GET['group-page']!=''){
			 $query->set( 'posts_per_page', 1 );
		}
		
		$options_gr = unserialize($wpdb->get_var("SELECT option_value FROM ".RCL_PREF."groups_options WHERE group_id='".$term_id."'"));
		
		if($options_gr['private']==1){
			
			if($user_ID) $in_group = get_the_author_meta('user_group_'.$term_id,$user_ID);
			if(!$in_group&&$options_gr['admin']!=$user_ID){					
				if($query->is_single){
					add_filter('the_content','close_content_closed_group',999);
					add_filter('comment_text','close_comment_closed_group',999);
					add_filter('comment_form_default_fields','close_comment_default_fields',999);
					add_filter('comment_form_field_comment','close_commentform_closed_group',999);
				}else{ 
					$query->set('post_type', 'groups');
				}
			}
		}
	}
	return $query;	
}
add_action('pre_get_posts','chek_access_private_group_posts');

function close_comment_default_fields(){
	return false;
}
function close_commentform_closed_group(){
	return '<style>.form-submit input[type="submit"]{display:none;}</style>
	<h3 align="center" style="color:red;">Для возможности комментирования вы должны стать членом этой группы</h3>';
}
function close_content_closed_group(){
	return '<h3 align="center" style="color:red;">Доступ к контенту закрыт настройками приватности</h3>';
}
function close_comment_closed_group(){
	return '<p>(сообщение скрыто настройками приватности)</p>';
}

function get_group_globals_rcl(){
	global $wp_query,$wpdb,$group_id,$options_gr;
	$curent_term = get_term_by('slug', $wp_query->query_vars['groups'], 'groups');
	
	if($curent_term->parent!=0) $group_id = $curent_term->parent;
	else $group_id = $curent_term->term_id;
	$ser_opt = $wpdb->get_var("SELECT option_value FROM ".RCL_PREF."groups_options WHERE group_id='$group_id'");
	$options_gr = unserialize($ser_opt);
}
add_action('wp','get_group_globals_rcl',1);

function login_group_request_rcl(){
	global $wpdb,$group_id,$options_gr,$user_ID;
	if(isset($_POST['login_group'])&&$user_ID){
		if( !wp_verify_nonce( $_POST['_wpnonce'], 'login-group-request-rcl' ) ) return false;
		
		if($user_ID) $in_group = get_the_author_meta('user_group_'.$group_id,$user_ID);
		
		$admin_id = $options_gr['admin'];

		if($in_group){ 
				delete_usermeta( $user_ID, 'user_group_'.$group_id );
				$in_group = false;
			}else{
				if($options_gr['private']==1){
				
					$curent_term = get_term_by('ID', $group_id, 'groups');
					$requests = unserialize(get_option('request_group_access_'.$group_id));
					$requests[$user_ID] = get_usermeta($user_ID,'display_name');
					$requests = serialize($requests);
					update_option('request_group_access_'.$group_id,$requests);
					
					add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
					$headers = 'From: '.get_bloginfo('name').' <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
					$subject = 'Запрос на доступ к группе!';					
					$textmail = '
					<p>Вы получили новый запрос на доступ к администрируемой вами группе "'.$curent_term->name.'" на сайте "'.get_bloginfo('name').'".</p>
					<h3>Информация о пользователе:</h3>
					<p><b>Профиль пользователя</b>: <a href="'.get_author_posts_url($user_ID).'">'.get_the_author_meta('display_name',$user_ID).'</a></p>
					<p>Вы можете одобрить или отклонить запрос перейдя по ссылке:</p>  
					<p>'.get_term_link( (int)$group_id, 'groups' ).'</p>';					
					$admin_email = get_the_author_meta('user_email',$admin_id);
					wp_mail($admin_email, $subject, $textmail, $headers);
				
				}else{
					update_usermeta($user_ID,'user_group_'.$group_id, $group_id);	
					$in_group = true;
				}
			}
		wp_redirect(get_term_link((int)$group_id,'groups')); exit;
	}
}
add_action('wp','login_group_request_rcl',20);

function upload_image_group_rcl(){
	global $wpdb,$group_id,$options_gr,$user_ID;
	if(isset($_FILES['image_group'])&&$user_ID){
		$file_name = $_FILES['image_group']['name'];
		$rest = substr($file_name, -4);//получаем расширение файла		
		if($rest=='.png'||$rest=='.jpg'||$rest=='jpeg'||$rest=='.gif'||$rest=='.PNG'||$rest=='.JPG'||$rest=='.JPEG'||$rest=='.GIF'||$rest=='.bmp'){
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');	
			
			if($imade_id){
				wp_delete_post($imade_id,true);
				get_option('image_group_'.$group_id);
			}
			
			$image = wp_handle_upload( $_FILES['image_group'], array('test_form' => FALSE) );
			if($image['file']){
				$attachment = array(
					'post_mime_type' => $image['type'],
					'post_title' => 'image_group_'.$group_id,
					'post_content' => $image['url'],		
					'guid' => $image['url'],
					'post_parent' => '',
					'post_status' => 'inherit'
				);

				$imade_id = wp_insert_attachment( $attachment, $image['file'] );
				$attach_data = wp_generate_attachment_metadata( $imade_id, $image['file'] );		
				wp_update_attachment_metadata( $imade_id, $attach_data );
				
				$options_gr['avatar'] = $imade_id;
				//$options_gr['admin'] = $admin_id;
				
				$options_ser = serialize($options_gr);
				
				$res = $wpdb->update(
					RCL_PREF.'groups_options',
					array('option_value'=>$options_ser),
					array('group_id'=>$group_id)
				);

				if(!$res){
						$wpdb->insert(
						RCL_PREF.'groups_options',
						array('group_id'=>$group_id,'option_value'=>$options_ser)
					);
				}
			}
		}
		wp_redirect(get_term_link((int)$group_id,'groups')); exit;
	}	
}
add_action('wp','upload_image_group_rcl',30);

function add_post_in_group(){
	global $user_ID;

	include('class_group.php');	
	$group = new Rcl_Group();
	
	$group->init_variables();
	$group->get_post_request();
	$group->header_group();
	$group->options_group();

	if($user_ID&&$user_ID==$group->admin_id) $group->admin_block();
	else $group->users_block();
		
	$group->userlist_group();	
	$group->private_title();	
	$group->imagelist_group();	
		
	$group->content_group();	
	
	$group->public_form();			
	$group->footer_group();
}

function init_get_name_group(){
	if(is_single()) add_filter('the_content','add_name_rcl_groups',80);
}
add_action('wp','init_get_name_group');

function get_tags_list_group_rcl($tags,$post_id=null,$first=null){
	if(isset($tags)){
	
		if($post_id){

			$group_data = get_the_terms( $post_id, 'groups' );	
			foreach($group_data as $data){
				if($data->parent==0) $group_id = $data->term_id;
				else $name = $data->name;
			}
			
		}else{
			if(isset($_GET['group-tag'])) $name = $_GET['group-tag'];		
		}

		$tg_lst = '<select name="group-tag">';
		if($first) $tg_lst .= '<option value="">'.$first.'</option>';

		if(!is_object($tags)){
			$ar_tags = explode(',',$tags);
			foreach($ar_tags as $tag){
				$ob_tags[++$i] = new stdClass();
				$ob_tags[$i]->name = trim($tag);
			}
		}else{
			foreach($tags as $tag){
				$ob_tags[++$a] = new stdClass();
				$ob_tags[$a]->name =$tag->name;
				$ob_tags[$a]->slug =$tag->slug;
			}
		}

		foreach($ob_tags as $gr_tag){
			if(!$gr_tag->slug) $slug = $gr_tag->name;
			else $slug = $gr_tag->slug;
			$tg_lst .= '<option '.selected($name,$slug,false).' value="'.$slug.'">'.trim($gr_tag->name).'</option>';
		}
		$tg_lst .= '</select>';
	}
	return $tg_lst;
}

function add_name_rcl_groups($content){
	global $post;
	if(get_post_type( $post->ID )!='post-group') return $content;
	
	$groups = get_the_terms( $post->ID, 'groups' );
	foreach((array)$groups as $group){
		$group_link = '<p>Опубликовано в группе: <a href="'. get_term_link( (int)$group->term_id, 'groups' ) .'">'. $group->name .'</a></p>';
	}
	$content = $group_link.$content;
	return $content;	
}

/*************************************************
Смотрим всех пользователей группы
*************************************************/
function all_users_group_recall(){
	global $wpdb;
	$page = $_POST['page'];
	if(!$_POST['page']) $page = 1;
	include('class_group.php');	
	$group = new Rcl_Group($_POST['id_group']);
	$block_users = '<div class="backform" style="display: block;"></div>
	<div class="float-window-recall" style="display:block;"><p align="right"><input class="recall-button close_edit" type="button" value="Закрыть"></p><div>';
	$block_users .= $group->all_users_group($page);
	$block_users .= '</div></div>
	<script type="text/javascript"> jQuery(function(){ jQuery(".close_edit").click(function(){ jQuery(".group_content").empty(); }); }); </script>';
	$log['recall']=100;
	$log['block_users']=$block_users;
	echo json_encode($log);	
	exit;
}
add_action('wp_ajax_all_users_group_recall', 'all_users_group_recall');
add_action('wp_ajax_nopriv_all_users_group_recall', 'all_users_group_recall');

function request_users_group_access_rcl(){

	$id_group = $_POST['id_group'];
	$id_user = $_POST['id_user'];
	$req = $_POST['req'];
	
	$all_request = unserialize(get_option('request_group_access_'.$id_group));
	$curent_term = get_term_by('id', $id_group, 'groups');
	if($req==1){
		update_usermeta($id_user,'user_group_'.$id_group, $id_group);
		$subject = 'Запрос на доступ к группе одобрен!';	
		$textmail = '
		<h3>Добро пожаловать в группу "'.$curent_term->name.'"!</h3>
		<p>Поздравляем, ваш запрос на доступ к приватной группе на сайте "'.get_bloginfo('name').'" был одобрен.</p>
		<p>Теперь вы можете принимать участие в жизни этой группы как полноценный ее участник.</p>
		<p>Вы можете перейти в группу прямо сейчас, перейдя по ссылке:</p>  
		<p>'.get_term_link( (int)$id_group, 'groups' ).'</p>';
		unset($all_request[$id_user]);
	}
	if($req==2){
		unset($all_request[$id_user]);
		$subject = 'Запрос на доступ к группе отклонен.';
		$textmail = '		
		<p>Сожалеем, но ваш запрос на доступ к приватной группе "'.$curent_term->name.'" на сайте "'.get_bloginfo('name').'" был отклонен ее админом.</p>';
	}
	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	$headers = 'From: '.get_bloginfo('name').' <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";											
	$user_email = get_the_author_meta('user_email',$id_user);
	wp_mail($user_email, $subject, $textmail, $headers);
	
	$all_request = serialize($all_request);
	update_option('request_group_access_'.$id_group,$all_request);
	
	$log['result']=100;
	$log['user']=$id_user;
	echo json_encode($log);
	exit;
}
add_action('wp_ajax_request_users_group_access_rcl', 'request_users_group_access_rcl');

function get_group_by_event($status){
	global $wpdb;
	if(!$status) $like = 'NO';
	$group_ids = $wpdb->results("SELECT * FROM ".RCL_PREF."groups_options WHERE option_value LIKE '%active%'");
	print_r($group_ids);
	foreach($group_ids as $data){
		if(++$a>1) $lst .= ',';
		$lst .= $data->group_id;
	}
	echo $lst;
	$group_data = $wpdb->results("SELECT * FROM ".$wpdb->prefix."terms WHERE term_id ON ($lst)");
	return $group_data;
}

add_shortcode('grouplist','shortcode_grouplist');
function shortcode_grouplist($atts, $content = null){
global $wpdb,$post;
	
	if($_GET['navi']) $navi = $_GET['navi'];
	if(!$navi) $navi=1;
	
	extract(shortcode_atts(array(
		'orderby' => 'id',
		'order' => 'DESC',
		'inpage' => 10
	),
	$atts));
	
	if(!$_GET['event']){
	
		if($_GET['filter']) $orderby = $_GET['filter'];
	
		$args = array(  
			'number'        => 0  
			,'offset'       => 0  
			,'orderby'      => $orderby  
			,'order'        => $order  
			,'hide_empty'   => false  
			,'fields'       => 'all'  
			,'slug'         => ''  
			,'hierarchical' => false  
			,'name__like'   => $_POST['search-group']  
			,'pad_counts'   => false  
			,'get'          => ''  
			,'child_of'     => 0  
			,'parent'       => 0  
		);  
		  
		$groups = get_terms('groups', $args); 
	
	}else{
		$groups = get_group_by_event($_GET['event']);
	}
	
	if($inpage){ 
		$count_group = count($groups);
		$num_page = ceil($count_group/$inpage);
		$max_group_inpage = $inpage*$navi;
	}
	
	$n=0;
	
	$users_groups = $wpdb->get_results("SELECT user_id,meta_key FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'user_group_%' OR meta_key LIKE 'admin_group_%'");
	
	$a = 0;
	foreach((array)$users_groups as $user_gr){
		if(++$a>1) $userslst .= ',';
		$userslst .= $user_gr->user_id;
	}
	
	$display_names = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix."users WHERE ID IN ($userslst)");
	
	foreach((array)$display_names as $name){
		$names[$name->ID] = $name->display_name;
	}
	
	$grouplist .= '<form method="get" action="">
			<p class="alignright">Поиск группы: <input type="text" required name="search-group" value="'.$_POST['search-group'].'">			
			<input type="submit" class="recall-button" value="Найти"><br>
			</p>
			</form>';
			
	$grouplist .= '<p class="alignleft">Фильтровать по: 
		<a '.a_active($_GET['filter'],'id').' href="'.get_permalink($post->ID).'?filter=id">Дате создания</a> 
		<a '.a_active($_GET['filter'],'name').' href="'.get_permalink($post->ID).'?filter=name">По названию</a> 
		<a '.a_active($_GET['filter'],'count').' href="'.get_permalink($post->ID).'?filter=count">Количеству записей</a>
	</p>';
	$grouplist .= '<div class="group-list">';
	
	/*старые аватарки*/
	$images_gr = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'image_group_%'");
	foreach((array)$images_gr as $imag){
		$old_ava[$imag->option_name] = $imag->option_value;
	}
	/**/
	
	$option_gr = $wpdb->get_results("SELECT * FROM ".RCL_PREF."groups_options");
	
	foreach((array)$option_gr as $option){
		$opt_groups[$option->group_id] = $option->option_value;
	}
	
	foreach((array)$groups as $group){
	
	$n++;
		if($n > $max_group_inpage-$inpage){
	
			$users_count = 0;
			
			$options_gr = unserialize($opt_groups[$group->term_id]);
			
			$admin_id = $options_gr['admin'];
			$imade_id = $options_gr['avatar'];
			
			foreach((array)$users_groups as $user){
				if($user->meta_key=="user_group_".$group->term_id) $users_count++;
				if($user->meta_key=="admin_group_".$group->term_id&&!$admin_id) $admin_id = $user->user_id;
			}

			$grouplist .= '<div id="single-group-'.$group->term_id.'" class="group-info">';	

			//if(!$imade_id) $imade_id = get_option('image_group_'.$term_id);
			
			if(!$imade_id) $imade_id = $old_ava['image_group_'.$group->term_id];

			$src = wp_get_attachment_image_src( $imade_id, 'thumbnail');		
			$group_desc = $group->description;
			if(strlen($group_desc) > 300){
				$allowed_html = array(
					'br' => array(),
					'em' => array(),
					'strong' => array()
				); 

				$group_desc = wp_kses(preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $group_desc), $allowed_html);
				$group_desc = mb_substr($group_desc, 0, 300);
				
			}
			if($src[0]) $grouplist .= '<img src="'.$src[0].'" class="avatar_gallery_group">';
			else $grouplist .= '<img src="'.plugins_url('img/empty-avatar.jpg', __FILE__).'" class="avatar_gallery_group">';
			$grouplist .= '<h2 class="groupname-list"><a href="'.get_term_link( (int)$group->term_id,'groups').'">'.$group->name.'</a></h2>
			
			<div class="desc_group_list">'.$group_desc.'</div>
			<div class="author-users">
			<p class="admin-group">Создатель: <a href="'.get_author_posts_url($admin_id).'">'.$names[$admin_id].'</a></p>
			<p class="users-group">Участников: '.$users_count.'</p>
			</div>';	
			
			$grouplist .= '</div>';
			$admin_id = '';
			$imade_id = '';
		}
		if($n==$max_group_inpage) break;
	
	}
	$grouplist .= '</div>';

	$page_navi = navi_rcl($inpage,$count_group,$num_page,'','&filter='.$orderby);
	
	return $grouplist.$page_navi;
	
}

add_filter('file_scripts_rcl','get_scripts_group_rcl');
function get_scripts_group_rcl($script){

	$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";
	$ajaxfile = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-content/plugins/recall/add-on/groups/ajax-request.php',";
				
	$script .= "
		jQuery('.edit .groupname').live('click',function(){				
			var group_name = jQuery(this).text();
			var idgroup = jQuery('.group-info').attr('id');
			var id_group = parseInt(idgroup.replace(/\D+/g,''));
			jQuery(this).attr('class','groupname_edit');
			jQuery(this).html('<input class=\"new-name-group\" type=\"text\" id=\"name-group\" value=\"'+group_name+'\"><input id=\"edit-group-'+id_group+'\" class=\"edit_name_group\" type=\"button\" value=\"Обновить\"><input class=\"cancel_title\" type=\"button\" value=\"Отмена\">');
		});	
		jQuery('.edit .avatar_gallery_group').live('click',function(){				
			jQuery('.edit-avatar').html('<form action=\"\" method=\"post\" enctype=\"multipart/form-data\"><input type=\"file\" name=\"image_group\" class=\"field\"/><input type=\"submit\" name=\"addava\" value=\"Загрузить\"><input class=\"cancel_avatar\" type=\"button\" value=\"Отмена\"></form>');
		});
		jQuery('.edit .desc_group').live('click',function(){
			var desc_group = jQuery(this).text();				
			jQuery(this).attr('class','text_desc_group');
			jQuery(this).html('<textarea name=\"group_desc\" id=\"group_desc\" rows=\"3\" style=\"width:70%;height:150px;\">'+desc_group+'</textarea><input  class=\"edit_desc_group\" type=\"button\" value=\"Обновить\" style=\"float: right; margin-bottom: 15px;\"><input class=\"cancel_desc\" type=\"button\" value=\"Отмена\">');
		});
		jQuery('.cancel_title').live('click',function(){				
			var group_name = jQuery('#name-group').attr('value');
			jQuery('.groupname_edit').html(group_name);
			jQuery('.groupname_edit').attr('class','groupname');
		});
		jQuery('.cancel_avatar').live('click',function(){									
			jQuery('.edit-avatar').empty();
		});
		jQuery('.cancel_desc').live('click',function(){				
			var desc_group = jQuery('#group_desc').attr('value');
			jQuery('.text_desc_group').html('<p>'+desc_group+'</p>');
			jQuery('.text_desc_group').attr('class','desc_group');
		});
		
		jQuery('.show_form_add_group').click(function(){ 
			jQuery('.add_new_group').slideToggle();
		return false; 		
		});

	/* Смотрим всех пользователей группы */
		jQuery('.all-users-group, .float-window-recall .user-navi a').live('click',function(){
			var idgroup = jQuery('.group-info').attr('id');
			var page = parseInt(jQuery(this).text().replace(/\D+/g,''));
			var id_group = parseInt(idgroup.replace(/\D+/g,''));	
			var dataString = 'action=all_users_group_recall&id_group='+ id_group;
			if(page) dataString += '&page='+ page;
			jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['recall']==100){
						jQuery('.group_content').html(data['block_users']).fadeIn();							 
					} else {
						alert('Ошибка!');
					}
				} 
			});	  	
		return false;
		});
	/* Одобряем или отклоняем запрос на вступление в группу */
		jQuery('.request-list .request-access').click(function(){
			var idbutt = jQuery(this).attr('id');
			var id_group = parseInt(idbutt.replace(/\D+/g,''));
			var id_user = parseInt(jQuery(this).parent().parent().attr('id').replace(/\D+/g,''));
			var type_req = 0;
			if(idbutt == 'add-user-req-'+id_group) type_req = 1;
			if(idbutt == 'del-user-req-'+id_group) type_req = 2;
			var dataString = 'action=request_users_group_access_rcl&id_group='+id_group+'&req='+type_req+'&id_user='+id_user;
			jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['result']==100){
						jQuery('#user-req-'+data['user']).remove();							 
					} else {
						alert('Ошибка!');
					}
				} 
			});	  	
		return false;
		});
	/* Редактируем название и описание группы */
		jQuery('.edit_name_group').live('click',function(){	
			var idgroup = jQuery('.group-info').attr('id');
			var id_group = parseInt(idgroup.replace(/\D+/g,''));
			var new_name_group = jQuery('#name-group').attr('value');	
			var dataString = 'action=edit_group_wp_recall&new_name_group='+new_name_group+'&id_group='+id_group+'&user_ID='+user_ID;					
			jQuery.ajax({
			".$ajaxfile."
			success: function(data){
				if(data['int']==100){
						jQuery('.groupname_edit').html(new_name_group);
						jQuery('.groupname_edit').attr('class','groupname');							 
				} else {
					alert(data['res']+'-'+data['group']);
				}
			} 
			});	  	
			return false;
		});
		jQuery('.ban-group').live('click',function(){	
			var user_id = jQuery(this).attr('user-data');
			var group_id = jQuery(this).attr('group-data');	
			var dataString = 'action=group_ban_user_rcl&user_id='+user_id+'&group_id='+group_id+'&user_ID='+user_ID;					
			jQuery.ajax({
			".$ajaxfile."
			success: function(data){
				if(data['int']==100){
					jQuery('#usergroup-'+user_id).replaceWith(data['content']);							 
				} else {
					alert('Ошибка');
				}
			} 
			});	  	
			return false;
		});
		jQuery('.remove-public-group').live('click',function(){	
			var user_id = jQuery(this).attr('user-data');
			var group_id = jQuery(this).attr('group-data');	
			var dataString = 'action=remove_user_publics_group_rcl&user_id='+user_id+'&group_id='+group_id+'&user_ID='+user_ID;					
			jQuery.ajax({
			".$ajaxfile."
			success: function(data){
				if(data['int']==100){
					jQuery('#usergroup-'+user_id).replaceWith(data['content']);							 
				} else {
					alert('Ошибка');
				}
			} 
			});	  	
			return false;
		});
		jQuery('.edit_desc_group').live('click',function(){
			var idgroup = jQuery('.group-info').attr('id');
			var id_group = parseInt(idgroup.replace(/\D+/g,''));
			var new_desc_group = jQuery('#group_desc').attr('value');	
			var dataString = 'action=edit_group_wp_recall&new_desc_group='+new_desc_group+'&id_group='+id_group+'&user_ID='+user_ID;
			jQuery.ajax({
			".$ajaxfile."
			success: function(data){
				if(data['int']==100){
					jQuery('.text_desc_group').html('<p>'+new_desc_group+'</p>');
					jQuery('.text_desc_group').attr('class','desc_group');
				} else {
					alert('Ошибка изменения!');
				}
			} 
			});	  	
				return false;
		});
		jQuery('.posts_group_block .sec_block_button').live('click',function(){
			var btn = jQuery(this);
			get_page_content_rcl(btn,'posts_group_block');
			return false;
		});
	";
	return $script;
}

function get_footer_scripts_groups_rcl($script){
	global $rcl_options;
	if($rcl_options['public_gallery_weight']) $weight = $rcl_options['public_gallery_weight'];
	else $weight = '2';
	
	if($rcl_options['count_image_gallery']) $cnt = $rcl_options['count_image_gallery'];
	else $cnt = '3';

	$script .= "		
	var term_id_group = jQuery('input[name=\"term_id\"]').val();
	jQuery('#postgroupupload').fileapi({
		   url: '".get_bloginfo('wpurl')."/wp-content/plugins/recall/add-on/groups/upload-file.php?id_group='+term_id_group,
		   multiple: true,
		   maxSize: ".$weight." * FileAPI.MB,
		   maxFiles:".$cnt.",
		   clearOnComplete:true,
		   paramName:'uploadfile',
		   accept: 'image/*',
		   elements: {
			  ctrl: { upload: '.js-upload' },
			  empty: { show: '.b-upload__hint' },
			  emptyQueue: { hide: '.js-upload' },
			  list: '.js-files',
			  file: {
				 tpl: '.js-file-tpl',
				 preview: {
					el: '.b-thumb__preview',
					width: 100,
					height: 100
				 },
				 upload: { show: '.progress', hide: '.b-thumb__rotate' },
				 complete: { hide: '.progress' },
				 progress: '.progress .bar'
				}
		   },onSelect: function (evt, data){
				data.all; 
				data.files; 
				if( data.other.length ){				
					var errors = data.other[0].errors;
					if( errors ){
						if(errors.maxSize) alert('Превышен допустимый размер файла.\nОдин файл не более ".$weight."MB');
					}
				}
			},
			onFilePrepare:function(evt, uiEvt){";
				if($cnt){
					$script .= "var num = jQuery('#temp-files li').size();
					if(num>=".$cnt."){
						jQuery('#status-temp').html('<span style=\"color:red;\">Вы уже достигли предела загрузок</span>');
						jQuery('#postgroupupload').fileapi('abort');
					}";
				}
			$script .= "},
			onFileComplete:function(evt, uiEvt){
				var result = uiEvt.result;
				if(result['string']){
					jQuery('#temp-files').append(result['string']);";
					if($cnt){
						$script .= "var num = jQuery('#temp-files li').size();
						if(num>=".$cnt."){
							jQuery('#status-temp').html('<span style=\"color:red;\">Вы уже достигли предела загрузок</span>');
							jQuery('#postgroupupload').fileapi('abort');
						}";
					}
				$script .= "}
			},
			onComplete:function(evt, uiEvt){
				var result = uiEvt.result;
				jQuery('#postgroupupload .js-files').empty();
			}
	});";
	return $script;
}
add_filter('file_footer_scripts_rcl','get_footer_scripts_groups_rcl');
?>