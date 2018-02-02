<?php
/*
Plugin Name: WP-Recall
Plugin URI: http://wppost.ru
Description: Фронт-енд профиль, система личных сообщений и рейтинг пользователей на сайте вордпресс.
Version: 8.12.0
Author: Plechev Andrey
Author URI: http://vk.com/device64
*/

/*  Copyright 2012  Plechev Andrey  (email : plechev.a {at} yandex.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//определяем глобальные переменные
function get_rcl_global_unit(){
	global $wpdb;
	global $user_ID;
	global $rcl_current_action;
	global $rcl_user_URL;
	global $rcl_options;
	$rcl_options = unserialize(get_option('primary-rcl-options'));
	$upload_dir = wp_upload_dir();
	$path_parts = pathinfo(__FILE__);
	define('TEMP_PATH', $upload_dir['basedir'].'/temp-rcl/');
	define('TEMP_URL', $upload_dir['baseurl'].'/temp-rcl/');	
	define('RCL_PATH', $path_parts['dirname'].'/');
	define('RCL_URL', plugins_url().'/recall/');
	define('RCL_PREF', $wpdb->prefix.'rcl_');
	$rcl_user_URL = get_author_posts_url($user_ID);
	$rcl_current_action = $wpdb->get_var("SELECT time_action FROM ".RCL_PREF."user_action WHERE user='$user_ID'");	
}
add_action('init','get_rcl_global_unit',10);

require_once("functions-rcl.php");

function recall_install(){
global $wpdb;
define('RCL_PREF', $wpdb->prefix.'rcl_');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	 	
    $table4 = RCL_PREF."user_action";
    if($wpdb->get_var("show tables like '". $table4 . "'") != $table4) {
	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table4 . "` (
		  ID bigint (20) NOT NULL AUTO_INCREMENT,
		  user INT(20) NOT NULL,	  
		  time_action DATETIME NOT NULL,
		  UNIQUE KEY id (id)
		) DEFAULT CHARSET=utf8;");
	}
	
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'].'/temp-rcl/';
	if(!is_dir($path)){
		mkdir($path);
		chmod($path, 0755);
	}
	
	wp_clear_scheduled_hook('days_garbage_file_rcl');
	
	update_option('default_role','author');
	update_option('show_avatars',1);

	$roledata = array(
		'need-confirm' => array(
			'name'=>__('Неподтвержденные','rcl'),
			'cap'=>array('read' => false, 'edit_posts' => false, 'delete_posts' => false, 'upload_files' => false)
		)
	);
		
		
	foreach($roledata as $key=>$role){
		remove_role($key);
		add_role($key, $role['name'], $role['cap']);
	}
		
	$rcl_options = unserialize(get_option('primary-rcl-options'));
	$rcl_options['footer_url_recall']=1;
	$rcl_options = serialize($rcl_options);
	update_option('primary-rcl-options',$rcl_options);
}
register_activation_hook(__FILE__,'recall_install');

function recall_uninstall() {
    global $wpdb;
	$wpdb->query("DROP TABLE ".$wpdb->prefix."rcl_user_action");
}
register_uninstall_hook(__FILE__, 'recall_uninstall');

function wp_recall($user_LK=null){

	global $wpdb,$user_ID,$rcl_user_LK,$rcl_userlk_action,$rcl_options;
	
	if(!$user_LK){
		$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
		$rcl_user_LK = $curauth->ID;
	}else{
		$rcl_user_LK = $user_LK;
	}
	$rcl_userlk_action = $wpdb->get_var("SELECT time_action FROM ".RCL_PREF."user_action WHERE user='$rcl_user_LK'");

	echo "<div id='conteiner'></div>";
	
    if($wpdb->get_var("show tables like '".RCL_PREF."black_list_user'"))
		$ban = $wpdb->get_row("SELECT * FROM ".RCL_PREF."black_list_user WHERE user = '$rcl_user_LK' AND ban = '$user_ID'");
			
	$last_action = last_user_action_recall($rcl_userlk_action);		
	if(!$last_action) $online = 1;
	else $online = 0;

	echo '<div id="rcl-'.$rcl_user_LK.'" class="wprecallblock">';
		
		if ($_GET['updated']) echo '<div class="box-green"><strong>'.__('Ваш профиль был обновлен','rcl').'</strong><br /></div>';
		
		$header_lk = apply_filters('rcl_header_user',$header_lk,$rcl_user_LK);
		$sidebar_lk = apply_filters('rcl_sidebar_user',$sidebar_lk,$rcl_user_LK);
		$content_lk = apply_filters('rcl_content_user',$content_lk,$rcl_user_LK);
		$footer_lk = apply_filters('rcl_footer_user',$footer_lk,$rcl_user_LK);
		
		$lk_author = '
			<div id="lk-conteyner">
				<div class="lk-header">'.$header_lk.'</div>
				<div class="lk-sidebar">'.$sidebar_lk.'</div>
				<div class="lk-content">'.$content_lk.'</div>
				<div class="lk-footer">'.$footer_lk.'</div>
			</div>';		
		
		
		echo $lk_author;
		
		if($rcl_options['buttons_place']==1) $class="left-buttons";
		echo '<div class="rcl-menu '.$class.'">'.get_the_button_wprecall($rcl_user_LK).'</div>';
		
		echo '<div class="rcl-content">'.get_the_block_wprecall($rcl_user_LK, $online, $ban).'</div>'; 
		
		echo '</div>';

		echo apply_filters('rcl_after_lk',$after_lk,$rcl_user_LK);

}


function get_the_button_wprecall($rcl_user_LK){
	$button_wprecall = apply_filters( 'the_button_wprecall', $button, $rcl_user_LK );
	return $button_wprecall;
}

function get_the_block_wprecall($rcl_user_LK, $online, $ban){
	$block_wprecall = apply_filters( 'the_block_wprecall', $block_wprecall, $rcl_user_LK, $online, $ban );	
	return $block_wprecall;
}
?>