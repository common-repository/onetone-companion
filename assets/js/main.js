jQuery(document).ready(function($){
	// contact form
	function IsEmail($email ) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test( $email );
	}

	$("form.contact-form #submit").click(function(){
		
		var obj     = $(this).parents(".contact-form");
		var Name    = obj.find("input#name").val();
		var Email   = obj.find("input#email").val();
		var Message = obj.find("textarea#message").val();
		var sendto  = obj.find("input#sendto").val();
		var notice  = obj.find(".noticefailed");
		
		
		obj.find('.noticesuccess').remove();
		obj.find('.contact-form-checkbox').parent('div').css({'border':'none'});
		
		if( !notice.length ){
			obj.append('<div class="noticefailed"></div>');
			notice  = obj.find(".noticefailed");
		}
	
		notice.text("");
		if(Name ===''){
			notice.html(oc_params.i18n.i3);
			return false;
		}
		
		if(Email ===''){
			notice.html(oc_params.i18n.i2);
			return false;
		}
		
		if( !IsEmail( Email ) ) {
			notice.html(oc_params.i18n.i2);
			return false;
		}
		if(Message === ''){
			notice.html(oc_params.i18n.i4);
			return false;
		}
		
		if(obj.find('.contact-form-checkbox').length){
			
			if( !obj.find('.contact-form-checkbox').is(':checked') ){
				obj.find('.contact-form-checkbox').parent('div').css({'border':'2px solid #f7e700'});
				notice.html(obj.find('.checkbox-notice').html());
				return false;
				}
			
			}
	
		notice.html("");
		notice.append("<img alt='loading' class='loading' src='"+oc_params.plugins_url+"/assets/images/loading.gif' />");
		$.ajax({
			type:"POST",
			dataType:"json",
			url:oc_params.ajaxurl,
			data:{'Name':Name,'Email':Email,'Message':Message,'sendto':sendto,'action':'onetone_contact'},
			success:function(data){ 
				if(data.error==0){
					notice.addClass("noticesuccess").removeClass("noticefailed");
					obj.find(".noticesuccess").html(data.msg);
				}else{
					notice.html(data.msg);	
				}
				jQuery('.loading').remove();obj[0].reset();
				},
			error:function(){
				notice.html("Error.");
				obj.find('.loading').remove();
				}
			});
	});
	
});