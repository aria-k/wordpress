<?php
global $rcl_options;
/*************************************************
Регистрация пользователя
*************************************************/
function register_user_recall(){


	global $wpdb,$rcl_options;
	$pass = $_POST['user_pass'];
	//$pass = $_POST['pass_confirm'];	
	$pass_confirm = $_POST['pass_confirm'];
	$email = $_POST['user_email'];	
	$login = $_POST['login_user'];
	$fio = $_POST['fio_user'];
	
	

	$get_fields = get_option( 'custom_profile_field' );
	//здесь пишет $get_fields = Array, т.е. get_option( 'custom_profile_field' ) - возвращает массив!
	$get_fields = unserialize( $get_fields);
	$requared = true;
	
	if($get_fields){	
		foreach((array)$get_fields as $custom_field){
						
			$slug = str_replace('-','_',$custom_field['slug']);//здесь пишет $slug = gruppa_15
			
			if($custom_field['requared']==1){
			
			//У нас здесь $custom_field['requared'] равно 1
				if($custom_field['type']=='checkbox'){
				
				//У нас здесь $custom_field['type'] равно text, поэтому ниже изложенный код не выполняется!
					$chek = explode('#',$custom_field['field_select']); //$chek тоже отсутствует!
					$count_field = count($chek);
					
					for($a=0;$a<$count_field;$a++){
						$slug_chek = $slug.'_'.$a; //а вот $slug_chek уже отсутствует!
						
						if($_POST[$slug_chek]=='undefined'){
							$requared = false;
						}else{
							$requared = true;
							break;
						}
					}
				}else{
					//if(!$_POST[$slug]) $requared = false;	
				}
			}
		}
	}
	
	if(!$pass||!$email||!$pass_confirm||!$login||!$requared){
	
	
		
		/*echo " =";
		 $pass;
		echo "<br/>";
		
		echo "$email =";
		echo $email;
		echo "<br/>";
		
		echo "$pass_confirm =";
		echo $pass_confirm;
		echo "<br/>";
		
		echo "$login =";
		echo $login;
		echo "<br/>";
		
		echo "$requared =";
		echo $requared;
		echo "<br/>"; */
		
		$res['int']=1;
		//$res['recall'] .= '<p style="text-align:center;color:red;">Заполните обязательные поля, отмеченные звездочкой!</p>';
		$res['recall'] .= $get_fields;
		echo json_encode($res);	
		exit;
	}
	
	$res_email = email_exists( $email );
	$res_login = username_exists($login);
	$correctemail = is_email($email);
	$valid = validate_username($login);
	if($res_login||$res_email||!$correctemail||$pass!=$pass_confirm||!$valid){
		if(!$valid){
			$res['int']=1;
			$res['recall'] .= '<p style="text-align:center;color:red;">В логине используются недопустимые символы!</p>';
		}
		if($res_login){
			$res['int']=1;
			$res['recall'] .= '<p style="text-align:center;color:red;">Этот логин уже используется!</p>';
		}
		if($res_email){
			$res['int']=1;
			$res['recall'] .= '<p style="text-align:center;color:red;">Этот email уже используется!</p>';
		}		
		if(!$correctemail){
			$res['int']=1;
			$res['recall'] .= '<p style="text-align:center;color:red;">Вы ввели некорректный email!</p>';
		}				
		if($pass!=$pass_confirm){
			$res['int']=1;
			$res['recall'] .= '<p style="text-align:center;color:red;">Введенные пароли не совпадают!</p>';
		}
	}else{					
			
			$userdata = array(
				'user_pass' => $pass //обязательно
				,'user_login' => $login //обязательно
				,'user_nicename' => ''
				,'user_email' => $email
				,'display_name' => $fio
				,'nickname' => $login
				,'first_name' => $fio
				,'rich_editing' => 'true'  // false - выключить визуальный редактор для пользователя.
			);

			$user_id = wp_insert_user( $userdata );
			update_usermeta($user_id, 'show_admin_bar_front', 'false');			
			
		if($user_id){
		
			if($get_fields){			
				foreach((array)$get_fields as $custom_field){				
					$slug = str_replace('-','_',$custom_field['slug']);
					if($custom_field['type']=='checkbox'){
						$chek = explode('#',$custom_field['field_select']);
						$count_field = count($chek);
						for($a=0;$a<$count_field;$a++){
							$slug_chek = $slug.'_'.$a;
							if($_POST[$slug_chek]!='undefined') update_usermeta($user_id, $slug_chek, $_POST[$slug_chek]);
						}
					}else{
						if($_POST[$slug]) update_usermeta($user_id, $slug, $_POST[$slug]);	
					}
				}
			}	
			
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
										
			$res['int']=100;									
		}						
	}

	echo json_encode($res);	
	exit;
}
add_action('wp_ajax_register_user_recall', 'register_user_recall');
add_action('wp_ajax_nopriv_register_user_recall', 'register_user_recall');

