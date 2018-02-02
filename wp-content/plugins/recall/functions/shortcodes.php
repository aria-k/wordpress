<?php
/*ФОРМА РЕГИСТРАЦИИ И ВХОДА*/
function register_button_new_user(){
global $user_ID,$rcl_user_URL,$rcl_options;
	$lgn_frm = $rcl_options['login_form_recall'];
	 $get_fields = get_option( 'custom_profile_field' );
	 $get_fields = unserialize( $get_fields);
		
		if($get_fields){
		$number_field=0;
			$field = '			
			<table class="form-table">';
			foreach((array)$get_fields as $custom_field){
				if($custom_field['register']==1){				
				$slug = str_replace('-','_',$custom_field['slug']);
				if($custom_field['requared']==1) $requared = ' <span class="req-star">*</span> ';
					else $requared = '';
				$field .= '<tr><td><label for="pass1">'.$custom_field['title'].$requared.':</label></th>';
				if($custom_field['type']=='text')
					$field .= '<td><input type="text" name="'.$slug.'" class="regular-text" id="'.$slug.'" maxlength="50" value="" /><br/></td>';
				if($custom_field['type']=='textarea')
					$field .= '<td><textarea name="'.$slug.'" class="regular-text" id="'.$slug.'" rows="5" cols="50"></textarea></td>';
				if($custom_field['type']=='select'){
					$fields = explode('#',$custom_field['field_select']);
					$count_field = count($fields);
					for($a=0;$a<$count_field;$a++){
						$field_select .='<option value="'.$fields[$a].'">'.$fields[$a].'</option>';
					}
					$field .= '<td><select name="'.$slug.'" class="regular-text" id="'.$slug.'">
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
			$field .= '</table>';			
		}
	if(!$user_ID){
		if($lgn_frm==1){
			echo '<style>
			.regform {
			margin: 0 auto!important;
			display:block!important;
			position: inherit!important;
			}
			.register-button{
			width:100%!important;
			}
			</style>';
		}
	echo "<div class='backform'></div>
	<div id='regform-rcl' class='regform'>
	<div id='register-form'>";
	if(!$lgn_frm) echo "<div class='close-button close'></div>";
	echo "<div class='title-register'>Регистрация на сайте</div>";
	if(!$lgn_frm) echo "<div class='arrow arrow-register' id='arrow-register'></div>";
	echo "<div class='title-sign'>Вход на сайт</div>";
	if(!$lgn_frm) echo "<div class='arrow arrow-sign' id='arrow-sign'></div>";
	echo "<div id='regrequest'></div>
	<div class='registerform'>
	 <h3>Впишите свой логин <span class='req-star'>*</span>:</h3>
	 <input class='in_field' type='text' name='login_user' id='login_user' placeholder='Ваш логин (латиница) *' onfocus='if(this.placeholder==\"Ваш логин (латиница) *\")this.placeholder=\"\";' onblur='if(this.placeholder==\"\")this.placeholder=\"Ваш логин (латиница) *\";' value='' class='input'/>
	 <h3>Адрес вашей электронной почты <span class='req-star'>*</span>:</h3>
	 <input class='in_field' type='text' name='user_email' value='' id='user_email' class='input' />
	 <div class='pass-input' style='float:left;'>
	 <h3>Пароль <span class='req-star'>*</span>:</h3>
	 <input class='in_field' type='password' name='user_pass' id='user_pass' value='' class='input'/>
	 </div>
	 <div class='pass-input'>
	 <h3>Повторите ваш пароль  <span class='req-star'>*</span>:</h3>
	 <input class='in_field' type='password' name='pass_confirm' id='pass_confirm' value='' class='input'/>
	 </div>
	 <input class='in_field' type='text' name='fio_user' id='fio_user' placeholder='Фамилия и Имя' onfocus='if(this.placeholder==\"Фамилия и Имя\")this.placeholder=\"\";' onblur='if(this.placeholder==\"\")this.placeholder=\"Фамилия и Имя\";' value='' class='input'/>";
	 
	 if($field) echo $field;
	 
	 //do_action( 'register_form' );
	 
	 echo "<div class='block-button'>";
	 if(!$lgn_frm){
		 echo "<div class='register-button' style='float:right;'>
		 <input type='button' class='closeform close' value='Отмена' id='cancel' />
		 </div>";
	 }
	 
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
	 
	 echo "<div class='register-button'>
	 <input type='button' value='Регистрация' id='register' />
	 </div>
	 </div>
	 </div>
	 <div class='signform'>
	 <h3>Логин:</h3>
	 <input class='in_field' type='text' name='user_login_sign' value='' id='user_login_sign' class='input' />
	 <div class='sign-pass-input'>
	 <h3>Пароль:</h3>
	 <input class='in_field' type='password' name='user_pass_sign' id='user_pass_sign' value='' class='input'/>
	 <input type='hidden' id='referer-rcl' name='referer-rcl' value='http://".$host."'>
	 </div>";

	do_action( 'login_form' );
	 
	 echo "<div class='block-button'>";
	 if(!$lgn_frm){
		 echo "<div class='register-button' style='float:right;'>
		 <input type='button' class='closeform close' value='Отмена' id='cancel' />
		 </div>";
	 }
	 echo "<div class='register-button'>
	 <input type='button' value='Отправить' id='sign' />
	 </div>
	 </div>
	 </div>
	 </div>
	 </div>";
	 echo '<script>
		jQuery(function(){
			jQuery("#register").click(function(){';
	if($name_value){		 
		foreach((array)$name_value as $value){
			if($value['chek']){
				echo 'if(jQuery("#'.$value['chek'].'").attr("checked")=="checked") var '.$value['chek'].' = jQuery("#'.$value['chek'].'").attr("value");';
				$reg_request .= '+"&'.$value['chek'].'="+'.$value['chek'];
			}
			if($value['radio']){
				echo 'if(jQuery("#'.$value['radio']['id'].'").attr("checked")=="checked") var '.$value['radio']['name'].' = jQuery("#'.$value['radio']['id'].'").attr("value");';
				$reg_radio = '+"&'.$value['radio']['name'].'="+'.$value['radio']['name'];
			}
			if($value['other']){
				echo 'var '.$value['other'].' = jQuery("#'.$value['other'].'").attr("value");';
				$reg_request .= '+"&'.$value['other'].'="+'.$value['other'];
			}
		}
	}
	
	echo '
				var pass_confirm = jQuery("#pass_confirm").val();
				var user_pass = jQuery("#user_pass").val();
				var user_email = jQuery("#user_email").val();
				var login_user = jQuery("#login_user").val();
				var fio_user = jQuery("#fio_user").val();
				
				var dataString_reg = "action=register_user_recall&pass_confirm="+pass_confirm+"&user_pass="+user_pass+"&user_email="+user_email+"&login_user="+login_user+"&fio_user="+fio_user'.$reg_request.$reg_radio.';
				register_request_new_user_recall(dataString_reg);
			});
		});	 
	</script>';

	}
}
add_shortcode('loginform','register_button_new_user');

add_shortcode('userlist','short_user_list_rcl');
function short_user_list_rcl($atts, $content = null){
    global $post,$wpdb,$rcl_options,$rcl_addons,$user_ID;  
           
    //режим поиска пользователей по имени или логину
    if($_POST['search-user']){
	
		//$args = apply_filters('search_filter_rcl',$args);
		//print_r($args); exit;
		if(!$args){
			if($_POST['orderuser']==1){ //по имени
				$args = array(
					'meta_query'   => array(
						'relation' => 'OR',
						array(  
							'key' => 'first_name',  
							'value' => $_POST['name-user'],  
							'compare' => 'LIKE',  
						),
						array(  
							'key' => 'last_name',  
							'value' => $_POST['name-user'],  
							'compare' => 'LIKE',  
						)
					)
				);
			}else{ //по логину			
				$args = array( 'search'=>'*'.$_POST['name-user'].'*');
			}
        } 
		
        //параметр шорткода на запрет отображения пользователя
        extract(shortcode_atts(array(
            'exclude' => 0,
			'type' => 'rows',
			'search' => 'yes'
        ),
        $atts));
                   
        //получение найденных ID
        $users = get_users($args);             
                   
        foreach($users as $user){
            if(++$a>1)$us_lst .= ',';
            $us_lst .= $user->ID;
        }
        
		$count_user = count($users);

        //получение списка найденных ID по посещаемости
        $rcl_action_users = $wpdb->get_results("SELECT user,time_action FROM ".RCL_PREF."user_action WHERE (user IN ($us_lst)) AND (user NOT IN ($exclude)) ORDER BY time_action DESC");
		foreach($rcl_action_users as $us){
			$us_list[$us->user]['action'] = $us->time_action;
		}
		$rayt_users = $wpdb->get_results("SELECT user_id,total FROM ".RCL_PREF."total_rayting_users WHERE (user_id IN ($us_lst)) AND (user_id NOT IN ($exclude))");
		foreach($rayt_users as $rt){
			$us_list[$rt->user_id]['rayting'] = $rt->total;				
		}
    }else{
        //параметры шорткода по умолчанию
         extract(shortcode_atts(array(
            'inpage' => 10,        
            'orderby' => 'registered',
			'exclude' => 0,
			'order' => 'DESC',
			'type' => 'rows',
			'limit' => 0,
			'group' => 0,
			'search' => 'yes',
			'page' => ''
        ),
        $atts)); 
		
		if($page) $navi = $page;
		
		if($_GET['filter']) $orderby = $_GET['filter'];

		switch($orderby){
			case 'posts': $order_by = 'post_count'; break;
			case 'comments': $order_by = 'comments_count'; break;
			case 'rayting': $order_by = 'total'; break;
			case 'action': $order_by = 'time_action'; break;
			case 'registered': $order_by = 'user_registered'; break;
			case 'display_name': $order_by = 'display_name'; break;
		}
                   
        //подсчет разрешенных для отображения пользователей
		if($group){	
			$gr = new Rcl_Group();	
			$count_user = $gr->users_count;
		}else{
			$count_user = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix ."users WHERE ID NOT IN ($exclude)");
		}
		
		$rclnavi = new RCL_navi($inpage,$count_user,'&filter='.$orderby,$page);		
		if(!$limit) $limit_us = $rclnavi->limit();
		else $limit_us = $limit;
        //получение списка ID сортированных по посещаемости
        if($order_by == 'time_action'){
			$rcl_action_users = $wpdb->get_results("SELECT user,time_action FROM ".RCL_PREF."user_action WHERE user NOT IN ($exclude) ORDER BY $order_by $order LIMIT $limit_us");
			foreach($rcl_action_users as $us){
				$us_list[$us->user]['action'] = $us->time_action;
			}
			$a = 0;
			foreach((array)$us_list as $id=>$data){
				if(++$a>1)$us_lst .= ',';
				$us_lst .= $id;
			}
			$rayt_users = $wpdb->get_results("SELECT user_id,total FROM ".RCL_PREF."total_rayting_users WHERE (user_id IN ($us_lst)) AND (user_id NOT IN ($exclude))");
			foreach($rayt_users as $rt){
				$us_list[$rt->user_id]['rayting'] = $rt->total;				
			}
		}else if($order_by == 'total'){			
			$rayt_users = $wpdb->get_results("SELECT user_id,total FROM ".RCL_PREF."total_rayting_users WHERE user_id NOT IN ($exclude) ORDER BY $order_by $order LIMIT $limit_us");			
			foreach($rayt_users as $rt){
				$us_list[$rt->user_id]['rayting'] = $rt->total;				
			}
			$a = 0;
			foreach((array)$us_list as $id=>$data){
				if(++$a>1)$us_lst .= ',';
				$us_lst .= $id;
			}
			$users = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix ."users WHERE ID NOT IN ($exclude) ORDER BY $order_by $order LIMIT $limit_us");		
			$action_users = $wpdb->get_results("SELECT user,time_action FROM ".RCL_PREF."user_action WHERE (user IN ($us_lst)) AND (user NOT IN ($exclude))");
			foreach($action_users as $act){
				$us_list[$act->user]['action'] = $act->time_action;				
			}
		}else{
			
			if($group){
				$users = $wpdb->get_results("SELECT user_id FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'user_group_$group' LIMIT $limit_us");
				$uslist = get_names_array_rcl($users,'user_id');
				foreach($uslist as $id=>$n){
					$us_list[$id]['name'] = $n;
				}
				$admin_id = $wpdb->get_var("SELECT user_id FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'admin_group_$group'");
			}else if($order_by == 'comments_count'){
				$users = $wpdb->get_results("
					SELECT COUNT(user_id) AS comments_count, user_id, comment_author
					FROM (select * from ".$wpdb->comments." order by comment_ID desc) as pc
					WHERE user_id != '' AND comment_approved = 1 GROUP BY user_id ORDER BY $order_by $order LIMIT $limit_us"
				);
				foreach($users as $us){
					$us_list[$us->user_id]['name'] = $us->comment_author;
					$us_list[$us->user_id]['comments'] = $us->comments_count;
				}
			}else if($order_by == 'post_count'){
				$users = $wpdb->get_results("
					SELECT COUNT(post_author) AS post_count, post_author
					FROM (select * from ".$wpdb->posts." order by ID desc) as pc
					WHERE post_status = 'publish' GROUP BY post_author ORDER BY $order_by $order LIMIT $limit_us"
				);
				foreach($users as $us){
					$us_list[$us->post_author]['posts'] = $us->post_count;
				}
			}else{ 
				$users = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix ."users WHERE ID NOT IN ($exclude) ORDER BY $order_by $order LIMIT $limit_us");
				foreach($users as $us){
					$us_list[$us->ID]['name'] = $us->display_name;
				}
			}
			
			
			$a = 0;
			foreach((array)$us_list as $id=>$data){
				if(++$a>1)$us_lst .= ',';
				$us_lst .= $id;
			}
			$action_users = $wpdb->get_results("SELECT user,time_action FROM ".RCL_PREF."user_action WHERE (user IN ($us_lst)) AND (user NOT IN ($exclude))");
			foreach($action_users as $act){
				$us_list[$act->user]['action'] = $act->time_action;				
			}
			$rayt_users = $wpdb->get_results("SELECT user_id,total FROM ".RCL_PREF."total_rayting_users WHERE (user_id IN ($us_lst)) AND (user_id NOT IN ($exclude))");
			foreach($rayt_users as $rt){
				$us_list[$rt->user_id]['rayting'] = $rt->total;				
			}
		}
    }
	
	if($type=='rows'){
		$users_desc = $wpdb->get_results("SELECT user_id,meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id IN ($us_lst) AND meta_key = 'description'");	
		foreach($users_desc as $us_desc){
			$desc[$us_desc->user_id] = $us_desc->meta_value;
		}
	}
	
	$display_names = $wpdb->get_results("SELECT ID,display_name FROM ".$wpdb->prefix."users WHERE ID IN ($us_lst)");
	foreach((array)$display_names as $name){
		$names[$name->ID] = $name->display_name;
	}
                   
    //Форма поиска
	if($search == 'yes'){
		$userlist .='
        <form method="post" action="">
        <p class="alignright">Поиск пользователей: <input type="text" name="name-user" value="'.$_POST['name-user'].'">
        <select name="orderuser">
            <option '.selected($_POST['orderuser'],1,false).' value="1">по имени</option>
            <option '.selected($_POST['orderuser'],2,false).' value="2">по логину</option>
        </select>
        <input type="submit" class="recall-button" name="search-user" value="Найти"><br>
        </p>
        </form>';

		$userlist .='<h3>Всего пользователей: '.$count_user.'</h3>';
		
		$userlist .= '<p class="alignleft">Фильтровать по: 
			<a '.a_active($_GET['filter'],'action').' href="'.get_permalink($post->ID).'?filter=action">Активности</a> 
			<a '.a_active($_GET['filter'],'rayting').' href="'.get_permalink($post->ID).'?filter=rayting">Рейтингу</a> 
			<a '.a_active($_GET['filter'],'posts').' href="'.get_permalink($post->ID).'?filter=posts">Публикациям</a>
			<a '.a_active($_GET['filter'],'comments').' href="'.get_permalink($post->ID).'?filter=comments">Комментариям</a>
			<a '.a_active($_GET['filter'],'registered').' href="'.get_permalink($post->ID).'?filter=registered">Регистрации</a>
		</p>';
	}
        
			
    //Вывод списка пользователей
	$userlist .='<div class="userlist">';	

	$ref_rayt = get_option('incentive_referal');
	
    foreach((array)$us_list as $id=>$data){
		
		if($rcl_options['rayt_comment_user_rayt']==1||$rcl_options['rayt_post_user_rayt']==1||$ref_rayt==1){
			
			if(function_exists('get_rayting_block_rcl')) $rayt_user = get_rayting_block_rcl($data['rayting']);
		}
		$url = get_author_posts_url($id);
        
		if($type=='rows'){
			$last_action = last_user_action_recall($data['action']);
																					   
			if(!$last_action)
				$action = '<div class="status_author_mess online" style="background:url('.get_bloginfo('wpurl').'/wp-content/plugins/recall/img/minionline.gif) no-repeat center center;"></div>';
			else
				$action = '<div class="status_author_mess offline">не в сети '.$last_action.'</div>';

			$userlist .='<div class="user-single list-list">
			<div class="thumb-user"><a title="'.$names[$id].'" href="'.$url.'">'.get_avatar($id,70).'</a>'.$rayt_user.'</div>
			<div class="user-content-rcl">'.$action.'<a href="'.$url.'"><h3 class="user-name">'.$names[$id].'</h3></a>';
			
			if($desc[$id])$userlist .='<div class="ballun-status"><span class="ballun"></span><p class="status-user-rcl">'.esc_textarea($desc[$id]).'</p></div>';
			
			$cont = apply_filters('rcl_description_user',$cont,$id);		
			$userlist .= $cont;
			if($group&&$admin_id==$user_ID&&$id!=$user_ID) $userlist .='<p class="alignright"><a href="#" id="usergroup-'.$id.'" user-data="'.$id.'" group-data="'.$group.'" class="ban-group recall-button">Удалить из группы</a></p>';
			$userlist .='</div>';
			
			$userlist .='</div>';
			$cont = '';
		}
		if($type=='avatars'){			                           
			$userlist .='<div class="user-single avatars-list"><a title="'.$names[$id].'" href="'.$url.'">'.get_avatar($id,70).'</a>'.$rayt_user.'</div>';
		}
    }
                           
    $userlist .='</div>';
           
    //вывод постраничной навигации       
    if(!$_POST['search-user']&&!$limit) $userlist .= $rclnavi->navi();
           
    return $userlist;
} 

add_shortcode('wp-recall','get_wp_recall_shortcode');
function get_wp_recall_shortcode(){
	global $user_ID,$rcl_options;	
	$get='user';
	$user_LK = $user_ID;
	if($rcl_options['link_user_lk_rcl']!='') $get = $rcl_options['link_user_lk_rcl'];	
	if(isset($_GET[$get])) $user_LK = $_GET[$get];
	if(!$user_LK){		
		return '<h4>Чтобы начать пользоваться возможностями своего личного кабинета, авторизуйтесь или зарегистрируйтесь на этом сайте</h4>
		<div class="authorize-form-rcl">'.get_authorize_form_rcl().'</div>';
	}
	wp_recall($user_LK);	
}
?>