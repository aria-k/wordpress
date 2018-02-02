<?php
require_once("admin-pages.php");
require_once("widget.php");
require_once("functions/ajax-func.php");
require_once("functions/shortcodes.php");
require_once("class_addons.php");
require_once('functions/includes.php');
require_once('functions/navi-rcl.php');

function get_init_filters_actions_rcl(){
	global $rcl_options;
	if (!is_admin()):
		add_action('wp_enqueue_scripts', 'output_style_scripts_recall');
		if(!$rcl_options['login_form_recall']) add_filter('wp_footer', 'register_button_new_user',99);
		
		add_filter('get_comment_author_url', 'add_link_author_in_page');					
		add_action('wp_head','hidden_admin_panel');
			  			
		if($rcl_options['login_form_recall']==1) add_filter('wp_enqueue_scripts', 'script_page_form_recall');
		else add_filter('wp_enqueue_scripts', 'script_float_form_recall');
	endif;

	if (is_admin()):		
		add_action('admin_init', 'recall_postmeta', 1);
		add_action('save_post', 'recall_postmeta_update', 0);
	endif;

		add_action('wp_head','rcl_update_timeaction_user');
		add_filter('get_avatar','custom_avatar_recall', 1, 5);	
		add_action('before_delete_post', 'delete_attachments_with_post_rcl');
		
	if(is_admin()):
		add_action('admin_head','output_script_style_admin_recall');
		add_action('admin_menu', 'wp_recall_options_panel',19);
	endif;	
}
add_action('init','get_init_filters_actions_rcl');

function script_page_form_recall(){
	wp_enqueue_script( 'page_form_recall', RCL_URL.'js/page_form.js' );
}
function script_float_form_recall(){
	wp_enqueue_script( 'float_form_recall', RCL_URL.'js/float_form.js' );
}
	
