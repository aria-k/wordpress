<?php$path_parts = pathinfo(__FILE__);$url_ar = explode('/',$path_parts['dirname']);for($a=count($url_ar);$a>=0;$a--){if($url_ar[$a]=='wp-content'){ $path .= 'wp-load.php'; break; }else{ $path .= '../'; }}require_once( $path );require_once(ABSPATH . "wp-admin" . '/includes/image.php');require_once(ABSPATH . "wp-admin" . '/includes/file.php');require_once(ABSPATH . "wp-admin" . '/includes/media.php');global $user_ID;		if(!$user_ID) return false;	if($_FILES['files']){		foreach($_FILES['files'] as $key => $data){			$upload['file'][$key] = $data[0];		}	}		if($_FILES['filedata']){		foreach($_FILES['filedata'] as $key => $data){			$upload['file'][$key] = $data;		}	}		//print_r($_FILES['files']);	//print_r($upload['file']);					if(function_exists('ulogin_get_avatar')){		delete_user_meta($user_ID, 'ulogin_photo');	}				if(get_option('avatar_user_'.$user_ID)){		$attachment_id = get_option('avatar_user_'.$user_ID);		wp_delete_attachment( $attachment_id );	}		$avatar = wp_handle_upload( $upload['file'], array('test_form' => FALSE) );		$attachment = array(		'post_mime_type' => $avatar['type'],		'post_title' => 'avatar user'.$user_ID,		'post_content' => 'image',						'post_status' => 'inherit'	);		$attach_id = wp_insert_attachment( $attachment, $avatar['file'], 0 );	if (is_wp_error($attach_id)) {		$error = "Error: $attach_id <br />";	}	if($error == ''){		$attach_data = wp_generate_attachment_metadata( $attach_id, $avatar['file'] );				wp_update_attachment_metadata( $attach_id, $attach_data );						$result = update_option( 'avatar_user_'.$user_ID, $attach_id );		if($result){				//wp_redirect( get_redirect_url_rcl(get_author_posts_url($user_ID),'profile') );  exit;						$res['result'] = '<div class="success">Новый аватар был успешно загружен!</div>';						}				}		echo json_encode($res);	exit;?>