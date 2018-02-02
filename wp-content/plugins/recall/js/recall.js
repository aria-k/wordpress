jQuery(function(){

	jQuery('.bxslider').bxSlider({
		pagerCustom: '#bx-pager'
	});

	jQuery('.close-popup').live('click',function(){
		jQuery('#rcl-overlay').fadeOut();
		jQuery('#rcl-popup').empty();		
		return false;
	});
	
	jQuery('#rcl-overlay').click(function(){
		jQuery('#rcl-overlay').fadeOut();
		jQuery('#rcl-popup').empty();		
		return false;
	});

	jQuery("#temp-files .thumb-foto").live('click',function(){		
		jQuery("#temp-files .thumb-foto").removeAttr("checked");
		jQuery(this).attr("checked",'checked');			
	});

	jQuery(".recallbar li").hover(
		function(){
			jQuery(".recallbar li .sub-menu").css('display','none');		
			jQuery(this).children(".sub-menu").css('display','block');				
	});
	
	/*jQuery('.themodal-overlay').live('click',function(){	
		jQuery('.themodal-overlay').remove(); return false;
	});	*/
		
	jQuery("body").mousedown(function(){
          jQuery(".recallbar li .sub-menu").css('display','none');
      });
	
	jQuery(".thumbs a").click(function(){	
			var largePath = jQuery(this).attr("href");
			var largeAlt = jQuery(this).attr("title");		
			jQuery("#largeImg").attr({ src: largePath, alt: largeAlt });
			jQuery(".largeImglink").attr({ href: largePath });		
			jQuery("h2 em").html(" (" + largeAlt + ")"); return false;
		});	
	
	var num_field_rcl = jQuery('input .field_thumb_rcl').size() + 1;
    jQuery('#add-new-input-rcl').click(function() {
        if(num_field_rcl<5) jQuery('<tr><td><input type="radio" name="image_thumb" value="'+num_field_rcl+'"/></td><td><input type="file" class="field_thumb_rcl" name="image_file_'+num_field_rcl+'" value="" /></td></tr>').fadeIn('slow').appendTo('.inputs');
		else jQuery(this).remove();
        num_field_rcl++;
		return false;
    });
	
	jQuery('.public-post-group').live('click',function(){				
		jQuery(this).slideUp();
		jQuery(this).next().slideDown();
		return false;
	});
	jQuery('.close-public-form').live('click',function(){				
		jQuery(this).parent().prev().slideDown();
		jQuery(this).parent().slideUp();
		return false;
	});
	
	jQuery(".close-votes").live('click',function(){	
		jQuery(".votes-comment").remove();
		jQuery(".float-window-recall").remove();
		jQuery(".votes-post").remove();
		jQuery(".users-feed").remove();
		return false; 
	});

	jQuery('.close_edit').click(function(){
		jQuery('.group_content').empty();
	});
	
	jQuery('.form-tab-rcl .link-tab-rcl').click(function(){
		jQuery('.form-tab-rcl').slideUp();
		if(jQuery(this).attr('id')=='link-login-rcl') jQuery('#login-form-rcl').slideDown();
		if(jQuery(this).attr('id')=='link-register-rcl') jQuery('#register-form-rcl').slideDown();
		if(jQuery(this).attr('id')=='link-remember-rcl') jQuery('#remember-form-rcl').slideDown();
		return false; 
	});
		
	jQuery('.block_button').click(function(){
		if(jQuery(this).hasClass('active'))return false;
		var id = jQuery(this).attr('id');		
		jQuery(".block_button").removeClass("active");
		jQuery(".recall_content_block").removeClass("active").slideUp();
		jQuery(this).addClass("active");
		jQuery('.'+id+'_block').slideDown().addClass("active");
		return false;
	});
	
	/*jQuery('.ajax_button').click(function(){
		if(jQuery(this).hasClass('active'))return false;
		var id = jQuery(this).attr('id');		
		jQuery(".block_button").removeClass("active");
		jQuery(this).addClass("active");
		jQuery(".recall_content_block").removeClass("active").slideUp();
	});*/

	jQuery('.child_block_button').click(function(){
		if(jQuery(this).hasClass('active'))return false;
		var id = jQuery(this).attr('id');
		var parent_id = jQuery(this).parent().attr('id');
		jQuery("#"+parent_id+" .child_block_button").removeClass("active");
		jQuery("#"+parent_id+" .recall_child_content_block").removeClass("active").slideUp();
		jQuery(this).addClass("active");
		jQuery('#'+parent_id+' .'+id+'_block').slideDown().addClass("active");
		return false;
	});	
	
	jQuery('textarea,input[type="text"]').click(function()
	{
		jQuery('.errorMsg').fadeOut();		
			return false;		
	});		
	
	if(get_param['action-rcl']){
		jQuery('.form-tab-rcl').slideUp();
		jQuery('#'+get_param['action-rcl']+'-form-rcl').slideDown();		
		return false; 
	}
	
	if(get_param['view']){		
		var id_block = get_param['view'];
		view_recall_content_block(id_block);
	}
	
	function view_recall_content_block(id_block){
		jQuery(".block_button").removeClass("active");
		jQuery('.recall_content_block').slideUp();
		jQuery('#'+id_block).addClass("active");
		jQuery('.'+id_block+'_block').slideDown().addClass("active");
		return false;
	}
	
	if(jQuery.cookie('favs')){		
		favsr=jQuery.cookie('favs'); 
		favsr=favsr.split('|');
		jQuery("#favs").html('<p style="margin:0;" align="right"><a onclick="jQuery(\'#favs\').slideToggle();return false;" href="#">Закрыть</a></p>');
		for(i=1;i<favsr.length;i++){
			favsl=favsr[i].split(',');
			if(favsl[1]){ 
				jQuery("#favs").append('<div><a href="'+favsl[0]+'">'+favsl[1]+'</a> [<a href="javascript://" onclick="delfav(\''+favsl[0]+'\')">x</a>]</div>');
			}else{
				delfav(favsl[0]);
			}
		}
		return false;
	} else {
		jQuery("#favs").html('<p style="margin:0;" align="right"><a onclick="jQuery(\'#favs\').slideToggle();return false;" href="#">Закрыть</a></p><p class="empty"><b>Формируйте свой список интересных страниц сайта с помощью закладок!</b><br />Закладки не добавляются в ваш браузер и действуют только на этом сайте.<br />Для добавления новой закладки,<br>на нужной странице нажмите <b>В закладки</b>.<br> Помните что если очистить Cookies, то закладки тоже исчезнут.<br>Управляйте временем сохранения закладок через настройки вашего браузера для Cookies.</p>');
		return false;
	}
	
});

	var FileAPI = {
		debug: true
		, media: true
		, staticPath: '/wp-content/plugins/recall/js/fileapi/FileAPI/'
	};
	var examples = [];

	var rcl_tmp = new Array();
	var rcl_tmp2 = new Array();
	var get_param = new Array();

	var get = location.search;
	if(get != ''){
	  rcl_tmp = (get.substr(1)).split('&');
	  for(var i=0; i < rcl_tmp.length; i++) {
	  rcl_tmp2 = rcl_tmp[i].split('=');
	  get_param[rcl_tmp2[0]] = rcl_tmp2[1];
	  }
	}


	function js_clickpagesetCookie($Name,$Value,$EndH){ 

		var exdate=new Date(); 
		$EndH=exdate.getHours()+$EndH; 
		exdate.setHours($EndH); 
		document.cookie=$Name+ "=" +escape($Value)+(($EndH==null) ? "" : ";expires="+exdate.toGMTString()+"; path=/;");
	}

	function js_clickpagegetCookie($Name){ 

		if (document.cookie.length>0) { 
			$Start=document.cookie.indexOf($Name + "="); 
			if ($Start!=-1) { $Start=$Start + $Name.length+1; $End=document.cookie.indexOf(";",$Start); 
			if ($End==-1) $End=document.cookie.length; return unescape(document.cookie.substring($Start,$End)); 
			} 
		} 

		return "";
	}

	jQuery.cookie = function(name, value, options) {
		if (typeof value != 'undefined') { 
			options = options || {};
			if (value === null) {
				value = '';
				options.expires = -1;
			}
			var expires = '';
			if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
				var date;
				if (typeof options.expires == 'number') {
					date = new Date();
					date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
				} else {
					date = options.expires;
				}
				expires = '; expires=' + date.toUTCString();
			}
			var path = options.path ? '; path=' + (options.path) : '';
			var domain = options.domain ? '; domain=' + (options.domain) : '';
			var secure = options.secure ? '; secure' : '';
			document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
		} else {
			var cookieValue = null;
			if (document.cookie && document.cookie != '') {
				var cookies = document.cookie.split(';');
				for (var i = 0; i < cookies.length; i++) {
					var cookie = jQuery.trim(cookies[i]);
					if (cookie.substring(0, name.length + 1) == (name + '=')) {
						cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
						break;
					}
				}
			}
			return cookieValue;
		}
	};

	cookiepar={expires: 9999, path: '/'} // Все закладки общие

	function addfav(title,url) {
		title=title || document.title; url=url || document.location.href; if(title.length>20){
			title=title.substr(0,99)+'..';
		}
		if(jQuery("#favs a[href='"+url+"']").length>0){
			jQuery("#add_bookmarks").html('Страница уже есть в закладках').slideDown().delay(1000).fadeOut(1000);		
			return false;
		}
		if(jQuery.cookie('favs')){
			jQuery.cookie('favs',jQuery.cookie('favs')+'|'+url+','+title,cookiepar);
		} else {
			jQuery.cookie('favs','|'+url+','+title,cookiepar);
		}
		jQuery("#add_bookmarks").html('Закладка добавлена!').slideDown().delay(2000).fadeOut(1000);

		if(jQuery("#favs").text()=='У вас пока нет закладок') {
			jQuery("#favs").html(' ');
		}
		var empty = jQuery("#favs .empty");
		if(empty) jQuery("#favs .empty").remove();
		title=title.split('|');
		jQuery("#favs").append('<div style="display:none" id="newbk"><a href="'+url+'">'+title[0]+'</a> [<a href="javascript://" onclick="delfav(\''+url+'\')">x</a>]</div>');
		jQuery("#newbk").fadeIn('slow').attr('id','');
	}
	function delfav(url){
		jQuery("#favs a[href='"+url+"']").parent().fadeOut('slow',function(){
			jQuery(this).empty().remove(); 
			if(jQuery("#favs").html().length<2){
				jQuery("#favs").html('У вас нет закладок');
			}
		});
		nfavs=''; 
		dfavs=jQuery.cookie('favs');
		dfavs=dfavs.split('|');
		for(i=0;i<dfavs.length;i++){
			if(dfavs[i].split(',')[0]==url){
				dfavs[i]='';
			}
			if(dfavs[i]!=''){
				nfavs+='|'+dfavs[i];
			}
		}
		jQuery.cookie('favs',nfavs,cookiepar);
	}
	function isValidEmailAddress(emailAddress) {
		var pattern = new RegExp(/^((\"[\w-\s]+\")|([\w-]+(?:\.[\w-]+)*)|(\"[\w-\s]+\")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	}