function output_style_scripts_recall(){
	global $user_ID;
	global $rcl_options;
	if($rcl_options['custom_scc_file_recall']!='') wp_enqueue_style( 'style_recall', $rcl_options['custom_scc_file_recall'] );
	else wp_enqueue_style( 'style_recall', RCL_URL.'css/style.css' );
	wp_enqueue_style( 'fileapi_static', RCL_URL.'js/fileapi/statics/main.css' );
	wp_enqueue_style( 'fileapi_jcrop', RCL_URL.'js/fileapi/jcrop/jquery.Jcrop.min.css' );
	wp_enqueue_style( 'bx-slider-css', RCL_URL.'js/jquery.bxslider/jquery.bxslider.css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'recall_recall', RCL_URL.'js/recall.js' );
	wp_enqueue_script( 'bx-slider', RCL_URL.'js/jquery.bxslider/jquery.bxslider.js' );
	if(!file_exists(TEMP_PATH.'scripts/header-scripts.js')){
		$rcl_addons = new rcl_addons();
		$rcl_addons->get_update_scripts_file_rcl();
	}
	wp_enqueue_script( 'temp-scripts-recall', TEMP_URL.'scripts/header-scripts.js' );
	wp_enqueue_script( 'rangyinputs', RCL_URL.'js/rangyinputs.js' );
	
}

function add_user_data_rcl(){
	global $user_ID;
    $data = '<script>var user_ID = '.$user_ID.';</script>';
    echo $data;
}
add_action('wp_head','add_user_data_rcl');

function output_script_style_admin_recall(){
	wp_enqueue_style( 'admin_recall', RCL_URL.'css/admin.css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'primary_script_admin_recall', RCL_URL.'js/admin.js' );
	if(isset($_GET['page'])){
		if($_GET['page']=='manage-userfield'||$_GET['page']=='manage-custom-fields'||$_GET['page']=='manage-public-form') wp_enqueue_script( 'script_sortable_recall', RCL_URL.'js/sortable.js' );
	}
}

add_action('wp_footer', 'foot_scripts_uuf');
function foot_scripts_uuf() {
	if(file_exists(TEMP_PATH.'scripts/footer-scripts.js')){
		//get_update_scripts_footer_rcl();
		wp_enqueue_script( 'jquery' );	
		wp_enqueue_script( 'FileAPI-min', RCL_URL.'js/fileapi/FileAPI/FileAPI.min.js' );
		wp_enqueue_script( 'mousewheel-js', RCL_URL.'js/fileapi/FileAPI/FileAPI.exif.js' );
		wp_enqueue_script( 'fileapi-pack-js', RCL_URL.'js/fileapi/jquery.fileapi.js' );
		wp_enqueue_script( 'Jcrop-buttons-js', RCL_URL.'js/fileapi/jcrop/jquery.Jcrop.min.js' );
		wp_enqueue_script( 'modal-thumbs-js', RCL_URL.'js/fileapi/statics/jquery.modal.js' );
		//wp_enqueue_script( 'footer-js', RCL_URL.'js/fileapi/footer.js' );
		wp_enqueue_script( 'footer-js-recall', TEMP_URL.'scripts/footer-scripts.js' );
	}
}	

add_filter('wp_footer', 'add_footer_url_recall');
function add_footer_url_recall(){
	global $rcl_options;
	if($rcl_options['footer_url_recall']!=1) return false;
	if(is_front_page()&&!is_user_logged_in()) echo '<p class="plugin-info">'.__('Сайт работает с использованием функционала плагина','rcl').'  <a target="_blank" href="http://wppost.ru/">Wp-Recall</a></p>';
}

function delete_user_action_rcl($user){
	global  $wpdb;
	$wpdb->query("DELETE FROM ".RCL_PREF."user_action WHERE user = '$user'");
}
add_action('delete_user','delete_user_action_rcl');

function get_login_user_rcl(){

	$pass = $_POST['pass-user'];
	$login = $_POST['login-user'];
	$member = $_POST['member-user'];
	
	if($pass&&$login){
	
		if ( $user = get_user_by('login', $login) ){
			$user_data = get_userdata( $user->ID );
			$roles = $user_data->roles;
			$role = array_shift($roles);
			if($role=='need-confirm'){
				wp_redirect(get_redirect_url_rcl($_POST['referer_rcl']).'action-rcl=login&err=confirm');exit;
			}
		}
				$creds = array();
				$creds['user_login'] = $login;
				$creds['user_password'] = $pass;
				$creds['remember'] = $member;
				$user = wp_signon( $creds, false );
				if ( is_wp_error($user) ){
					wp_redirect(get_redirect_url_rcl($_POST['referer_rcl']).'action-rcl=login&err=failed');exit;
				}else{
					rcl_update_timeaction_user();					
					wp_redirect(get_authorize_url_rcl($user->ID));exit;
				}						
	}else{		
		wp_redirect(get_redirect_url_rcl($_POST['referer_rcl']).'action-rcl=login&err=empty');exit;
	} 
}

add_action('init', 'get_login_user_rcl_activate');
function get_login_user_rcl_activate ( ) {
  if ( isset( $_POST['submit-login'] ) ) {
	if( !wp_verify_nonce( $_POST['_wpnonce'], 'login-key-rcl' ) ) return false;	
    add_action( 'wp', 'get_login_user_rcl' );
  }
}

function get_authorize_url_rcl($user_id){
	global $rcl_options;
	if($rcl_options['authorize_page']){
		if($rcl_options['authorize_page']==1) $redirect = $_POST['referer_rcl'];
		if($rcl_options['authorize_page']==2) $redirect = $rcl_options['custom_authorize_page'];
	}else{
		$redirect = get_author_posts_url($user_id);
	}
	return $redirect;
}

function get_register_user_rcl(){
	global $wpdb,$rcl_options;
	$pass = $_POST['pass-user'];	
	$email = $_POST['email-user'];	
	$login = $_POST['login-user'];
	
	$perm = $_POST['referer-rcl'];
	$ar_perm = explode('?',$perm);
	$cnt = count($ar_perm);
	if($cnt>1) $a = '&';
	else $a = '?';

	if(!$pass||!$email||!$login){
		wp_redirect($perm.$a.'action-rcl=register&err=empty');exit;
	}
	
	$res_email = email_exists( $email );
	$res_login = username_exists($login);
	$correctemail = is_email($email);
	$valid = validate_username($login);
	if($res_login||$res_email||!$correctemail||!$valid){
		if(!$valid){
			wp_redirect($perm.$a.'action-rcl=register&err=login');exit;
		}
		if($res_login){
			wp_redirect($perm.$a.'action-rcl=register&err=login-us');exit;
		}
		if($res_email){			
			wp_redirect($perm.$a.'action-rcl=register&err=email-us');exit;
		}		
		if(!$correctemail){			
			wp_redirect($perm.$a.'action-rcl=register&err=email');exit;
		}						
	}else{					
			
			$userdata = array(
				'user_pass' => $pass
				,'user_login' => $login
				,'user_nicename' => ''
				,'user_email' => $email
				,'display_name' => $fio
				,'nickname' => $login
				,'first_name' => $fio
				,'rich_editing' => 'true'
			);

			$user_id = wp_insert_user( $userdata );
			update_usermeta($user_id, 'show_admin_bar_front', 'false');			
			
		if($user_id){	
			
			$regcode = md5($login);	
			
			add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
			$headers = 'From: '.get_bloginfo('name').' <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
			$subject = 'Подтвердите регистрацию!';														
			$textmail = '
			<p>Вы или кто то другой зарегистрировались на сайте "'.get_bloginfo('name').'" со следующими данными:</p>
			<p>Логин: '.$login.'</p>
			<p>Пароль: '.$pass.'</p>';
			
			if($rcl_options['confirm_register_recall']==1){
				//update_usermeta($user_id, 'account_confirm', 'false');	
				wp_update_user( array ('ID' => $user_id, 'role' => 'need-confirm') ) ;
				$res['recall']='<p style="text-align:center;color:green;">Регистрация завершена!<br />Для подтверждения регистрации перейдите по ссылке в письме, высланном на указанную вами почту.</p>';
				$textmail .= '<p>Если это были вы, то подтвердите свою регистрацию перейдя по ссылке ниже:</p>
				<p>'.get_bloginfo('wpurl').'/?rglogin='.$login.'&rgpass='.$pass.'&rgcode='.$regcode.'</p>
				<p>Не получается активировать аккаунт?</p>
				<p>Скопируйте текст ссылки ниже, вставьте его в адресную строку вашего браузера и нажмите Enter</p>';
			}else{
				$res['recall']='<p style="text-align:center;color:green;">Регистрация завершена!<br />Авторизуйтесь на сайте, используя логин и пароль указанные при регистрации</p>';
				$wpdb->insert( RCL_PREF.'user_action', array( 'user' => $user_id, 'time_action' => '' ));
			}
			
			$textmail .= '<p>Если это были не вы, то просто проигнорируйте это письмо</p>
			<p>------------------------------------------------------------</p>
			<p>Это письмо было создано автоматически, не надо отвечать на него</p>';				
			wp_mail($email, $subject, $textmail, $headers);					
										
			wp_redirect($perm.$a.'action-rcl=login&success=true');exit;									
		}						
	}
}

add_action('init', 'get_register_user_rcl_activate');
function get_register_user_rcl_activate ( ) {
  if ( isset( $_POST['submit-register'] ) ) {
	if( !wp_verify_nonce( $_POST['_wpnonce'], 'register-key-rcl' ) ) return false;	
    add_action( 'wp', 'get_register_user_rcl' );
  }
}

add_filter('rcl_content_user','get_action_user_lk_rcl',5,2);
function get_action_user_lk_rcl($content_lk,$author_lk){
	global $rcl_userlk_action;
			
	$last_action = last_user_action_recall($rcl_userlk_action);		
	if(!$last_action){
		$content_lk .= '<div class="status" title="на сайте"><div class="status_user" style="background:url('.RCL_URL.'img/online.gif) no-repeat;"></div></div>';
		$online = 1;
	}else{ 
		$content_lk .= '<div class="status" title="'.__('не в сети','rcl').' '.$last_action.'"><div class="status_user" style="background:url('.RCL_URL.'img/offline.gif) no-repeat;"></div></div>';
		$online = 0;
	}
	return $content_lk;
}

add_filter('rcl_sidebar_user','get_avatar_sidebar_lk_rcl',1,2);
function get_avatar_sidebar_lk_rcl($sidebar,$author_lk){
	$sidebar .= get_avatar($author_lk,120);
	return $sidebar;
}

add_filter('rcl_content_user','get_user_description_content_lk_rcl',6,2);
function get_user_description_content_lk_rcl($content_lk,$author_lk){
	$desc = get_the_author_meta('description',$author_lk);
	if($desc) $content_lk .= '<div class="ballun-status"><span class="ballun"></span><p class="status-user-rcl">'.esc_textarea($desc).'</p></div>';
	return $content_lk;
}

add_filter('rcl_content_user','get_display_name_lk_rcl',3,2);
function get_display_name_lk_rcl($content_lk,$author_lk){
	$d_name = get_the_author_meta('display_name',$author_lk);
	if($d_name) $content_lk .= '<h2>'.$d_name.'</h2>';
	return $content_lk;
}	

// подключаем функцию активации мета блока (my_extra_fields)
function recall_postmeta() {
    add_meta_box( 'recall_meta', __('Галерея Wp-Recall для записи','rcl'), 'recall_postmeta_box_func', 'post', 'normal', 'high'  );
	add_meta_box( 'recall_meta', __('Галерея Wp-Recall для записи','rcl'), 'recall_postmeta_box_func', 'products', 'normal', 'high'  );
}

// код блока
function recall_postmeta_box_func( $post ){
	$mark_v = get_post_meta($post->ID, 'recall_slider', 1);
	echo '<p>'.__('Использовать для изображений записи вывод в галерее Wp-Recall?','rcl').':
		 <label><input type="radio" name="wprecall[recall_slider]" value="" '.checked( $mark_v, '',false ).' />'.__('Нет','rcl').'</label>
		 <label><input type="radio" name="wprecall[recall_slider]" value="1" '.checked( $mark_v, '1',false ).' />'.__('Да','rcl').'</label>
	</p>'; ?>
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<?php
}

function recall_postmeta_update( $post_id ){
    if ( !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	if ( !current_user_can('edit_post', $post_id) ) return false;

	if( !isset($_POST['wprecall']) ) return false;	

	$_POST['wprecall'] = array_map('trim', (array)$_POST['wprecall']);
	foreach((array) $_POST['wprecall'] as $key=>$value ){
		if($value=='') delete_post_meta($post_id, $key);
		else update_post_meta($post_id, $key, $value);
	}
	return $post_id;
}

//Добавляем recallbar

add_action('init','add_recall_bar');
function add_recall_bar(){
	global $rcl_options;
	if($rcl_options['view_recallbar']!=1) return false;
	register_nav_menus(array( 'recallbar' => __('Recallbar','rcl') ));									
}

add_action('wp_footer','add_recallbar_menu');
function add_recallbar_menu(){
global $rcl_options;
global $user_ID;
global $rcl_user_URL;
if($rcl_options['view_recallbar']!=1) return false;

		global $user_ID;
		global $rcl_options;
			echo '<div id="recallbar">';
			echo '<ul class="right-recall-menu">';
			$right_li .= '<li><a onclick="addfav()" href="javascript://">'.__('В закладки','rcl').'</a></li>';
			$right_li .= '<li><a onclick="jQuery(\'#favs\').slideToggle();return false;" href="javascript://">'.__('Мои закладки','rcl').'</a></li>';
			
			$right_li = apply_filters('recallbar_right_content',$right_li);
			
			echo $right_li.'</ul>';
			if(is_user_logged_in()){
				echo '<ul class="left-recall-menu">';
				echo '<li><a href="'.$rcl_user_URL.'">'.__('Домой','rcl').'</a></li>';
				echo '</ul>';
			}else{
				echo '<ul class="left-recall-menu">';
				if($rcl_options['login_form_recall']==1){	

					$redirect_url = get_redirect_url_rcl(get_permalink($rcl_options['page_login_form_recall']));
					
					echo '<li><a href="'.$redirect_url.'form=register">'.__('Регистрация','rcl').'</a></li>';
					echo '<li><a href="'.$redirect_url.'form=sign">'.__('Войти','rcl').'</a></li>';
				}else if($rcl_options['login_form_recall']==2){											
					$form .= '<li>'.wp_register('', '', 0).'</li>';
					$form .= '<li>'.wp_loginout('', 0).'</li>';
					echo $form;
				}else if($rcl_options['login_form_recall']==3){											
					$form .= '<li><a href="/">'.__('Главная','rcl').'</a></li>';
					echo $form;
				}else if(!$rcl_options['login_form_recall']){
					echo '<li><a href="#" class="reglink">'.__('Регистрация','rcl').'</a></li>';
					echo '<li><a href="#" class="sign-button">'.__('Войти','rcl').'</a></li>';
				}
				echo '</ul>';
				
			}
			 wp_nav_menu('fallback_cb=null&container_class=recallbar&theme_location=recallbar'); 
			 
			 if ( is_admin_bar_showing() ){ 
				echo '<style>#recallbar{margin-top:28px;}</style>';
			}

			echo '</div>
			<div id="favs" style="display:none"></div>
			<div id="add_bookmarks" style="display:none"></div>' ;
		}

//заменяем ссылку автора комментария на ссылку его ЛК
function add_link_author_in_page($href){
	global $comment;	
	if($comment->user_id==0) return $href;
	$href = get_author_posts_url($comment->user_id);	
	return $href;
}

function rcl_add_edit_post_button($excerpt,$post=null){
if(!isset($post)) global $post;
global $user_ID;
	if($user_ID){
		if($user_ID==$post->post_author){
			$form_button = "<div class='post-edit-button'>					
				<input id='delete-post' type='image' name='delete_post' src='".RCL_URL."img/delete.png' value='".$post->ID."'></div>
				<div class='post-edit-button'>					
				<input type='image' id='edit-post' name='update_post' src='".RCL_URL."img/redactor.png' value='".$post->ID."'></div>";
		}
		
		$form_button = apply_filters('buttons_edit_post_rcl',$form_button,$post);
		
		if($form_button) $excerpt .= $form_button;
	}
	return $excerpt;
}

//Удаление пользователя
function wp_delete_account( $id, $reassign = 'novalue' ) {
	global $wpdb;

	$id = (int) $id;
	$user = new WP_User( $id );

	// allow for transaction statement
	do_action('delete_user', $id);

	if ( 'novalue' === $reassign || null === $reassign ) {
		$post_types_to_delete = array();
		foreach ( get_post_types( array(), 'objects' ) as $post_type ) {
			if ( $post_type->delete_with_user ) {
				$post_types_to_delete[] = $post_type->name;
			} elseif ( null === $post_type->delete_with_user && post_type_supports( $post_type->name, 'author' ) ) {
				$post_types_to_delete[] = $post_type->name;
			}
		}

		$post_types_to_delete = apply_filters( 'post_types_to_delete_with_user', $post_types_to_delete, $id );
		$post_types_to_delete = implode( "', '", $post_types_to_delete );
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d AND post_type IN ('$post_types_to_delete')", $id ) );
		if ( $post_ids ) {
			foreach ( $post_ids as $post_id )
				wp_delete_post( $post_id );
		}

		$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $id) );

		if ( $link_ids ) {
			foreach ( $link_ids as $link_id )
				wp_delete_link($link_id);
		}
	} else {
		$reassign = (int) $reassign;
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id ) );
		$wpdb->update( $wpdb->posts, array('post_author' => $reassign), array('post_author' => $id) );
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id )
				clean_post_cache( $post_id );
		}
		$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $id) );
		$wpdb->update( $wpdb->links, array('link_owner' => $reassign), array('link_owner' => $id) );
		if ( ! empty( $link_ids ) ) {
			foreach ( $link_ids as $link_id )
				clean_bookmark_cache( $link_id );
		}
	}

	if ( is_multisite() ) {
		remove_user_from_blog( $id, get_current_blog_id() );
	} else {
		$meta = $wpdb->get_col( $wpdb->prepare( "SELECT umeta_id FROM $wpdb->usermeta WHERE user_id = %d", $id ) );
		foreach ( $meta as $mid )
			delete_metadata_by_mid( 'user', $mid );

		$wpdb->delete( $wpdb->users, array( 'ID' => $id ) );
	}

	clean_user_cache( $user );

	do_action('deleted_user', $id);

	return true;
}