function chek_user_authenticate($email){
	global $rcl_options;
	if($rcl_options['confirm_register_recall']==1){ 
		if ( $user = get_user_by('login', $email) ){
			$user_data = get_userdata( $user->ID );
			$roles = $user_data->roles;
			$role = array_shift($roles);
			if($role=='need-confirm'){
				//echo '4';exit;
				wp_redirect( get_bloginfo('wpurl').'?getconfirm=needed' ); exit;
			}
		}
	}
}


function confirm_user_registration(){
global $wpdb;

	$reglogin = $_GET['rglogin'];
	$regpass = $_GET['rgpass'];
	$regcode = md5($reglogin);
	//echo '3';exit;
	if($regcode==$_GET['rgcode']){
		if ( $user = get_user_by('login', $reglogin) ){
			//delete_usermeta($user->ID, 'account_confirm');
			wp_update_user( array ('ID' => $user->ID, 'role' => get_option('default_role')) ) ;
			$time_action = date("Y-m-d H:i:s");
			$wpdb->insert( RCL_PREF.'user_action', array( 'user' => $user->ID, 'time_action' => $time_action ) );
			
			$creds = array();
			$creds['user_login'] = $reglogin;
			$creds['user_password'] = $regpass;
			$creds['remember'] = true;			
			$sign = wp_signon( $creds, false );
			
			if ( is_wp_error($sign) ){
				//echo '2';exit;
				wp_redirect( get_bloginfo('wpurl').'?getconfirm=needed' ); exit;
			}else{
				
				wp_redirect( get_author_posts_url($user->ID) ); exit;
			}
		}			
	}else{
		//echo '1';exit;	
		wp_redirect( get_bloginfo('wpurl').'?getconfirm=needed' ); exit;
	}	
}

function confirm_user_resistration_activate(){
global $rcl_options;
  if (isset($_GET['rgcode'])&&isset($_GET['rglogin'])){
	if($rcl_options['confirm_register_recall']==1) add_action( 'wp', 'confirm_user_registration' ); 
  }
}
add_action('init', 'confirm_user_resistration_activate');
add_action('wp_authenticate','chek_user_authenticate');

/*************************************************
Авторизация пользователя
*************************************************/
function sign_user_in_account_recall(){
	$pass = $_POST['user_pass'];
	$login = $_POST['login_sign'];	
	if($pass&&$login){
	
		if ( $user = get_user_by('login', $login) ){
			$user_data = get_userdata( $user->ID );
			$roles = $user_data->roles;
			$role = array_shift($roles);
			if($role=='need-confirm'){
				$res['int']=1;
				$res['recall'] .= '<p style="text-align:center;color:red;">Подтвердите ваш аккаунт!<br/>Для этого перейдите по ссылке в письме, высланном на почту, указанную вами при регистрации.</p>';
				echo json_encode($res);	
				exit;
			}
		}
				$creds = array();
				$creds['user_login'] = $login;
				$creds['user_password'] = $pass;
				$creds['remember'] = true;
				$user = wp_signon( $creds, false );
				if ( is_wp_error($user) ){
					//$user->get_error_message()
					$res['int']=1;
					$res['recall'] .= '<p style="text-align:center;color:red;">Логин или пароль были набраны неверно<br /><a href="'. wp_lostpassword_url().'">Забыли пароль?</a></p>';
				
				}else{				
					rcl_update_timeaction_user();
					$res['redirect'] = get_authorize_url_rcl($user->ID);								
					$res['int']=100;						
					$res['recall']='<p style="text-align:center;color:green;">Успешный вход! Вы будете перенаправлены на свою страницу.</p>';
				}						
	}else{
		$res['int']=1;
		$res['recall'] .= '<p style="text-align:center;color:red;">Все поля обязательны для заполнения!</p>';
		} 
		
	echo json_encode($res);	
	exit;
}
add_action('wp_ajax_sign_user_in_account_recall', 'sign_user_in_account_recall');
add_action('wp_ajax_nopriv_sign_user_in_account_recall', 'sign_user_in_account_recall');

/*************************************************
Добавляем textarea в поле профиля для внесения настроек
*************************************************/
function get_data_type_profile_field_recall(){
    global $wpdb;

	$type = $_POST['type'];	
	$slug = $_POST['slug'];		
	
	$content = '<textarea rows="1" name="field_select_'.$slug.'"></textarea>';
	
	$data['result']=100;
	$data['content']= $content;
	echo json_encode($data);

    exit;
}
add_action('wp_ajax_get_data_type_profile_field_recall', 'get_data_type_profile_field_recall');
add_action('wp_ajax_nopriv_get_data_type_profile_field_recall', 'get_data_type_profile_field_recall');		
?>