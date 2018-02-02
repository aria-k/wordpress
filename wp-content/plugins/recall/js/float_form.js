jQuery(function(){
	jQuery(".reglink").click(function(){ 
		jQuery(".backform").fadeIn(); 
		jQuery(".regform").fadeIn();
		jQuery(".signform").css("display","none"); 
		jQuery(".registerform").fadeIn(); 
		jQuery(".arrow-sign").removeAttr("id", "arrow-sign"); 
		jQuery(".arrow-register").attr("id", "arrow-register"); 
	});
	jQuery(".close").click(function(){	
		jQuery(".regform").fadeOut();
		jQuery(".backform").fadeOut();		
	});
	jQuery(".title-register").click(function(){	
		jQuery(".signform").css("display","none");
		jQuery(".registerform").fadeIn();
		jQuery(".arrow-sign").removeAttr("id", "arrow-sign");
		jQuery(".arrow-register").attr("id", "arrow-register");
	});
	jQuery(".title-sign").click(function(){	
		jQuery(".registerform").css("display","none");
		jQuery(".signform").fadeIn();
		jQuery(".arrow-register").removeAttr("id", "arrow-register");
		jQuery(".arrow-sign").attr("id", "arrow-sign");
			
	});	
	jQuery(".sign-button").click(function(){ 
		jQuery(".backform").fadeIn(); 
		jQuery(".regform").fadeIn(); 
		jQuery("#header").css("z-index", "0"); 
		jQuery(".registerform").css("display","none"); 
		jQuery(".signform").fadeIn(); 
		jQuery(".arrow-register").removeAttr("id", "arrow-register"); 
		jQuery(".arrow-sign").attr("id", "arrow-sign"); 
	});
});