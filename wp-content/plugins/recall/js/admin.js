	var tmp_1 = new Array();
	var tmp_2 = new Array();
	var get_param = new Array();
	var get = location.search;
	if(get != ''){
	  tmp_1 = (get.substr(1)).split('&');
	  for(var i=0; i < tmp_1.length; i++) {
	  tmp_2 = tmp_1[i].split('=');
	  get_param[tmp_2[0]] = tmp_2[1];
	  }
	}

jQuery(document).ready( function() {

	jQuery('#select_all').live('click',function(){
		jQuery('input[type=\"checkbox\"]').attr('checked', true);
		jQuery('#select_all').attr('value', 'Снять все метки');
		jQuery('#select_all').attr('id', 'select_none');					
		return false;
	});
	jQuery('#select_none').live('click',function(){
		jQuery('input[type=\"checkbox\"]').attr('checked', false);
		jQuery('#select_none').attr('value', 'Отметить все');
		jQuery('#select_none').attr('id', 'select_all');				   
		return false;
	});
	
	jQuery('.profilefield-submitdelete').click(function() {
		var id_item = jQuery(this).attr('id');
		jQuery('#item-'+id_item+' .field').attr('value','');
		jQuery('#settings-'+id_item).slideUp();
		jQuery('#item-'+id_item+' .item-controls').empty();
		jQuery('#item-'+id_item+' .item-title').text('Будет удалено');
		return false;
	});
	jQuery('.profilefield-item-edit').click(function() {
		var id_button = jQuery(this).attr('id');
		var id_item = str_replace('edit-','settings-',id_button);	
		jQuery('#'+id_item).slideToggle();
		return false;
	});
	
	var i = jQuery('#inputs_user_fields .field').size();
    jQuery('#add_user_field').click(function() {
        jQuery('<li class="menu-item menu-item-edit-active"><dl class="menu-item-bar"><dt class="menu-item-handle"><span class="item-title"><input type="text" size="34" name="user_fields_recall[]" class="field" value=""/></span><span class="item-controls"><span class="item-type">Тип: <select id="'+i+'" class="type_field" name="type_field_'+i+'"><option value="text">Однострочное поле</option><option value="textarea">Многострочное поле</option><option value="select">Выпадающий список</option><option value="checkbox">Чекбокс</option><option value="radio">Радиокнопки</option></select></span></span></dt></dl><div class="menu-item-settings" style="display: block;"><p id="content-'+i+'" class="link-to-original"><input type="checkbox" class="first-chek" name="requared_register_'+i+'" value="1"/> обязательное поле<br /><input type="checkbox" name="register_user_field_'+i+'" value="1"/> отобразить в форме регистрации и при оформлении заказа для гостей<br /><input type="checkbox" name="requared_user_field_'+i+'" value="1"/> показывать содержимое для других пользователей</p>									</div></li>').fadeIn('slow').appendTo('.user_fields');
		i++;
		return false;
    });
	
	var z = jQuery('#inputs_public_fields .field').size();
    jQuery('#add_public_field').click(function(){
        jQuery('<li class="menu-item menu-item-edit-active"><dl class="menu-item-bar"><dt class="menu-item-handle"><span class="item-title"><input type="text" size="34" name="public_fields_title[]" class="field" value=""/></span><span class="item-controls"><span class="item-type">Тип: <select name="type_field_'+z+'"><option value="text">Однострочное поле</option><option value="textarea">Многострочное поле</option><option value="select">Выпадающий список</option><option value="checkbox">Чекбокс</option><option value="radio">Радиокнопки</option></select></span></span></dt></dl><div class="menu-item-settings" style="display: block;"><p><input type="checkbox" name="requared_public_'+z+'" value="1"/> обязательное поле<br /></p></div></li>').fadeIn('slow').appendTo('.public_fields');
		z++;
		return false;
    });
	
	jQuery('#recall h2').click(function(){
		jQuery('.wrap-recall-options').slideUp();
		jQuery(this).next('.wrap-recall-options').slideDown();
		return false;
	});
	
	if(get_param['options']){
		jQuery('.wrap-recall-options').slideUp();
		jQuery('#options-'+get_param['options']).slideDown();
		return false;
	}
	
	jQuery('.type_field').live('change',function(){
		var type = jQuery(this).val();
		var slug = jQuery(this).attr('id');
		if(type=='text'||type=='textarea'){
			jQuery('#content-'+slug+' textarea').remove();
			return false;
		}
		if(jQuery('#content-'+slug+' textarea').attr('name')) return false;				
		var dataString = 'action=get_data_type_profile_field_recall&type='+type+'&slug='+slug;

		jQuery.ajax({
			type: 'POST',
			data: dataString,
			dataType: 'json',
			url: "/wp-admin/admin-ajax.php",
			success: function(data){
				if(data['result']==100){					
					jQuery('#content-'+slug+' .first-chek').before(data['content']);				
				}else{
					alert('Ошибка!');
				}
			} 
		});	  	
		return false;
	});
	
	
	function str_replace(search, replace, subject) {
		return subject.split(search).join(replace);
	}
});