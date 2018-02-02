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
jQuery(function(){

	if(get_param['form']=='sign'){		
		jQuery(".regform").css("display","block"); 
		jQuery(".registerform").css("display","none"); 
		jQuery(".signform").css("display","block");
		jQuery(".title-sign").addClass("active"); 
		jQuery(".arrow-register").removeAttr("id", "arrow-register"); 
		jQuery(".arrow-sign").attr("id", "arrow-sign");
	}
	
	if(get_param['form']=='register'){ 
		jQuery(".regform").css("display","block");
		jQuery(".signform").css("display","none"); 
		jQuery(".registerform").css("display","block");
		jQuery(".title-register").addClass("active");
		jQuery(".arrow-sign").removeAttr("id", "arrow-sign"); 
		jQuery(".arrow-register").attr("id", "arrow-register"); 
	}
	
	jQuery(".title-register").click(function(){	
		jQuery(".signform").css("display","none");
		jQuery(".registerform").fadeIn();
		jQuery(".title-register").addClass("active");
		jQuery(".title-sign").removeClass("active");
		jQuery(".arrow-sign").removeAttr("id", "arrow-sign");
		jQuery(".arrow-register").attr("id", "arrow-register");
	});
	
	jQuery(".title-sign").click(function(){	
		jQuery(".registerform").css("display","none");
		jQuery(".signform").fadeIn();
		jQuery(".title-sign").addClass("active");
		jQuery(".title-register").removeClass("active");
		jQuery(".arrow-register").removeAttr("id", "arrow-register");
		jQuery(".arrow-sign").attr("id", "arrow-sign");
			
	});		
});