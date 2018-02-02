<?php
class rcl_feed{

    public function __construct() {
		
		add_filter('file_scripts_rcl',array(&$this, 'get_scripts_feed_rcl'));
		add_filter('feed_comment_text_rcl','add_rayting_comment',10,2);
		
		if (!is_admin()):
			add_action('rcl_sidebar_user',array(&$this, 'add_feed_button_user_lk'),5,2);
			if(function_exists('add_shortcode')) add_shortcode('feed',array(&$this, 'last_post_and_comments_feed'));
			add_filter('rcl_footer_user',array(&$this, 'get_footer_lk_rcl'),10,2);
		endif;
    }
	
	
	function get_footer_lk_rcl($footer_lk,$author_lk){
		global $rcl_options;
		global $user_ID;
		
		if($user_ID&&$user_ID!=$author_lk){ 
			if(!get_usermeta($user_ID, 'feed_user_'.$author_lk, true)) $feed_status = 'Подписаться на пользователя';
				else $feed_status = 'Отписаться';
			$footer_lk .= '<div id="feed-control" class="alignright"><input class="feed-user recall-button" id="feed-'.$author_lk.'" type="button" value="'.$feed_status.'"></div>';
		}
		return $footer_lk;
	}
	
	function get_feedout_button($user_id){
		return '<div id="feed-control" class="alignright"><input class="feed-user recall-button" id="feed-'.$user_id.'" type="button" value="Отписаться"></div>';
	}

	
	function add_feed_button_user_lk($sidebar_lk,$author_lk){
		global $wpdb;
		global $user_ID;
		
			$feed_count = $wpdb->get_var("SELECT count(umeta_id) FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'feed_user_$author_lk'");		
			if($user_ID==$author_lk){
				$users_group = $wpdb->get_row("SELECT umeta_id FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'feed_user_%' AND user_id = '$user_ID'");
				if($users_group) $yours_feed = '<div class="group_content"></div><p class="all-users-feed">Мои подписки</p>';
			}
			if($feed_count==0) $feed_info = '<p align="center"><b>Подписчиков: <span id="feed-count">'.$feed_count.'</span></b></p>'.$yours_feed;
			else $feed_info = '<p align="center"><b><a class="count_users_feed" id="user-feed-'.$author_lk.'">Подписчиков: <span id="feed-count">'.$feed_count.'</span></a></b></p>'.$yours_feed;
			
			if($feed_info) $sidebar_lk .= $feed_info;
			
			return $sidebar_lk;
	}

	
	function last_post_and_comments_feed(){
		
		global $user_ID;
		global $wpdb;
		
		if(!$user_ID){
			$feedlist = '<p align="center">Войдите или зарегистрируйтесь<br />для просмотра последних публикаций и комментариев<br />от пользователей, на которых у вас будет<br />оформлена подписка.</p>';
			return $feedlist;
		}

		$comments_feed = $this->get_comments_feed();		

		$feedlist = '<p align="right" id="feed-button">
		<input type="button" class="recall-button get-feed active" id="commentfeed" value="Комментарии"> 
		<input type="button" class="recall-button get-feed" id="postfeed" value="Публикации"></p>
		<span class="loader"></span>
		<div id="feedlist">';
		
		if(!$comments_feed){
			$feedlist .= '<h3 align="center">Нет ни одного комментария от пользователей на которых вы подписаны, а также ни одного ответа на ваши комментарии. Больше активности!</h3>';
			$feedlist .= '</div>';	
			return $feedlist;
		}
		
		$feedlist .= '<h2>Комментарии</h2>';	
		$feedlist .= $this->feed_comment_loop($comments_feed);
			
		$feedlist .= '</div>';	
			
		return $feedlist;
		
		
	}

	function feed_comment_loop($comments_feed){

		global $user_ID,$wpdb;
		
		$comments_children = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."comments WHERE comment_parent > 0 ORDER BY comment_date DESC LIMIT 40");
		
		foreach((array)$comments_feed as $c){
			if(++$a>1) $postsids .= ',';
			$postsids .= $c->comment_post_ID;
		}
		
		$posts_title = $wpdb->get_results("SELECT ID,post_title FROM ".$wpdb->prefix ."posts WHERE ID IN ($postsids)");
		
		foreach((array)$posts_title as $p){
			$titles[$p->ID] = $p->post_title;
		}
		
