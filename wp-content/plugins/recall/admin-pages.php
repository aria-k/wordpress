<?php
function wp_recall_options_panel(){
	add_menu_page(__('WP-RECALL','rcl'), __('WP-RECALL','rcl'), 'edit_plugins', 'manage-wprecall', 'global_recall_options');
		add_submenu_page( 'manage-wprecall', __('НАСТРОЙКИ','rcl'), __('НАСТРОЙКИ','rcl'), 'edit_plugins', 'manage-wprecall', 'global_recall_options');
		add_submenu_page( 'manage-wprecall', __('Документация','rcl'), __('Документация','rcl'), 'edit_plugins', 'manage-doc-recall', 'recall_doc_manage');
}

function recall_doc_manage(){
	echo '<h2>Документация по плагину WP-RECALL</h2>
		<ol>
		<li><a href="http://wppost.ru/ustanovka-plagina-wp-recall-na-sajt/" target="_blank">Установка плагина </a></li>
		<li><a href="http://wppost.ru/nastrojki-plagina-wp-recall/" target="_blank">Настройки плагина</a></li>
		<li><a href="http://wppost.ru/shortkody-wp-recall/" target="_blank">Используемые шорткоды Wp-Recall</a></li>
		<li><a href="http://wppost.ru/obshhie-svedeniya-o-dopolneniyax-wp-recall/" target="_blank">Общие сведения о дополнениях Wp-Recall</a></li>
		<li><a href="http://wppost.ru/dopolneniya-wp-recall/" target="_blank">Базовые дополнения Wp-Recall</a></li>
		<li><a href="http://wppost.ru/downloads-files/" target="_blank">Платные дополнения Wp-Recall</a></li>
		<li><a href="http://wppost.ru/sozdaem-svoe-dopolnenie-dlya-wp-recall-vyvodim-svoyu-vkladku-v-lichnom-kabinete/" target="_blank">Пример создания своего дополнения Wp-Recall</a></li>
		<li><a href="http://wppost.ru/xuki-i-filtry-wp-recall/" target="_blank">Хуки и фильтры Wp-Recall для разработки</a></li>
		<li><a href="http://wppost.ru/category/novosti/obnovleniya/" target="_blank">История обновлений Wp-Recall</a></li>
		<li><a href="http://wppost.ru/faq/" target="_blank">FAQ</a></li>
		<li><a href="http://wppost.ru/forum/" target="_blank">Форум</a></li>
	</ol>';
}

function get_key_addon_rcl($path_parts){
	$ar_dir = explode('/',$path_parts['dirname']);
	$cnt = count($ar_dir);
	for($a=$cnt;$a>=0;$a--){if($ar_dir[$a]=='add-on'){$key=$ar_dir[$a+1];break;}}
	return $key;
}

