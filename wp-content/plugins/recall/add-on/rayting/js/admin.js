jQuery(function(){
/*************************************************
Пополняем личный счет пользователя в админке
*************************************************/
	jQuery('.edit_rayting').live('click',function(){
			var id_attr = jQuery(this).attr('id');
			var id_user = parseInt(id_attr.replace(/\D+/g,''));	
			var rayting = jQuery('.raytinguser-'+id_user).attr('value');
			var dataString_count = 'action=edit_rayting_user_recall&user='+id_user+'&rayting='+rayting;

			jQuery.ajax({
				type: 'POST',
				data: dataString_count,
				dataType: 'json',
				url: '/wp-admin/admin-ajax.php',
				success: function(data){
					if(data['otvet']==100){
						alert('Данные сохранены!');
					} else {
					   alert('Ошибка!');
					}
				} 
			});				
			return false;
	});
});	