//запрещаем доступ в админку
add_action('init','wp_admin_success_rcl',1);
function wp_admin_success_rcl(){
	global $current_user,$rcl_options;
	if(is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){	
		$rcl_options = unserialize(get_option('primary-rcl-options'));
		get_currentuserinfo();
		$access = 7;
		if(isset($rcl_options['consol_access_rcl'])) $access = $rcl_options['consol_access_rcl'];
		$user_info = get_userdata($current_user->ID);		
		if ( $user_info->user_level < $access ){
			wp_redirect('/');
		}else {
			return true;
		}
	}	
}

function hidden_admin_panel(){
	global $current_user,$rcl_options;
	get_currentuserinfo();
	$access = 7;
	if(isset($rcl_options['consol_access_rcl'])) $access = $rcl_options['consol_access_rcl'];
	$user_info = get_userdata($current_user->ID);		
	if ( $user_info->user_level < $access ){
		show_admin_bar(false);
	}else{
		return true;
	}
}

/* Удаление поста вместе с его вложениями*/ 
function delete_attachments_with_post_rcl($postid){ 
global  $wpdb;
    $attachments = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => null, 'post_parent' => $postid ) );    
    if($attachments){  
	foreach((array) $attachments as $attachment )  
        wp_delete_attachment( $attachment->ID, true );  
	}	
} 