		foreach((array)$comments_feed as $comment){
				if($comment->user_id==$user_ID){ //если автор комментария я сам, то проверяю на наличие дочерних комментариев
					
					$children = false;
					foreach((array)$comments_children as $child_com){
						if($child_com->comment_parent==$comment->comment_ID){ 
						$children = $child_com;
						break;
						}
					}
					
					if($children){ //если есть, то вывожу свой и дочерний
					
						$feedlist .= $this->get_feed_comment($comment,$titles,1);
						
						$feedlist .= '<div class="comment-child">';
						$feedlist .= $this->get_feed_comment($children,'',2);
						$feedlist .='</div>';
					}
				}else{ //если автор комментария не я
					if($comment->comment_parent!=0){ //то проверяю, есть ли является ли он дочерним комментарием
						$parent = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."comments WHERE comment_ID = '$comment->comment_parent' ");
						if($parent->user_id!=$user_ID){ //если автор родительского комментария не я, то вывожу
							$feedlist .= $this->get_feed_comment($comment,$titles,1);
						}
					}else{ //если комментарий не дочерний, то вывожу
						$feedlist .= $this->get_feed_comment($comment,$titles,1);
					}			
				}
			}
			
		return $feedlist;
	}

	function get_feed_comment($comment,$titles,$status){
		global $user_ID;
		
		$feedlist = '<div id="feed-comment-'.$comment->comment_post_ID.'" class="feedcomment">
		<div class="feed-author-avatar"><a href="'.get_author_posts_url($comment->user_id).'">'.get_avatar($comment->user_id,50).'</a></div>';
		if($status==1) $feedlist .= '<h3 class="feed-title">к записи: <a href="/?p='.$comment->comment_post_ID.'">'.$titles[$comment->comment_post_ID].'</a></h3>';
		else $feedlist .= '<h4 class="recall-comment">в ответ на ваше сообщение</h4>';
		$feedlist .= '<small>'.date('d.m.Y H:i', strtotime($comment->comment_date)).'</small>';
		
		$comment_content = apply_filters('feed_comment_text_rcl',$comment->comment_content,$comment);
		
		$feedlist .= '<div class="feed-content">'.$comment_content.'</div>';
		if($comment->user_id!=$user_ID) $feedlist .= '<p align="right"><a target="_blank" href="/?p='.$comment->comment_post_ID.'#comment-'.$comment->comment_ID.'">Ответить</a></p>';
		$feedlist .= '</div>';
		
		return $feedlist;
	}

	/*************************************************
	Получаем всех своих подписчиков
	*************************************************/	
	function get_all_your_feed_users(){
		global $wpdb;
		global $user_ID;		
		if($user_ID){
			//require_once('../../ajax-data/avatar.php');
			$userid = $_POST['userid'];		
			$users_feed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'feed_user_".$userid."'");
			if($users_feed){
			
				$names = get_names_array_rcl($users_feed,'user_id');
			
				$feed_list = '<div id="users-feed-'.$userid.'" class="users-feed">			
			 <div align="right" id="close-votes-'.$userid.'" class="close-votes">X</div>
				<div>';
				foreach((array)$users_feed as $user){
					$feed_list .= '<a href="'.get_author_posts_url($user->user_id).'" title="'.$names[$user->user_id].'">'.get_avatar($user->user_id,50).'</a>';
				}
				$feed_list .= '</div></div>';
				
				$log['otvet']=100;
				$log['user_id']=$userid;
				$log['feed-list']=$feed_list;			
			}		
		} else {
			$log['otvet']=1;
		}	
		echo json_encode($log);
		exit;
	}
	
	/*************************************************
	Смотрим всех пользователей в своей подписке
	*************************************************/
	function get_all_users_feed_recall(){
		global $wpdb;
		global $user_ID;	
		$users_feed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."usermeta WHERE meta_key LIKE 'feed_user_%' AND user_id = '$user_ID'");
		if(!$users_feed){
			$log['recall']=1;
			echo json_encode($log);
			exit;
		}

		$names = get_names_array_rcl($users_feed,'meta_value');
		
		$feed_list = '<div id="users-feed-'.$user_ID.'" class="users-feed">			
			 <div align="right" id="close-votes-'.$user_ID.'" class="close-votes">X</div>
				<div>';
		foreach((array)$users_feed as $user){
			$feed_list .= '<a href="'.get_author_posts_url($user->meta_value).'" title="'.$names[$user->meta_value].'">'.get_avatar($user->meta_value,50).'</a>';
		}
		$feed_list .= '</div></div>';
				
		$log['recall']=100;
		$log['user_id']=$user_ID;
		$log['feed-list']=$feed_list;
		echo json_encode($log);
		exit;
	}
	
	/*************************************************
	Подписываемся на пользователя
	*************************************************/
	function add_feed_user_recall(){
		global $user_ID;
		
		$id_user = $_POST['id_user'];
		
		if($user_ID){
			$feed = get_usermeta($user_ID,'feed_user_'.$id_user);
			if(!$feed){ 
				$res = update_usermeta($user_ID, 'feed_user_'.$id_user, $id_user);
				
				if($res){
					do_action('rcl_on_user_feed',$user_ID,$id_user);
					
					$log['int']=100;
					$log['count']=1;
					$log['recall'] = '<input class="recall-button feed-user" id="feed-'.$id_user.'" type="button" value="Отписаться">';
				}
			}else{ 
				delete_usermeta($user_ID,'feed_user_'.$id_user);
				
				do_action('rcl_off_user_feed',$user_ID,$id_user);
				
				$log['int']=100;
				$log['count']=-1;
				$log['recall'] = '<input class="recall-button feed-user" id="feed-'.$id_user.'" type="button" value="Подписаться на пользователя">';
			}		
		}	 		
		echo json_encode($log);	
		exit;
	}
	
	/*************************************************
	Получаем комментарии из фида
	*************************************************/
	function get_comments_feed_recall(){
		global $user_ID;
		global $wpdb;
		
		if($user_ID){
		
			$comments_feed = $this->get_comments_feed();		
					
			if(!$comments_feed){
				$res['int'] = 100;
				$res['recall'] = '<h3>Похоже, что вы еще не оставили ни одного комментария или не являетесь подписчиком.</h3><p>Комментируйте публикации и подписывайтесь на других пользователей, тогда вы сможете здесь отслеживать ответы на ваши комментарии и видеть новые комментарии от пользователей которые вам интересны.</p>';
				echo json_encode($res);	
				exit;
			}
						
			foreach((array)$comments_feed as $c){
				if(++$a>1) $postsids .= ',';
				$postsids .= $c->comment_post_ID;
			}
			
			$posts_title = $wpdb->get_results("SELECT ID,post_title FROM ".$wpdb->prefix ."posts WHERE ID IN ($postsids)");
			
			foreach((array)$posts_title as $p){
				$titles[$p->ID] = $p->post_title;
			}

			$n++;
			$feedlist .= '<h2>Комментарии</h2>';			
			$feedlist .= $this->feed_comment_loop($comments_feed);			
			$res['int'] = 100;
			$res['recall'] = $feedlist;

		}
		 
		echo json_encode($res);	
		exit;
	}
	
	function get_comments_feed(){
		global $wpdb,$user_ID;	
	
		$feed_users = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE user_id = '$user_ID' AND meta_key LIKE 'feed_user_%' ");	
		$n=0;
		$feeds = $user_ID;
		foreach((array)$feed_users as $user){ 
				$feeds .= ','.$user->meta_value;			
		}
		
		$comments_feed = $wpdb->get_results("
				SELECT 
					cts.comment_ID,cts.comment_parent,cts.user_id,cts.comment_post_ID,cts.comment_content,cts.comment_date
				FROM 
					".$wpdb->prefix."comments as cts 
				INNER JOIN 
					".$wpdb->prefix."commentmeta as cm on (cts.comment_ID = cm.comment_id) 
				WHERE
					cts.user_id IN($feeds) && cm.meta_key!='_wp_trash_meta_status' && cts.comment_approved = '1'
				GROUP BY cts.comment_ID ORDER BY cts.comment_date DESC LIMIT 40");
				
		if(!$comments_feed) $comments_feed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."comments WHERE user_id IN ($feeds) && comment_approved = '1' ORDER BY comment_date DESC LIMIT 40");
		
		return $comments_feed;
	}
	
	/*************************************************
	Получаем публикации из фида
	*************************************************/
	function get_posts_feed_recall(){
		global $user_ID;
		global $wpdb;
		
		if($user_ID){
			
			$feed_users = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE user_id = '$user_ID' AND meta_key LIKE 'feed_user_%' ");
			
			if($feed_users){
				$n=0;
				foreach((array)$feed_users as $user){
				$n++;
					if($n>1) 
						$feeds .= ','.$user->meta_value;
						else 
							$feeds .= $user->meta_value;
				}
				
				$posts_users = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."posts WHERE 
				post_author IN($feeds) AND post_type IN ('post','post-group','video') AND post_status = 'publish' ORDER BY post_date DESC LIMIT 15");
				
				$admin_groups = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'admin_group_%' AND user_id = '$user_ID'");
				$user_groups = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix ."usermeta WHERE meta_key LIKE 'user_group_%' AND user_id = '$user_ID'");
				
				foreach($admin_groups as $ad){
					$group_ar[$ad->meta_value] = $ad->meta_value;
				}
				foreach($user_groups as $us){
					$group_ar[$us->meta_value] = $us->meta_value;
				}
				if($group_ar){
					foreach($group_ar as $gr){
						if(++$a>1) $group_list .= ',';
						$group_list .= $gr;
					}
					$where .= "a.term_taxonomy_id IN($group_list) &&";
				}			
				$posts_groups = $wpdb->get_results("
					SELECT 
						b.ID,b.post_title,b.post_date,b.post_content,b.post_author,b.post_type
					FROM 
						".$wpdb->prefix."term_relationships as a 
					INNER JOIN 
						".$wpdb->prefix."posts as b on (b.ID = a.object_id) 
					WHERE
						$where b.post_type='post-group' && b.post_status = 'publish' && b.post_author != '$user_ID'
					ORDER BY post_date DESC LIMIT 15");
					
				include('../video-gallery/class_video.php');
					
				foreach($posts_users as $posts_us){
					$posts_list[$posts_us->ID] = (array)$posts_us;
				}
				foreach($posts_groups as $posts_gr){
					$posts_list[$posts_gr->ID] = (array)$posts_gr;
				}
				
				$posts_list = array_multisort_key_rcl($posts_list, 'post_date', SORT_DESC);

				$feedlist .= '<h2>Публикации</h2>';
				foreach((array)$posts_list as $post){ 
					$feedlist .= '<div id="feed-post-'.$post['ID'].'" class="feed-post">';	
					
					$post_content = $post['post_content'];
					
					if(strlen($post_content) > 400){
						$post_content = substr($post_content, 0, 400);
						$post_content = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ... <a href="/?p='.$post['ID'].'">далее</a>', $post_content);
					}
					$feedlist .= '<div class="feed-author-avatar"><a href="'.get_author_posts_url($post['post_author']).'">'.get_avatar($post['post_author'],50).'</a></div>';
					$feedlist .= '<h3 class="feed-title"><a href="/?p='.$post['ID'].'">'.$post['post_title'].'</a></h3><small>'.date('d.m.Y H:i', strtotime($post['post_date'])).'</small>';
					
					if( has_post_thumbnail($post['ID']) ) {  
						$feedlist .= get_the_post_thumbnail( $post['ID'], 'medium', 'class=aligncenter' );  
					}
					if($post['post_type']=='video'){
						$data = explode(':',$post['post_excerpt']);
						$video = new VD_rcl();	
						$video->service = $data[0];
						$video->video_id = $data[1];
						$video->height = 300;
						$video->width = 450;
						$feedlist .= '<div class="video-iframe aligncenter">'.$video->get_video_window().'</div>';
					}
					$feedlist .= '<div class="feed-content">'.$post_content.'</div>';
					//$feedlist .= $this->get_feedout_button($post['post_author']);
					$feedlist .= '<div class="feed-comment">Комментариев ('.get_comments_number( $post['ID'] ).')</div>';
					$feedlist .= '</div>';
				}
				
				$res['int'] = 100;
				$res['recall'] = $feedlist;
			
			}else{
				$res['int'] = 100;
				$res['recall'] = '<h3>Вы еще не подписаны ни на чьи публикации.</h3><p>Зайдите на страницу интересного пользователя и нажмите кнопку "Подписаться" и вы сможете отслеживать его последние публикации здесь.</p>';
			}
		}
		echo json_encode($res);	
		exit;
	}
	
	function get_scripts_feed_rcl($script){
		
		$ajaxdata = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-admin/admin-ajax.php',";
		$ajaxfile = "type: 'POST', data: dataString, dataType: 'json', url: '".get_bloginfo('wpurl')."/wp-content/plugins/recall/add-on/feed/ajax-request.php',";
		
		$script .= "
			/* Смотрим всех пользователей в своей подписке */
				jQuery('.all-users-feed').live('click',function(){	
					var dataString = 'action=get_all_users_feed_recall&user_ID='+user_ID;
					jQuery.ajax({
						".$ajaxfile."
						success: function(data){
							if(data['recall']==100){
								jQuery('.all-users-feed').after(data['feed-list']);
								jQuery('#users-feed-'+data['user_id']).slideDown(data['feed-list']);						 
							} else {
								alert('Ошибка!');
							}
						} 
					});	  	
				return false;
				});
			/* Получаем всех своих подписчиков */
				jQuery('.count_users_feed').live('click',function(){			
						var userid = parseInt(jQuery(this).attr('id').replace(/\D+/g,''));	
						var dataString = 'action=get_all_your_feed_users&userid='+userid+'&user_ID='+user_ID;
						jQuery.ajax({
							".$ajaxfile."
							success: function(data){
								if(data['otvet']==100){
									jQuery('#user-feed-'+data['user_id']).after(data['feed-list']);
									jQuery('#users-feed-'+data['user_id']).slideDown(data['feed-list']);
								}else{
									alert('Авторизуйтесь, чтобы смотреть подписчиков пользователя!');
								}
							} 
						});	  	
						return false;
					});
			/* Подписываемся на пользователя */
				jQuery('.feed-user').live('click',function(){
					var id_user = parseInt(jQuery(this).attr('id').replace(/\D+/g,''));
					var dataString = 'action=add_feed_user_recall&id_user='+id_user+'&user_ID='+user_ID;
					jQuery.ajax({
						".$ajaxfile."
						success: function(data){
							if(data['int']==100){				
								 jQuery('#feed-control').empty().html(data['recall']);
								 var feed_count = jQuery('#feed-count').html();
								 feed_count = parseInt(feed_count) + parseInt(data['count']);
								 jQuery('#feed-count').html(feed_count);
							} else {
								alert('Ошибка!');
							}
						} 
					});	  	
					return false;
				});
			/* Получаем комментарии из фида */
				jQuery('#commentfeed').live('click',function(){
					if(jQuery(this).hasClass('active')) return false;
					jQuery('.get-feed').removeClass('active');
					jQuery(this).addClass('active');
					jQuery('.loader').html('<img src=\'/wp-content/plugins/recall/img/loader.gif\'>');
					jQuery('#feedlist').slideUp();
					var dataString = 'action=get_comments_feed_recall&user_ID='+user_ID;
					jQuery.ajax({
						".$ajaxfile."
						success: function(data){
							if(data['int']==100){									
								jQuery('#feedlist').delay(1000).queue(function () {jQuery('#feedlist').html(data['recall']);jQuery('#feedlist').dequeue();});	
								jQuery('#feedlist').slideDown(1000);							
								jQuery('.loader').delay(1000).queue(function () {jQuery('.loader').empty();jQuery('.loader').dequeue();});
							} else {
								alert('Ошибка!');
							}
						} 
					});	  	
					return false;
				});
			/* Получаем публикации из фида */
				jQuery('#postfeed').live('click',function(){
					if(jQuery(this).hasClass('active')) return false;
					jQuery('.get-feed').removeClass('active');
					jQuery(this).addClass('active');
					jQuery('.loader').html('<img src=\'/wp-content/plugins/recall/img/loader.gif\'>');
					jQuery('#feedlist').slideUp();
					var dataString = 'action=get_posts_feed_recall&user_ID='+user_ID;
					jQuery.ajax({
						".$ajaxfile."
						success: function(data){
							if(data['int']==100){									
								jQuery('#feedlist').delay(1000).queue(function () {jQuery('#feedlist').html(data['recall']);jQuery('#feedlist').dequeue();});	
								jQuery('#feedlist').slideDown(1000);
								jQuery('.loader').delay(1000).queue(function () {jQuery('.loader').empty();jQuery('.loader').dequeue();});
							} else {
								alert('Ошибка!');
							}
						} 
					});	  	
					return false;
				});";
		return $script;
	}
}
$rcl_feed = new rcl_feed();
?>