//Настройки плагина в админке
function global_recall_options(){
	global $wpdb;
	$options = unserialize(get_option('primary-rcl-options'));
	
	$content .= '
	
<div id="recall" class="wrap">
	<form method="post" action="">
	'.wp_nonce_field('update-options-rcl','_wpnonce',true,false).'
	<h2>'.__('Общие настройки','rcl').'</h2>	
	<div class="wrap-recall-options" style="display:block;">

		<div class="option-block">
			<h3>'.__('Личный кабинет','rcl').'</h3>
			<label>'.__('Порядок вывода личного кабинета пользователя','rcl').'</label>
			<select name="view_user_lk_rcl" size="1">';
				$view = $options['view_user_lk_rcl'];
				$content .= '<option value="">'.__('На странице архива автора','rcl').'</option>	
				<option value="1" '.selected($view,1,false).'>'.__('Через шорткод [wp-recall]','rcl').'</option>
			</select>
			<small>'.__('Если выбрана страница архива автора, то в нужном месте шаблона author.php вставить код if(function_exists(\'wp_recall\')) wp_recall();','rcl').'</small>';
			$content .= '<label>'.__('Страница размещения шорткода','rcl').'</label>';
			$args = array(    
				'selected'   => $options['lk_page_rcl'],   
				'name'       => 'lk_page_rcl',
				'show_option_none' => '<span style="color:red">Не выбрано</span>',
				'echo'       => 0  
			);  
			$content .= wp_dropdown_pages( $args );
			$content .= '<small>'.__('Если выбран вариант вывода через шорткод','rcl').'</small>
			<label>'.__('Формирование ссылки на личный кабинет','rcl').'</label>
			<input type="text" name="link_user_lk_rcl" value="'.$options['link_user_lk_rcl'].'" size="60">
			<small>'.__('Ссылка формируется по принципу "/slug_page/?get=ID". Параметр "get" можно задать тут. По-умолчанию user','rcl').'</small>
		</div>
		
		<div class="option-block">
			<h3>'.__('Доступ в консоль','rcl').'</h3>
			<label>'.__('Доступ в консоль сайт разрешена','rcl').'</label>';
			$access_recall = $options['consol_access_rcl'];
			if(!isset($access_recall)) $access_recall = 7;
			$content .= '<select name="consol_access_rcl" size="1">				
				<option value="10" '.selected($access_recall,10,false).'>'.__('только Администраторам','rcl').'</option>
				<option value="7" '.selected($access_recall,7,false).'>'.__('Редакторам и старше','rcl').'</option>
				<option value="2" '.selected($access_recall,2,false).'>'.__('Авторам и старше','rcl').'</option>
				<option value="1" '.selected($access_recall,1,false).'>'.__('Участникам и старше','rcl').'</option>
				<option value="0" '.selected($access_recall,0,false).'>'.__('Всем пользователям','rcl').'</option>
			</select>				
		</div>
		
		<div class="option-block">
			<h3>'.__('Оформление','rcl').'</h3>
			<label>'.__('Cвой файл стилей(CSS)','rcl').'</label>
			<input type="text" name="custom_scc_file_recall" value="'.$options['custom_scc_file_recall'].'" size="60">
			<label>'.__('Размещение кнопок разделов в ЛК','rcl').'</label>';			
			$buttons_place = $options['buttons_place'];
			$content .= '<select name="buttons_place" size="1">				
				<option value="">'.__('Сверху','rcl').'</option>
				<option value="1" '.selected($buttons_place,1,false).'>'.__('Слева','rcl').'</option>				
			</select>
		</div>
		
		<div class="option-block">
			<h3>'.__('Вход и регистрация','rcl').'</h3>
			<label>'.__('Порядок вывода','rcl').'</label>
			<select name="login_form_recall" size="1">
				<option value="">'.__('Плавающая форма','rcl').'</option>';
				$login_form = $options['login_form_recall'];
				$content .= '<option value="1" '.selected($login_form,1,false).'>'.__('На отдельной странице','rcl').'</option>	
				<option value="2" '.selected($login_form,2,false).'>'.__('Форма Wordpress','rcl').'</option>
				<option value="3" '.selected($login_form,3,false).'>'.__('Форма в виджете','rcl').'</option>
			</select>
			
			<label>'.__('ID страницы, где расположили шорткод формы','rcl').'</label>
			<input type="text" name="page_login_form_recall" value="'.$options['page_login_form_recall'].'" size="10">
			<small><b>'.__('Примечание','rcl').':</b> '.__('Если выбран порядок вывода формы входа и регистрации на отдельной странице, то необходимо создать страницу, расположить в ее содержимом шорткод [loginform] и указать ID этой страницы в поле выше.','rcl').'</small>
			
			<label>'.__('Подтверждение регистрации пользователем','rcl').'</label>
			<select name="confirm_register_recall" size="1">
				<option value="">'.__('Не используется','rcl').'</option>
				<option value="1" '.selected($options['confirm_register_recall'],1,false).'>'.__('Используется','rcl').'</option>			
			</select>
			
			<label>'.__('Перенаправление пользователя после авторизации','rcl').'</label>
			<select name="authorize_page" size="1">
				<option value="">Профиль пользователя</option>
				<option value="1" '.selected($options['authorize_page'],1,false).'>Текущая страница</option>
				<option value="2" '.selected($options['authorize_page'],2,false).'>Произвольный URL</option>
			</select>
			<small>Впишите свой URL ниже, если выбран произвольный URL после авторизации</small>
			<input type="text" name="custom_authorize_page" value="'.$options['custom_authorize_page'].'" size="10">
		</div>
		
		<div class="option-block">
			<h3>'.__('Подписка','rcl').'</h3>
			<label>'.__('Подписка пользователей на категории сайта','rcl').'</label>
			<select name="feed_category_recall" size="1">
				<option value="">'.__('Отключено','rcl').'</option>
				<option value="1" '.selected($options['feed_category_recall'],1,false).'>'.__('Включено','rcl').'</option>
			</select>

			<label>'.__('ID рубрик, разрешенных для подписки','rcl').'</label>
			<input type="text" name="id_feed_category" value="'.$options['id_feed_category'].'" size="30">
			<small>'.__('Разделять запятой. Если пусто, то выводятся все рубрики.','rcl').'</small>
		</div>
	
		
		<div class="option-block">
			<h3>'.__('Recallbar','rcl').'</h3>
			<label>'.__('Вывод панели recallbar','rcl').'</label>
			<select name="view_recallbar" size="1">
				<option value="">'.__('Отключено','rcl').'</option>
				<option value="1" '.selected($options['view_recallbar'],1,false).'>'.__('Включено','rcl').'</option>
			</select>
		</div>
		
		<div class="option-block">
			<h3>'.__('Ваша благодарность','rcl').'</h3>
			<label>'.__('Отображать ссылку на сайт разработчика (Спасибо, если решили показать)','rcl').'</label>
			<select name="footer_url_recall" size="1">
				<option value="">'.__('Нет','rcl').'</option>
				<option value="1" '.selected($options['footer_url_recall'],1,false).'>'.__('Да','rcl').'</option>
			</select>
		</div>
		
	</div>';
		
	$content = apply_filters('admin_options_wprecall',$content);
				
	$content .= '<div style="width: 600px;">
	<p><input type="submit" class="button button-primary button-large right" name="primary-rcl-options" value="'.__('Сохранить настройки','rcl').'" /></p>
	</div></form></div>';
	
	echo $content;
}


?>