//Функция вывода своего аватара
function custom_avatar_recall($avatar, $id_or_email, $size, $default, $alt){
	if (is_numeric($id_or_email)){
		$avatar_id = get_option('avatar_user_'.$id_or_email);
		if($avatar_id){
			$image_attributes = wp_get_attachment_image_src($avatar_id); 
			$avatar = "<img class='avatar' src='".$image_attributes[0]."' alt='".$alt."' height='".$size."' width='".$size."' />";	
		}
	}elseif( is_object($id_or_email)){
		$avatar_id = get_option('avatar_user_'.$id_or_email->user_id);
		if ( !empty($id_or_email->user_id) && $avatar_id ){ 
			$image_attributes = wp_get_attachment_image_src($avatar_id); 
			$avatar = "<img class='avatar' src='".$image_attributes[0]."' alt='".$alt."' height='".$size."' width='".$size."' />";
		}
	}
	if ( !empty($id_or_email->user_id)) $avatar = '<a height="'.$size.'" width="'.$size.'" href="'.get_author_posts_url($id_or_email->user_id).'">'.$avatar.'</a>';
	
	return $avatar;
		
}

function get_custom_post_meta_recall($post_id, $id_form=null){
//global $post;
//$post_id = $post->ID;
if(!$id_form) $id_form = 1;

	$get_fields = get_option( 'custom_public_fields_'.$id_form );
	$get_fields = unserialize( $get_fields);
	
	if($get_fields){
		
		foreach((array)$get_fields as $custom_field){				
			$slug = str_replace('-','_',$custom_field['slug']);
				if($custom_field['type']=='text'&&get_post_meta($post_id,$slug,1))
					$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_post_meta($post_id,$slug,1).'</span></p>';
				if($custom_field['type']=='select'&&get_post_meta($post_id,$slug,1)||$custom_field['type']=='radio'&&get_post_meta($post_id,$slug,1))
					$show_custom_field .= '<p><b>'.$custom_field['title'].':</b> <span>'.get_post_meta($post_id,$slug,1).'</span></p>';
				if($custom_field['type']=='checkbox'){
					$chek = explode('#',$custom_field['field_select']);
					$count_field = count($chek);					
					$n=0;
					$chek_field = '';
					for($a=0;$a<$count_field;$a++){
						$slug_chek = $slug.'_'.$a;
						if(get_post_meta($post_id,$slug_chek,1)){
						$n++;
							if($n==1) $chek_field .= get_post_meta($post_id,$slug_chek,1);
								else $chek_field .= ', '.get_post_meta($post_id,$slug_chek,1);
						}
					}
					if($n!=0) $show_custom_field .= '<p><b>'.$custom_field['title'].': </b>'.$chek_field.'</p>';
				}					
				if($custom_field['type']=='textarea'&&get_post_meta($post_id,$slug,1))
					$show_custom_field .= '<p><b>'.$custom_field['title'].':</b></p><p>'.get_post_meta($post_id,$slug,1).'</p>';
		}
		
	return $show_custom_field.$content;
	}	
}
//add_filter('the_content','get_custom_post_meta_recall');

