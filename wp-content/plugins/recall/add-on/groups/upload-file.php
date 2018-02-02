<?php
$path_parts = pathinfo(__FILE__);
$url_ar = explode('/',$path_parts['dirname']);
for($a=count($url_ar);$a>=0;$a--){ if($url_ar[$a]=='wp-content'){ $path .= 'wp-load.php'; break; }else{ $path .= '../'; }}
require_once( $path );

require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	if(isset($_GET['id_group'])&&$_GET['id_group']!='undefined') $id_group = $_GET['id_group'];
			
	$image = wp_handle_upload( $_FILES['uploadfile'], array('test_form' => FALSE) );
	if($image['file']){
		$attachment = array(
			'post_mime_type' => $image['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($image['file'])),
			'post_content' => 'gallery_group_'.$term_id,			
			'guid' => $image['url'],
			'post_parent' => '',
			'post_author' => $user_ID,
			'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $image['file'], '' );				
		$attach_data = wp_generate_attachment_metadata( $attach_id, $image['file'] );		
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		if(!$id_post){
			$temp_gal = unserialize(get_the_author_meta('tempgallery',$user_ID));
			if(!$temp_gal) $temp_gal = array();
			$cnt = count($temp_gal);
			$temp_gal[$cnt]['ID'] = $attach_id;
			$temp_gal[$cnt]['url'] = $image['url'];
			update_usermeta($user_ID,'tempgallery',serialize($temp_gal));
		}
		
		$small_url = wp_get_attachment_image_src( $attach_id, 'thumbnail' );		

		$string = "<li id='li-".$attach_id."'><span class='delete'></span><label><img src='".$small_url[0]."'><span><input type='checkbox' class='thumb-foto' id='thumb-".$attach_id."' name='thumb[".$attach_id."]' value='1'> - главное</span></label><span>[art id='$attach_id']</span></li>";

		//$res['item']=$id_post;
		$res['string']=$string;	
		echo json_encode($res);
		exit;							
	}else{
		echo 'error';
	}
?>