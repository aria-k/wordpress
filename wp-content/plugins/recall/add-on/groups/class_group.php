<?phpclass Rcl_Group{	public $curent_term;	public $term_id;	public $requests;	public $in_group;	public $imade_id;	public $admin_id;	public $users_group;	public $gallery_group;	public $options_gr;	public $group_id;	public $users_count;		public function __construct($grid=false) {		global $wpdb,$user_ID,$rcl_options,$group_id,$options_gr;		if($grid) $group_id = $grid;		$this->group_id = $group_id;		$this->term_id = $this->group_id;		$this->options_gr = $options_gr;				$this->users_count = $wpdb->get_var("SELECT COUNT(user_id) FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'user_group_$this->term_id'");				if($_GET['group-page']=='users') add_filter('footer_group_rcl',array(&$this,'all_users_group'));    }		function init_variables(){		global $wpdb,$user_ID;				add_filter('the_excerpt', 'rcl_add_edit_post_button');		$this->curent_term = get_term_by('ID', $this->group_id, 'groups');				if($req = get_option('request_group_access_'.$this->term_id)) $this->requests = unserialize($req);		if($user_ID) $this->in_group = get_the_author_meta('user_group_'.$this->term_id,$user_ID);		if ( isset($_POST['delete-group-rcl'])&&$user_ID ){			if( !wp_verify_nonce( $_POST['_wpnonce'], 'delete-group-rcl' ) ) return false;				wp_delete_term( $this->term_id, 'groups');				$this->imade_id = get_option('image_group_'.$this->term_id);				delete_users_group_rcl($this->term_id, $this->term_id, 'groups');				echo '<h2 class="aligncenter" style="color:red;">Ваша группа была удалена!</h2>';				return false;		}		$this->imade_id = $this->options_gr['avatar'];		$this->admin_id = $this->options_gr['admin'];				if(!$this->admin_id) $this->admin_id = $wpdb->get_var("SELECT user_id FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'admin_group_$this->term_id'");		if(!$this->imade_id) $this->imade_id = get_option('image_group_'.$this->term_id);				$this->users_group = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."usermeta WHERE meta_key = 'user_group_$this->term_id' ORDER BY RAND() LIMIT 10");				$this->gallery_group = $wpdb->get_results("SELECT ID,post_parent FROM ".$wpdb->prefix ."posts WHERE post_content = 'gallery_group_$this->term_id' ORDER BY ID DESC LIMIT 12");	}		function get_post_request(){		global $user_ID,$wpdb,$options_gr;				if ( isset($_POST['update-group-rcl'])&&$user_ID ){			if( !wp_verify_nonce( $_POST['_wpnonce'], 'update-options-group-rcl' ) ) return false;			if($this->options_gr) $opt = true;						if(!$_POST['private']&&$this->options_gr['private']) unset($this->options_gr['private']);			if(!$_POST['images']&&$this->options_gr['images']) unset($this->options_gr['images']);			if(!$_POST['users']&&$this->options_gr['users']) unset($this->options_gr['users']);						foreach($_POST as $p => $data ){				if($data){					if($p=='event'){						if(!$_POST['event']['active']) $_POST['event']['active'] = 0;						foreach($_POST['event'] as $key=>$date){							$this->options_gr['event'][$key] = $date;						}					}else{ 						$this->options_gr[$p] = $data;					}				}else if($this->options_gr[$data]){					unset($this->options_gr[$data]);				}			}						$options_gr = $this->options_gr;			$options_ser = serialize($this->options_gr);			if($opt){				$res = $wpdb->update(					RCL_PREF.'groups_options',					array('option_value'=>$options_ser),					array('group_id'=>$this->group_id)				);			}else{				$wpdb->insert(					RCL_PREF.'groups_options',					array('group_id'=>$this->group_id,'option_value'=>$options_ser)				);				}		}	}		function header_group(){		global $user_ID;		if($user_ID&&$user_ID==$this->admin_id) $edit = ' edit';			echo '<div id="group-'.$this->term_id.'" class="group-info'.$edit.'">';		if($this->imade_id){			$src = wp_get_attachment_image_src( $this->imade_id, 'thumbnail'); 			echo '<img src="'.$src[0].'" class="avatar_gallery_group"><div class="edit-avatar"></div>';					}else{			echo '<img src="'.plugins_url('img/empty-avatar.jpg', __FILE__).'" class="avatar_gallery_group"><div class="edit-avatar"></div>';		}					echo '<h2 class="groupname">'.$this->curent_term->name.'</h2>';				echo '<p class="admin-group">Создатель: <a href="'.get_author_posts_url($this->admin_id).'">'.get_the_author_meta('display_name',$this->admin_id).'</a></p>';		echo '<p class="users-group">Участников: '.$this->users_count.'</p>';		$desc = term_description( $this->term_id, 'groups' );		if($desc) echo '<div class="desc_group">'.$desc.'</div>';			else echo '<div class="desc_group"><b>Добавить описание группы</b></div>';		echo '<div class="group_content"></div>';			if($user_ID&&$user_ID==$this->admin_id){			echo '<p align="right" style="clear:both;"><small style="color:red">Для редактирования названия, описания или картинки<br /> группы просто нажмите на этот элемент</small></p>';		}	}		function options_group(){		global $user_ID;		if($user_ID&&$user_ID==$this->admin_id){				echo '<div class="public-post-group"><a href="#">Настройки группы</a></div>				 			<div class="options-group-rcl public_block" style="clear:both;display:none;">			<a class="close-public-form" href="#">Закрыть настройки</a>			<form action="" method="post">			<input type="hidden" name="group_id" value="'.$this->term_id.'">			<h3>Настройки группы:</h3>			<p>Статус группы <input type="checkbox" class="status_groups" '.checked($this->options_gr['private'],1,false).' name="private" value="1"> Приватная группа</p>';			echo '<p><input type="checkbox" class="status_groups" '.checked($this->options_gr['users'],1,false).' name="users" value="1"> - показывать участников группы</p>';			echo '<p><input type="checkbox" class="status_groups" '.checked($this->options_gr['images'],1,false).' name="images" value="1"> - показывать последние изображения группы</p>';			echo '<p>Категории записей группы (разделять запятой):<br>			<textarea name="tags" rows="3" cols="60">'.$this->options_gr['tags'].'</textarea>			</p>';						$this->options_group = apply_filters('options_group_rcl',$this->options_group);						echo $this->options_group;						echo wp_nonce_field('update-options-group-rcl','_wpnonce',true,false).'			<p style="text-align:right;"><input type="submit" name="update-group-rcl" value="Сохранить настройки" class="recall-button"></p>			</form>			</div>';		}	}		function admin_block(){		$this->button_del_group();		$this->requests_users();	}		function button_del_group(){			$args = array(				'numberposts' => 1,				'fields' => 'ids',				'post_type' => 'post-group',				'tax_query' => array(					array(						'taxonomy' => 'groups',						'field' => 'id',						'terms' => $this->term_id					)				)			);			$post_gr = get_posts($args);			if(!$post_gr) echo '<div class="add-user-group">				<form action="" method="post">					'.wp_nonce_field('delete-group-rcl','_wpnonce',true,false).'					<input type="submit" name="delete-group-rcl" value="Удалить группу" onsubmit="return confirm(\'Вы уверены?\');" class="recall-button">				</form>			</div>';			}		function requests_users(){		if($this->requests){			echo '<h3>Запросы на вступление в группу</h3>			<table class="request-list">';				foreach((array)$this->requests as $user=>$name){					echo '<tr id="user-req-'.$user.'">						<td>'.get_avatar($user,50).'</td><td><a href="'.get_author_posts_url($user).'">'.$name.'</a></td><td><input type="button" id="add-user-req-'.$this->term_id.'" class="request-access recall-button" value="Принять"></td><td><input type="button" id="del-user-req-'.$this->term_id.'" class="request-access recall-button" value="Отклонить"></td>					</tr>';				}			echo '</table>';		}	}		function users_block(){		global $user_ID;				if($this->in_group) echo '<div class="add-user-group"><form action="" method="post">			'.wp_nonce_field('login-group-request-rcl','_wpnonce',true,false).'			<input type="submit" class="recall-button" name="login_group" value="Выйти из группы"></form></div>';		else if($user_ID&&$this->options_gr['private']==1){			if($this->requests[$user_ID]) echo '<p style="clear:both;text-align:right;color:green;">Ваша заявка на вступление принята</p>';			else echo '<div class="add-user-group"><form action="" method="post">			'.wp_nonce_field('login-group-request-rcl','_wpnonce',true,false).'			<input type="submit" name="login_group" value="Подать заявку на вступление" class="recall-button"></form></div>';		}else if($user_ID) echo '<div class="add-user-group">			<form action="" method="post">			'.wp_nonce_field('login-group-request-rcl','_wpnonce',true,false).'			<input type="submit" name="login_group" value="Присоединиться" class="recall-button"></form></div>';	}		function userlist_group(){		if($this->users_group&&$this->options_gr['users']==1){			$block_users = '<div class="users_group">			<h3>Участники группы:</h3>';			$a=0;			$names = get_names_array_rcl($this->users_group,'user_id');			foreach((array)$this->users_group as $single_user){				$a++;				$block_users .= '<a title="'.$names[$single_user->user_id].'" href="'.get_author_posts_url($single_user->user_id).'">'.get_avatar($single_user->user_id,50).'</a>';				if($a==9)break;			}			$block_users .= '			<p style="clear:both;margin:0;" align="right"><a href="'.get_term_link( (int)$this->group_id, 'groups' ).'?group-page=users#userlist" class="all-users-group">Все участники</a></p>			</div>';						echo $block_users;		}	}		function private_title(){		global $user_ID;		if($this->options_gr['private']==1){			if(!$this->in_group&&$user_ID!=$this->admin_id){				echo '<h2 align="center" style="color:red;">Доступ в группу закрыт настройками приватности.</h2>';				//echo '</div>';				return false;			}		}	}		function imagelist_group(){		if($this->gallery_group&&$this->options_gr['images']==1){			echo '<div class="gallery-group">';			echo '<h3>Последние фото группы:</h3>';			foreach((array)$this->gallery_group as $foto){				$src_foto = wp_get_attachment_image_src( $foto->ID, 'thumbnail'); 				echo '<a href="'. get_permalink($foto->post_parent) .'"><img src="'.$src_foto[0].'" width="75" align="left"></a>';			}			echo '</div>';		}	}		function content_group(){		$content_group = apply_filters('content_group_rcl',$content_group);		echo $content_group;	}		function public_form(){		global $user_ID;				if($this->in_group||$user_ID==$this->admin_id){			echo '<div class="public-post-group"><a href="#">Опубликовать новую запись в группе</a></div>				 <div class="public_block" style="clear:both;display:none;">';			echo '<a class="close-public-form" href="#">Закрыть форму</a><h3>Публикация записи</h3>';			echo do_shortcode('[public-form post_type="post-group" group_id="'.$this->term_id.'"]');			echo '</div>';		}	}		function tags_list(){		$targs = array(  			'number'        => 0 			,'hide_empty'   => true  			,'hierarchical' => false  			,'pad_counts'   => false  			,'get'          => ''  			,'child_of'     => 0  			,'parent'       => $this->term_id  		);  		  		$tags = get_terms('groups', $targs);		if($tags) echo '<div class="search-form-rcl">				<form method="get">					'.get_tags_list_group_rcl((object)$tags,'','Вывести все записи').'					<input type="submit" class="recall-button" value="Показать">				</form>			</div>';	}		function all_users_group($page){		return do_shortcode('[userlist page="'.$page.'" orderby="action" group="'.$this->group_id.'" search="no"]');	}		function footer_group(){		global $user_ID;		//if($this->in_group||$user_ID==$this->admin_id) 		$this->tags_list();		echo '</div>';		$footer = apply_filters('footer_group_rcl',$footer);		echo $footer;			}	}?>