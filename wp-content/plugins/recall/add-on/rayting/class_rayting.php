<?php
class RCL_Rayt{
	function add_post_rayting_recall(){
		global $wpdb,$rcl_options,$user_ID;		
		if(!$user_ID) exit;
		
		$post = $_POST['post'];
		$post = explode('-', $post);
		$id_rayt = $_POST['id_rayt'];
		$id_rayt = pow($id_rayt, 0.5);
		$rayt = $id_rayt - $post[1];
		$post_id = $post[1];
		if(!$rcl_options['count_rayt_post']) $rcl_options['count_rayt_post'] = 1;
		if(abs($rayt)!=$rcl_options['count_rayt_post']) exit;
			
		$values = $wpdb->get_row("SELECT * FROM ".RCL_PREF."rayting_post WHERE post = '$post_id' AND user = '$user_ID'");
			
		if($values){
			$log['otvet']=110;
			echo json_encode($log);
			exit;
		}
			
		$post_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE ID = '$post_id'");
		if($post_data->post_type=='products'){
			$salefile = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_parent = '".$post[1]."' AND post_title = 'salefile'");
			if($salefile){
				$sale = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."rmag_files_downloads WHERE parent_id = '".$post[1]."' AND user_id = '$user_ID'");
				if(!$sale){
					$log['otvet']=120;
					$log['message'] = 'Вы не можете менять рейтинг товару, который не приобретали лично!';
					echo json_encode($log);
					exit;
				}
			}
		}
		
		$wpdb->insert(  
			RCL_PREF.'rayting_post',  
					array( 'user' => $user_ID, 'post' => $post[1], 'author_post' => $post_data->post_author, 'status' => $rayt )
		);

		update_total_rayt_post_rcl($post_id,$post_data->post_author,$rayt);
				
		$log['otvet']=100;
		$log['post']=$post[1];
		$log['rayt']=$rayt;
				