$gost = array(
   "Є"=>"EH","І"=>"I","і"=>"i","№"=>"#","є"=>"eh",
   "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
   "Е"=>"E","Ё"=>"JO","Ж"=>"ZH",
   "З"=>"Z","И"=>"I","Й"=>"JJ","К"=>"K","Л"=>"L",
   "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
   "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KH",
   "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
   "Ы"=>"Y","Ь"=>"","Э"=>"EH","Ю"=>"YU","Я"=>"YA",
   "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
   "е"=>"e","ё"=>"jo","ж"=>"zh",
   "з"=>"z","и"=>"i","й"=>"jj","к"=>"k","л"=>"l",
   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kh",
   "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
   "ы"=>"y","ь"=>"","э"=>"eh","ю"=>"yu","я"=>"ya",
   "—"=>"-","«"=>"","»"=>"","…"=>""
  );

$iso = array(
   "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
   "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
   "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
   "З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
   "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
   "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
   "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
   "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
   "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
   "е"=>"e","ё"=>"yo","ж"=>"zh",
   "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
   "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
   "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
   "—"=>"-","«"=>"","»"=>"","…"=>""
  );
 
function sanitize_title_with_translit_recall($title) {
	global $gost, $iso;
	$rtl_standard = get_option('rtl_standard');
	switch ($rtl_standard) {
		case 'off':
		    return $title;		
		case 'gost':
		    return strtr($title, $gost);
		default: 
		    return strtr($title, $iso);
	}
}

