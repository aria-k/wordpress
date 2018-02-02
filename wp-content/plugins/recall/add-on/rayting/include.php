<?php
function raytout($rayt){
	if($rayt>0){$class="rayt-plus";$rayt='+'.$rayt;}
	elseif($rayt<0)$class="rayt-minus";
	else{$class="null";$rayt=0;}
	return '<span class="'.$class.'">'.$rayt.'</span>';
}

function update_total_rayt_post_rcl($post_id,$user_id,$rayt){
	global $wpdb,$rcl_options;
	
	$total = $wpdb->get_var("SELECT total FROM ".RCL_PREF."total_rayting_posts WHERE post_id = '$post_id'");
	
	if(isset($total)){
	$total += $rayt;
	$res = $wpdb->update(  
		RCL_PREF.'total_rayting_posts',  
		array('total'=>$total),
		array('post_id'=>$post_id,'author_id' => $user_id)
	);
	
	}else{
		$wpdb->insert(  
			RCL_PREF.'total_rayting_posts',  
			array( 'author_id' => $user_id, 'post_id' => $post_id, 'total' => $rayt )
		);
	}
	if($rcl_options['rayt_post_user_rayt']==1) update_total_rayt_user_rcl($user_id,$rayt);
}

function update_total_rayt_comment_rcl($comment_id,$user_id,$rayt){
	global $wpdb,$rcl_options;
	
	$total = $wpdb->get_var("SELECT total FROM ".RCL_PREF."total_rayting_comments WHERE comment_id = '$comment_id'");
	
	if(isset($total)){
	$total += $rayt;
	$res = $wpdb->update(  
		RCL_PREF.'total_rayting_comments',  
		array('total'=>$total),
		array('comment_id'=>$comment_id,'author_id' => $user_id)
	);
	
	}else{
	
		$wpdb->insert(  
			RCL_PREF.'total_rayting_comments',  
			array( 'author_id' => $user_id, 'comment_id' => $comment_id, 'total' => $rayt )
		);
		
	}
	if($rcl_options['rayt_comment_user_rayt']==1) update_total_rayt_user_rcl($user_id,$rayt);
}

function update_total_rayt_user_rcl($user_id,$rayt){
	global $wpdb;
	
	$total = $wpdb->get_var("SELECT total FROM ".RCL_PREF."total_rayting_users WHERE user_id = '$user_id'");

	if(isset($total)){
		$total += (int)$rayt;
		$res = $wpdb->update(  
			RCL_PREF.'total_rayting_users',  
			array('total'=>$total),
			array('user_id' => $user_id)
		);
	
	}else{
		$total == $rayt;
		$wpdb->insert(  
			RCL_PREF.'total_rayting_users',  
			array( 'user_id' => $user_id, 'total' => $rayt )
		);
		
	}
	return $total;
}

function cancel_comment_rayt_rcl($user_id,$comment_id,$rayt){
	global $wpdb,$user_ID;
	$wpdb->query("DELETE FROM ".RCL_PREF."rayting_comments WHERE comment_id = '$comment_id' AND user='$user_ID'");
	$total = $wpdb->get_var("SELECT total FROM ".RCL_PREF."total_rayting_comments WHERE author_id = '$user_id' AND comment_id='$comment_id'");
	$total -= (int)$rayt;
	$res = $wpdb->update(  
		RCL_PREF.'total_rayting_comments',  
		array('total'=>$total),
		array('comment_id' => $comment_id)
	);
	$rayt = -1*$rayt;
	$total_user = update_total_rayt_user_rcl($user_id,$rayt);
	
	return $total;
}
function cancel_post_rayt_rcl($user_id,$post_id,$rayt){
	global $wpdb,$user_ID;
	$wpdb->query("DELETE FROM ".RCL_PREF."rayting_post WHERE post = '$post_id' AND user='$user_ID'");
	$total = $wpdb->get_var("SELECT total FROM ".RCL_PREF."total_rayting_posts WHERE author_id = '$user_id' AND post_id='$post_id'");
	$total -= (int)$rayt;
	$res = $wpdb->update(  
		RCL_PREF.'total_rayting_posts',  
		array('total'=>$total),
		array('post_id' => $post_id)
	);	
	$rayt = -1*$rayt;
	update_total_rayt_user_rcl($user_id,$rayt);
	
	return $total;
}
?>