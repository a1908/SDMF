/*$(function(){
	$('form').areYouSure({message:'Изменения будут потеряны'});
});

window.onload = function(){
	$('form').areYouSure({message:'Изменения будут потеряны'});
	
};
*/

function processForm(form, formdata = null){

	// function to show modal 
	function showModal(id, title, body){
		if($(form).is("[modal]")){
			$('#'+id+' .modal-title').html(title);
			$('#'+id+' .modal-body p').html(body);
			$('#'+id).modal('show');
		}else{
			alert(body);
		}
	}
	
	function getFieldMessage(fieldName,messageType){
		var f;
		if( fieldName in formdata ){
			f = formdata[fieldName];
		}else{
			f = formdata.default;
		}
		
		if( messageType in f){
			return f[messageType];
		}else{
			return _("form-error-Undefined-message-type");
		}
		
		
	}

	if(!formdata){
		formdata = {
			modalTitle : { required:_("modal-title-Required-field-is-missing"), validate:_("modal-title-Form-validation-error") },
			name : { required: _("form-error-Name-is-missing") },
			email:{ required:_("form-error-Email-is-missing"), validate: _("form-error-Invalid-email") },
			phone:{required:_("form-error-Phone-is-missing"), validate: _("form-error-Invalid-phone-number") },
			contacts:{ required:_("form-error-No-contacts-are-provided") },
			message: { required: _("form-error-Message-is-missing") },
			login: {required: _("form-error-Login-is_missing")},
			default: { required: _("form-error-Mandatory-field-is-missing-default"), validate:_("form-error-Incorrect-input-default") },
		}
	}
	
	var error = false;
	var modalID = $(form).attr("modal");//+"Modal";
	var slideup = $(form).attr("slideup");
	var slidedown = $(form).attr("slidedown");
	var action = $(form).attr("action");
	


	// check the form
	// check required fields and groups first	
	var message_body;
	var cnt=0;	
	var i = 0;
	$(form).find("input[required], select[required], textarea[required]").each(function(){

		if( !$(this).is("[name]")){
			alert("REQUIED TAG '"+$(this).prop("tagName")+"' WITHOUT NAME: PLEASE CONTACT DEVELOPER");
			return false;
		}
		
		if($(this).prop("tagName") == "DIV"){
			var fieldlist = [];			
			var valueFound = false;			
			$(this).find('input, textarea').each(function(){
				fieldlist.push($(this).attr("name"));
				if($(this).val()!=""){
					valueFound = true;
					return false;
				}
			});

			if(!valueFound){
				message_body = getFieldMessage($(this).attr("name"),"required");
				showModal(modalID,formdata.modalTitle.required,message_body);				
				$(form).find("[name="+fieldlist[0]+"]").focus();
				error = true;
				return false;
			}
			
		}else{

			var v = $(this).val();
			if(!v){
				// set focus to the missing field
				message_body = getFieldMessage($(this).attr("name"),"required");
				showModal(modalID,formdata.modalTitle.required,message_body);
				if( $(this).attr("type") == "file"){
					$(this).next().addClass("btn-danger");
				}else{
					$(this).focus();
				}
				error = true;
				return false;
			}else{
				if( $(this).attr("type") == "file"){
					$(this).next().removeClass("btn-danger").addClass("btn-success");
				}
			}
		}
	});
	
	if(error) return false;

	// validate fields that require validation
	$(form).find("input[validate]")
	.each(function(){
		if( !$(this).is("[name]")){
			alert("TAG TO VALIDATE '"+$(this).prop("tagName")+"' WITHOUT NAME: PLEASE CONTACT DEVELOPER");
			return false;
		}

		var v = $(this).val();
		if(v=="") return true;
		
		switch($(this).attr("type")){
			case "text":
				alert("CANNOT VALIDATE TEXT: <"+$(this).prop("tagName")+" name='"+$(this).attr("name")+"' type='"+$(this).attr("type")+"'...");
				return false;
			break;
			case "email":
				if(!/@/.test(v)){
					error = true;
				}
			break;
			case "tel":
				if(!/^(\s)*([0-9]|\+)(\d|\s|\(|\)|\-|\.){10,}$/.test(v)){
					error = true;
				}
			break;
			case "number":
				if(!/^[0-9]+(\.)?[0-9]*$/.test(v)){
					error = true;
				}			
			break;
			case "url":
				if(!/^https?:\/\/(.)+\.(.)*$/.test(v)){
					error = true;
				}
			break;
			default:
				alert("NO VALIDATION RULE FOR THE FIELD: <"+$(this).prop("tagName")+" name='"+$(this).attr("name")+"' type='"+$(this).attr("type")+"'...");
				error = true;
		}
		
		// display error message and set the focus on the erroneus field
		if(error){
			message_body = getFieldMessage($(this).attr("name"),"validate");
			showModal(modalID,formdata.modalTitle.validate,message_body);
			$(this).focus();
			return false;
		}
	});

	if(error) return false;
	
	// submit the form if no action is set 
	// otherwise send it thorough AJAX
	
	if( !action ){
		$(form).submit();
		return true;
	}	

	// prepare post variables

	if(a)
	var postvars = {};
	$(form).find("input, textarea, select")
	.each(function(){
		postvars[$(this).attr("name")] = $(this).val();
	});

	/*var str = JSON.stringify(postvars, null, 4);
	alert(str);*/

	// proceed to sending the form
	$.post($(form).attr("action"), postvars, function(data){
		var response = jQuery.parseJSON(data);
		var title = "";
		var body = "";			
		if(response.result=="OK"){
			title = _("Form submitted");
			body = _("Thank you, we'll be in touch shortly!");
			$(form).find(".form-control").val("");
		}else{
			title = _("Form delivery failure");
			body = _("We are sorry, our server is not able to process your request. Please try again in a while, or contact us by other means");
		}
		showModal(modalID,title,body);
	})
	.fail(function(data){
		showModal(modalID,_("Identification error"), _("We are sorry, your request was not sent due to technical problem. Please reload the page and try again"));
	});

	// close the form and open button/link - if defined	
	if(slideup!=undefined){
		$('#'+slideup).slideUp();		
	}
			
	if(slidedown!=undefined){
		$('#'+slidedown).slideDown();
	}	

}