if(!function_exists('sanitize_title_with_translit')) add_action('sanitize_title', 'sanitize_title_with_translit_recall', 0);

add_filter('the_content','add_message_post_moderation_rcl');
function add_message_post_moderation_rcl($cont){
global $post;
	if($post->post_status=='pending'){
		$mess = '<h3 class="pending-message">'.__('Запись ожидает утверждения!','rcl').'</h3>';
		$cont = $mess.$cont;
	}
	return $cont;
}

function add_popup_contayner_rcl(){
    $popup = '<div id="rcl-overlay"></div><div id="rcl-popup"></div>';
    echo $popup;
}
add_action('wp_footer','add_popup_contayner_rcl');

add_filter('author_link','edit_author_link_rcl',99,3);
function edit_author_link_rcl($link, $author_id, $author_nicename){
	global $rcl_options;
	if($rcl_options['view_user_lk_rcl']!=1) return $link;
	$get = 'user';
	if($rcl_options['link_user_lk_rcl']!='') $get = $rcl_options['link_user_lk_rcl'];
	return get_redirect_url_rcl(get_permalink($rcl_options['lk_page_rcl'])).$get.'='.$author_id;	
}

function get_userfield_array_rcl($array,$field,$name_data){
	global $wpdb;

	foreach((array)$array as $object){
        if(++$a>1)$userslst .= ',';
        if(is_object($array))$userslst .= $object->$name_data;
		if(is_array($array))$userslst .= $object[$name_data];
    }
	
	$users_fields = $wpdb->get_results("SELECT user_id,meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id IN ($userslst) AND meta_key = '$field'");
	
	foreach((array)$users_fields as $user){
		$fields[$user->user_id] = $user->$field;
	}
	return $fields;
}