		echo json_encode($log);	
		exit;
	}
	function add_rayting_comment_recall(){
		global $wpdb,$user_ID,$rcl_options;
		
		if(!$user_ID){
			$log['otvet']=110;
			echo json_encode($log);	
			exit;
		}
		
		$com = $_POST['com'];
		$com = explode('-', $com);
		$id_rayt = $_POST['id_rayt'];
		$id_rayt = pow($id_rayt, 0.5);
		$rayt = $id_rayt - $com[1];
		$com_id = $com[1];
		
		if(!$rcl_options['count_rayt_comment']) $rcl_options['count_rayt_comment'] = 1;
		if(abs($rayt)!=$rcl_options['count_rayt_comment']) exit;
			
		$values = $wpdb->get_row("SELECT * FROM ".RCL_PREF. "rayting_comments WHERE comment_id = '$com_id' AND user = '$user_ID'");
			
		if($values){
			$log['otvet']=110;
			echo json_encode($log);
			exit;
		}
			
		$userid = $wpdb->get_var("SELECT user_id FROM ".$wpdb->prefix."comments WHERE comment_ID = '$com_id'");
		$time_action = date("Y-m-d H:i:s");
		$wpdb->insert(  
			RCL_PREF.'rayting_comments',  
			array( 'user' => $user_ID, 'comment_id' => $com[1], 'author_com' => $userid, 'rayting' => $rayt, 'time_action' => $time_action )
		);
		
		update_total_rayt_comment_rcl($com_id,$userid,$rayt);

		do_action('rcl_edit_rayt_comment',$user_ID,$com_id);
				
		$log['otvet']=100;
		$log['com']=$com[1];
		$log['rayt']=$rayt;

		echo json_encode($log);	
		exit;
	}
	function get_vote_comment_recall(){
		global $wpdb,$user_ID,$rcl_options;
		$id_com = $_POST['id_com'];		
		$votes_com = $wpdb->get_results("SELECT rayting,user FROM ".RCL_PREF."rayting_comments WHERE comment_id = '$id_com' ORDER BY ID DESC");
		if($votes_com){
			
			$names = get_names_array_rcl($votes_com,'user');
			
			$recall_votes = '<div id="votes-comment-'.$id_com.'" class="votes-comment">			
			 <div align="right" id="close-votes-'.$id_com.'" class="close-votes">X</div>
				<ul>';
			foreach((array)$votes_com as $vote){
				$rayt = $vote->rayting;
				$recall_votes .= '<li><a target="_blank" href="'.get_author_posts_url($vote->user).'">'.$names[$vote->user].'</a> поставил: '.raytout($rayt).'</li>';
			}
			$recall_votes .= '</ul></div>';
				
			$log['otvet']=100;
			$log['id_com']=$id_com;
			$log['votes']=$recall_votes;			
		}		
		echo json_encode($log);
		exit;
	}
	function get_vote_post_recall(){
		global $wpdb,$user_ID,$rcl_options;
		$id_post = $_POST['id_post'];		
		$votes_post = $wpdb->get_results("SELECT user,status FROM ".RCL_PREF."rayting_post WHERE post = '$id_post' ORDER BY ID DESC");
		if($votes_post){
		
			$names = get_names_array_rcl($votes_post,'user');
		
			$recall_votes = '<div id="votes-post-'.$id_post.'" class="votes-post">			
		 <div align="right" id="close-votes-'.$id_post.'" class="close-votes">X</div>
			<ul>';
			foreach((array)$votes_post as $vote){
				$rayt = $vote->status;
				$recall_votes .= '<li><a target="_blank" href="'.get_author_posts_url($vote->user).'">'.$names[$vote->user].'</a> поставил: '.raytout($rayt).'</li>';
			}
			$recall_votes .= '</ul></div>';
			
			$log['otvet']=100;
			$log['id_post']=$id_post;
			$log['votes']=$recall_votes;			
		}
		echo json_encode($log);
		exit;
	}
	function get_vote_user_comments(){
		global $wpdb,$user_ID,$rcl_options;
		if(!$user_ID){
			$log['otvet']=1;	
			echo json_encode($log);
			exit;
		}
		
		$id_user = $_POST['iduser'];
		$rcl_comments_rayt = $wpdb->get_results("SELECT user,comment_id,author_com,rayting FROM ".RCL_PREF."rayting_comments WHERE author_com = '$id_user' ORDER BY ID DESC");
		
			$recall_votes = '<div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div><input type="button" id="view-rayt-posts-'.$id_user.'" class="view-rayt-posts recall-button" value="Рейтинг записей"><ul class="rayt-list-user">';
			$n=0;
			
			$names = get_names_array_rcl($rcl_comments_rayt,'user');
			
			foreach((array)$rcl_comments_rayt as $comments){
				
				$n++;
					$rayt = $comments->rayting;				
						$recall_votes .= '<li>'.$comments->ID.'<a target="_blank" href="'.get_author_posts_url($comments->user).'">'.$names[$comments->user].'</a> оценил: '.raytout($rayt).' <a href="'.get_comment_link( $comments->comment_id ).'">отзыв</a> к записи</li>';

			}

			$recall_votes .= '</ul>';
		
		if($n!=0){
			$log['otvet']=100;
			$log['iduser']=$id_user;
			$log['votes']=$recall_votes;			
		}else{
			$log['otvet']=100;
			$log['iduser']=$id_user;
			$log['votes']='<div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div><input type="button" id="view-rayt-posts-'.$id_user.'" class="view-rayt-posts recall-button" value="Рейтинг записей"><ul><p>У пользователя нет комментариев</p><ul>';
		}
		echo json_encode($log);
		exit;
	}
	function get_vote_user_posts(){
		global $wpdb,$user_ID,$rcl_options;
		if(!$user_ID){
			$log['otvet']=1;	
			echo json_encode($log);
			exit;
		}
	
		$id_user = $_POST['iduser'];

		$rcl_rayting_post = $wpdb->get_results("SELECT user,post,status,author_post FROM ".RCL_PREF."rayting_post WHERE author_post = '$id_user' ORDER BY ID DESC");
	
		$recall_votes = '<div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div><input type="button" id="view-rayt-comments-'.$id_user.'" class="view-rayt-comments recall-button" value="Рейтинг комментариев"><ul class="rayt-list-user">';
		$n=0;
		
		foreach((array)$rcl_rayting_post as $user){
			$userslst[$user->user] = $user->user;
			$postslst[$user->post] = $user->post;
		}
		
		$b = 0;
		foreach((array)$userslst as $id){
			if(++$b>1) $uslst .= ',';
			$uslst .= $id;
		}
		
		$display_names = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix."users WHERE ID IN ($uslst)");
		
		foreach((array)$display_names as $name){
			$names[$name->ID] = $name->display_name;
		}
		
		$b = 0;
		foreach((array)$postslst as $id){
			if(++$b>1) $plst .= ',';
			$plst .= $id;
		}
		
		$postdata = $wpdb->get_results("SELECT ID,post_title FROM ".$wpdb->prefix."posts WHERE ID IN ($plst)");
		
		foreach((array)$postdata as $p){
			$title[$p->ID] = $p->post_title;
		}
		
		foreach((array)$rcl_rayting_post as $post){
			if($post->author_post==$id_user){
				$n++;
					$rayt = $post->status;				
						$recall_votes .= '<li><a target="_blank" href="'.get_author_posts_url($post->user).'">'.$names[$post->user].'</a> оценил: '.raytout($rayt).' запись <a href="/?p='.$post->post.'">'.$title[$post->post].'</a></li>';
			}
		}

		$recall_votes .= '</ul>';
		
		if($n!=0){
			$log['otvet']=100;
			$log['iduser']=$id_user;
			$log['votes']=$recall_votes;			
		}else{
			$log['otvet']=100;
			$log['iduser']=$id_user;
			$log['votes']='<div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div><input type="button" id="view-rayt-comments-'.$id_user.'" class="view-rayt-comments recall-button" value="Рейтинг комментариев"><ul><p>У пользователя нет записей</p><ul>';
		}
		echo json_encode($log);
		exit;
	}
	function get_vote_user_recall(){
		global $wpdb,$user_ID,$rcl_options;
		if(!$user_ID){
			$log['otvet']=1;	
			echo json_encode($log);
			exit;
		}
		$id_user = $_POST['iduser'];
		
			$recall_votes = '<div id="votes-user-'.$id_user.'" class="float-window-recall">			
		 <div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div>
		 <input type="button" id="view-rayt-posts-'.$id_user.'" class="view-rayt-posts recall-button" value="Рейтинг записей">
			<ul class="rayt-list-user">';
			$n=0;
			
			$rcl_comments_rayt = $wpdb->get_results("SELECT user,comment_id,author_com,rayting FROM ".RCL_PREF."rayting_comments WHERE author_com = '$id_user' ORDER BY ID DESC");
			
			$names = get_names_array_rcl($rcl_comments_rayt,'user');
			
			foreach((array)$rcl_comments_rayt as $comments){
				
				$n++;
					$rayt = $comments->rayting;				
						$recall_votes .= '<li>'.$comments->ID.'<a target="_blank" href="'.get_author_posts_url($comments->user).'">'.$names[$comments->user].'</a> оценил: '.raytout($rayt).' <a href="'.get_comment_link( $comments->comment_id ).'">отзыв</a> к записи</li>';

			}
			if($n==0){
			
			$rcl_rayting_post = $wpdb->get_results("SELECT user,post,status,author_post FROM ".RCL_PREF."rayting_post WHERE author_post = '$id_user' ORDER BY ID DESC");
			
			foreach((array)$rcl_rayting_post as $user){
				$userslst[$user->user] = $user->user;
				$postslst[$user->post] = $user->post;
			}
			
			$b = 0;
			foreach((array)$userslst as $id){
				if(++$b>1) $uslst .= ',';
				$uslst .= $id;
			}
			
			$display_names = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix."users WHERE ID IN ($uslst)");
			
			foreach((array)$display_names as $name){
				$names[$name->ID] = $name->display_name;
			}
			
			$b = 0;
			foreach((array)$postslst as $id){
				if(++$b>1) $plst .= ',';
				$plst .= $id;
			}
			
			$postdata = $wpdb->get_results("SELECT ID,post_title FROM ".$wpdb->prefix."posts WHERE ID IN ($plst)");
			
			foreach((array)$postdata as $p){
				$title[$p->ID] = $p->post_title;
			}
			
			$recall_votes = '<div id="votes-user-'.$id_user.'" class="float-window-recall">			
			 <div align="right" id="close-votes-'.$id_user.'" class="close-votes">X</div>
			 <input type="button" id="view-rayt-comments-'.$id_user.'" class="view-rayt-comments recall-button" value="Рейтинг комментариев">
			<ul class="rayt-list-user">';
				foreach((array)$rcl_rayting_post as $post){
					if($post->author_post==$id_user){
					$n++;
						$rayt = $post->status;				
							$recall_votes .= '<li><a target="_blank" href="'.get_author_posts_url($post->user).'">'.$names[$post->user].'</a> оценил: '.raytout($rayt).' запись <a href="/?p='.$post->post.'">'.$title[$post->post].'</a></li>';
					}
				}
			}
			$recall_votes .= '</ul></div>';
		
		if($n!=0){
			$log['otvet']=100;
			$log['iduser']=$id_user;
			$log['votes']=$recall_votes;			
		}else{
			$log['otvet']=1;
		}
		echo json_encode($log);
		exit;
	}
	function cancel_rayt_rcl(){
		global $wpdb,$user_ID,$rcl_options;
		$type = $_POST['type'];
		$id = $_POST['id'];
		if($type=='comment'){
			$comrayt = $wpdb->get_row(" SELECT * FROM ".RCL_PREF."rayting_comments WHERE comment_id='$id' AND user='$user_ID'");
			if(!$comrayt) return false;
			$id_user = $comrayt->author_com;
			$newrayt = cancel_comment_rayt_rcl($id_user,$comrayt->comment_id,$comrayt->rayting);
		}
		if($type=='post'){
			$postrayt = $wpdb->get_row(" SELECT * FROM ".RCL_PREF."rayting_post WHERE post='$id' AND user='$user_ID'");
			if(!$postrayt) return false;
			$id_user = $postrayt->author_post;
			$newrayt = cancel_post_rayt_rcl($id_user,$postrayt->post,$postrayt->status);
		}
		$log['result']=100;
		$log['type']=$type;
		$log['idpost']=$id;
		$log['rayt']=raytout($newrayt);
		echo json_encode($log);
	exit;
	}
}
?>