//Проверка на заполнененность обязательных полей
function get_chek_required_rcl($fields,$required){
	if(!$fields){
		foreach((array)$fields as $custom_field){				
			$slug = str_replace('-','_',$custom_field['slug']);
			$r = false;
			foreach((array)$required as $req){
				if($custom_field[$req]==1)$r = true;
			}
			if($r){
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
		if(!$requared) $requared = true;
		return $requared;
	}
}

function get_authorize_form_rcl(){
	global $rcl_options;
	
	echo '<div class="panel_lk_recall">';
				
				$login_form = $rcl_options['login_form_recall'];
				
				//echo 0;
				
				if($login_form==1){
				
				//echo 1111;
					
					$redirect_url = get_redirect_url_rcl(get_permalink($rcl_options['page_login_form_recall']));
					
					echo '<p><a href="'.$redirect_url.'form=sign"><input type="button" class="recall-button" value="'.__('Войти','rcl').'"></a></p>';
					echo '<p><a href="'.$redirect_url.'form=register"><input type="button" class="recall-button" value="'.__('Регистрация','rcl').'"></a></p>';
				}else if($login_form==2){
				
				//echo 2222;											
					echo '<p>'.wp_register('', '', 0).'</p>';
					echo '<p>'.wp_loginout('/', 0).'</p>';
				}else if($login_form==3){
				
				//echo 3333;
					
					$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];				
					
					if ( false !== strpos($url, '?action-rcl') ){
						preg_match_all('/(?<=http\:\/\/)[A-zА-я0-9\/\.\-\s\ё]*(?=\?action\-rcl)/iu',$url, $matches); 
						$host = $matches[0][0];
					}
					if ( false !== strpos($url, '&action-rcl') ){
						preg_match_all('/(?<=http\:\/\/)[A-zА-я0-9\/\.\_\-\s\ё]*(&=\&action\-rcl)/iu',$url, $matches); 
						$host = $matches[0][0];
					}
					if(!$host) $host = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
					
					echo '<div class="form-tab-rcl" id="login-form-rcl" style="display:block;">
						<h4 class="form-title">Авторизация</h4>';
						if($_GET['action-rcl']=='login'){
							switch($_GET['err']){
								case 'confirm': $text = 'Ваш email не подтвержден!'; break;
								case 'empty': $text = 'Заполните поля!'; break;
								case 'failed': $text = 'Логин или пароль не верны!'; break;								
							}
							if($text) echo '<span class="error">'.$text.'</span>';
							if(isset($_GET['success'])){
								echo '<span class="success">Регистрация завершена! Проверьте свою почту.</span>';
							}
						}
						echo '<form action="" method="post">							
							<div class="form-block-rcl">
								<label>Логин</label>
								<input required type="text" value="" name="login-user">
							</div>
							<div class="form-block-rcl">
								<label>Пароль</label>
								<input required type="password" value="" name="pass-user">';
								do_action( 'login_form' );
							echo '</div>
							<div class="form-block-rcl">
								<label><input type="checkbox" value="1" name="member-user"> Запомнить</label>								
							</div>
							<input type="submit" class="recall-button link-tab-form" name="submit-login" value="Отправить">
							<a href="#" id="link-register-rcl" class="link-tab-rcl ">Регистрация</a><br>
							<a href="#" id="link-remember-rcl" class="link-tab-rcl ">Забыли пароль?</a>
							'.wp_nonce_field('login-key-rcl','_wpnonce',true,false).'
							<input type="hidden" name="referer_rcl" value="http://'.$host.'">
						</form>
					</div>
					<div class="form-tab-rcl" id="register-form-rcl">
						<h4 class="form-title">Регистрация</h4>';
						if($_GET['action-rcl']=='register'){
							if(isset($_GET['err'])){
								switch($_GET['err']){
									case 'login': $text = 'В логине недопустимые символы!'; break;
									case 'empty': $text = 'Заполните поля!'; break;
									case 'login-us': $text = 'Логин уже используется!'; break;
									case 'email-us': $text = 'Е-mail уже используется!'; break;
									case 'email': $text = 'Некорректный E-mail!'; break;
									default: $text = 'Ошибка заполнения!';
								}
								echo '<span class="error">'.$text.'</span>';
							}
							if(isset($_GET['success'])){
								echo '<span class="success">Регистрация завершена! Проверьте свою почту.</span>';
							}
						}
						//if(!$_GET['action-rcl']=='register'&&!isset($_GET['success'])){
							echo '<form action="" method="post">							
								<div class="form-block-rcl">
									<label>Логин</label>
									<input required type="text" value="" name="login-user">
								</div>
								<div class="form-block-rcl">
									<label>E-mail</label>
									<input required type="email" value="" name="email-user">
								</div>
								<div class="form-block-rcl">
									<label>Пароль</label>
									<input required type="password" value="" name="pass-user">
								</div>';
								//do_action( 'register_form' );
								echo '<input type="submit" class="recall-button" name="submit-register" value="Отправить">
								<a href="#" id="link-login-rcl" class="link-tab-rcl">Вход</a>
								'.wp_nonce_field('register-key-rcl','_wpnonce',true,false).'
								<input type="hidden" name="referer_rcl" value="http://'.$host.'">
							</form>';
						//}
					echo '</div>
					<div class="form-tab-rcl" id="remember-form-rcl">
						<h4 class="form-title">Генерация пароля</h4>';
						if($_GET['action-rcl']=='remember'){
							/*switch($_GET['err']){
								case 'email': $text = 'Некорректный E-mail!'; break;
								case 'empty': $text = 'Пустое поле!'; break;
								case 'email-nous': $text = 'Такой E-mail не зарегистрирован!'; break;								
							}
							if($text) echo '<span class="error">'.$text.'</span>';*/
							if(isset($_GET['success'])){
								echo '<span class="success">Пароль был выслан!<br>Проверьте свою почту.</span>';
							}
						}
						if($_GET['action-rcl']!='remember'&&!isset($_GET['success'])){
						echo '<form action="'.esc_url( site_url( 'wp-login.php?action=lostpassword', 'login_post' )).'" method="post">							
							<div class="form-block-rcl">
								<label>Имя пользователя или e-mail</label>
								<input required type="text" value="" name="user_login">								
							</div>
							<input type="submit" class="recall-button link-tab-form" name="remember-login" value="Отправить">
							<a href="#" id="link-login-rcl" class="link-tab-rcl ">Вход</a><br>
							<a href="#" id="link-register-rcl" class="link-tab-rcl ">Регистрация</a>
							'.wp_nonce_field('remember-key-rcl','_wpnonce',true,false).'
							<input type="hidden" name="redirect_to" value="http://'.get_redirect_url_rcl($host).'action-rcl=remember&success=true">
						</form>';
						}
					echo '</div>';

				}else if(!$login_form){
				
				//echo 4444;
					echo '<p><input class="sign-button recall-button" type="button" value="'.__('Войти','rcl').'"></p>';
					echo '<p><input class="reglink recall-button"  type="button" value="'.__('Регистрация','rcl').'"></p>';
				}
				
				
				echo '</div>';
	//return $form;
}

function global_recall_wpm_options(){
	$content .= ' <div id="recall" class="wrap">
	<form method="post" action="">
		'.wp_nonce_field('update-options-rmag','_wpnonce',true,false);
		
	$content = apply_filters('admin_options_rmag',$content);
	
	$content .= '<div style="width: 600px;">
	<p><input type="submit" class="button button-primary button-large right" name="primary-rmag-options" value="Сохранить настройки" /></p>			
	</form></div>
	</div>';
	echo $content;
}

function update_options_rmag_activate ( ) {
  if ( isset( $_POST['primary-rmag-options'] ) ) {
	if( !wp_verify_nonce( $_POST['_wpnonce'], 'update-options-rmag' ) ) return false;
    foreach($_POST as $key => $value){
		if($key=='primary-rmag-options') continue;
		$options[$key]=$value;
	}
	$options = serialize($options);
	update_option('primary-rmag-options',$options);
	wp_redirect('/wp-admin/admin.php?page=manage-wpm-options');
	exit;
  }
}
add_action('init', 'update_options_rmag_activate');

add_filter('file_scripts_rcl','get_primary_ajax_scripts_rcl');
function get_primary_ajax_scripts_rcl($script){
	
	$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";
	
	$script .= "/*Авторизация пользователя*/
	function sign_user_recall(){
		var pass_sign = jQuery('#user_pass_sign').attr('value');
			var login_sign = jQuery('#user_login_sign').attr('value');
			var referer = jQuery('#referer-rcl').attr('value');
			var dataString = 'action=sign_user_in_account_recall&user_pass='+pass_sign+'&login_sign='+login_sign+'&referer_rcl='+referer;
			jQuery.ajax({
				".$ajaxdata."
				success: function(data){
					if(data['int']==100){				
						 jQuery('#regrequest').html(data['recall']);
						 jQuery('#regrequest').slideDown(1500);
						 location.replace(data['redirect']);
					} else {
						jQuery('#regrequest').html(data['recall']);
						jQuery('#regrequest').slideDown(1500).delay(5000).slideUp(1500);
					}
				} 
			});	  	
			return false;
	}
	
	jQuery('#sign').live('click',function(){
			sign_user_recall();
		});
	jQuery('#user_login_sign, #user_pass_sign').keypress(function(e){
		if(e.keyCode==13){
			sign_user_recall();
		}
	});";
	return $script;
}
add_filter('javascripts_rcl','get_primary_scripts_rcl');
function get_primary_scripts_rcl($script){
	
	$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";
	$script .= "
	function register_request_new_user_recall(dataString){	
		jQuery.ajax({
			".$ajaxdata."
			success: function(data){
				if(data['int']==100){				
					 jQuery('#regrequest').html(data['recall']);
					 jQuery('#regrequest').slideDown(1500);
					 jQuery('.regform').css('display','block'); 
					jQuery('.registerform').css('display','none'); 
					jQuery('.signform').css('display','block');
					jQuery('.arrow-register').removeAttr('id', 'arrow-register'); 
					jQuery('.arrow-sign').attr('id', 'arrow-sign');
				} else {
					jQuery('#regrequest').html(data['recall']);
					jQuery('#regrequest').slideDown(1500).delay(5000).slideUp(1500);
				}
			} 
		});	  	
		return false;
	}
	";
	return $script;
}

/*Внесение данных в таблицу total_rayting_posts*/
function update_total_rayting_posts_rcl(){
	global $wpdb;
	
	$total = $wpdb->get_results("SELECT post,author_post,status FROM ".RCL_PREF."rayting_post");
	
	foreach($total as $rayt){
		$posts[$rayt->post]['author'] = $rayt->author_post;
		$posts[$rayt->post]['rayt'] = $posts[$rayt->post]['rayt'] + $rayt->status;
	}
	
	if($posts){
		foreach((array)$posts as $p=>$data){
			$wpdb->insert(  
				RCL_PREF.'total_rayting_posts',  
				array( 'author_id' => $data['author'], 'post_id' => $p, 'total' => $data['rayt'] )
			);
		}
	}

}

/*Внесение данных в таблицу total_rayting_comments*/
function update_total_rayting_comments_rcl(){
	global $wpdb;
	
	$total = $wpdb->get_results("SELECT comment_id,author_com,rayting FROM ".RCL_PREF."rayting_comments");
	
	foreach($total as $rayt){
		$posts[$rayt->comment_id]['author'] = $rayt->author_com;
		$posts[$rayt->comment_id]['rayt'] = $posts[$rayt->comment_id]['rayt'] + $rayt->rayting;
	}
	if($posts){
		foreach((array)$posts as $p=>$data){
			$wpdb->insert(  
				RCL_PREF.'total_rayting_comments',  
				array( 'author_id' => $data['author'], 'comment_id' => $p, 'total' => $data['rayt'] )
			);
		}
	}
}

/*Внесение данных в таблицу total_rayting_users*/
function update_total_rayting_users_rcl(){
	global $wpdb;
	
	$all_users = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."users");
	
	foreach($all_users as $us){
		$users[$us->ID]['rayt'] = 0;
	}
	
	$total_com = $wpdb->get_results("SELECT comment_id,author_com,rayting FROM ".RCL_PREF."rayting_comments");
	
	foreach($total_com as $com){
		//if(!isset($users[$com->author_com])) continue;
		$users[$com->author_com]['rayt'] += $com->rayting;
	}
	
	$total_post = $wpdb->get_results("SELECT post,author_post,status FROM ".RCL_PREF."rayting_post");
	
	foreach($total_post as $post){
		//if(!isset($users[$post->author_post])) continue;
		$users[$post->author_post]['rayt'] += $post->status;
	}

	
	foreach($users as $user=>$rayt){
		$wpdb->insert(  
			RCL_PREF.'total_rayting_users',  
			array( 'user_id' => $user, 'total' => $rayt['rayt'] )
		);
	}

}
?>