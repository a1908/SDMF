	
	// important! labels should preceed input fields!
	function processForm(form,token){

		// function to show message 
		function showMessage(id, message, title){
			if(!(typeof message === "string") ){
				alert("ShowMessage:\nMessage is not a string\nContact application developer");
				return;
			}
			
			if($(form).is("[modal]")){
				$(id+' .modal-title').html(title);
				$(id+' .modal-body').html(message.replace(/(\r\n|\r|\n)/g, '<br>'));
				$(id).modal('show');
			}else{
				alert(message);
			}
		}

		// check required fields
		var error = false; 
		
		if($(form).is("[modal]")){
			var modal_id = "#"+$(form).attr("modal");
			$(modal_id).on('hidden.bs.modal', function (e) {
				$(form).find(".error-field").focus();
			});
		}else{
			modal_id = null;
		}	
		
		$(form).find("[required]").removeClass("error-field");
		
		// check required fields
		$(form).find("[required]").each(function(){
			if( !$(this).is("[name]") ){
				alert("REQUIED TAG '"+$(this).prop("tagName")+"' WITHOUT NAME: PLEASE CONTACT DEVELOPER");
				return false;
			}
			var v = $(this).val();
			if( v=="" || v==null ){
				error = true;
				$(this).addClass("error-field");
				showMessage(modal_id,"Отсутствует обязательное поле: '"+$(this).prev().text()+"'","Ошибка заполнения формы");
				$(this).focus();
			}
			return !error;
		});
			
		if(error)
			return;
			
			
		// validate fields
		$(form).find("[validate]").each(function(){
			var v = $(this).val();
			if(v=="") return true;
			var errmsg ="";
			switch($(this).attr("type")){
				case "text":
					alert("CANNOT VALIDATE TEXT: <"+$(this).prop("tagName")+" name='"+$(this).attr("name")+"' type='"+$(this).attr("type")+"'...");
					return false;
				break;
				case "zip":
					if(!/^\d{6}$/.test(v)){
						errmsg = "индекс должен содержать 6 цифр";
						error = true;
					}
				break;
				case "email":
					if(!/^.+@.+\..+/.test(v)){
						errmsg = "неверный адрес электронной почты\nадрес должен соответствовать формату mailbox@domain.tld";
						error = true;
					}
				break;
				case "tel":
					if(!/^(8|\+7)\d{10,}$/.test(v)){
						errmsg = "неверный номер телефона\nдолжен содержать только цифры и начинаться с 8 или +7";
						error = true;
					}
				break;
				case "number":
					if(!/^[0-9]+(\.)?[0-9]*$/.test(v)){
						errmsg = "поле должно содержать цифры";
						error = true;
					}			
				break;
				case "url":
					if(!/^https?:\/\/(.)+\.(.)*$/.test(v)){
						errmsg = "неверный формат URL\nдолжен соответсвоать формату http://domainname.tld";
						error = true;
					}
				break;
				default:
					alert("NO VALIDATION RULE FOR THE FIELD: <"+$(this).prop("tagName")+" name='"+$(this).attr("name")+"' type='"+$(this).attr("type")+"'...");
					error = true;
			}
			
			// display error message and set the focus on the erroneus field
			if(error){
				$(this).addClass("error-field");
				showMessage(modal_id,"Ошибка в поле: '"+$(this).prev().text()+"'\n"+errmsg, "Ошибка заполнения формы");
				$(this).focus();
				return false;
			}
		});


		if(error)
			return;
			
		
		var names = [];
		var captions = [];
		var data = [];
		var data_types = [];
		
		$(form).find('label').each(function(){
			
			var parent = $(this).parent();
			
			if( $(parent).hasClass("form-group") ){
				var next = $(this).next();
				var nextTag = $(next).prop("tagName");
				if( nextTag == "INPUT" || nextTag == "SELECT" || nextTag == "TEXTAREA" ){
					names.push($(next).prop("name"));
					captions.push($(this).text());
					data.push($(next).val());
					switch(nextTag){
						case "INPUT":
							data_types.push($(next).prop("type"));
						break;
						
						case "TEXTAREA":
							data_types.push("textarea");
						break;	
						
						case "SELECT":
							data_types.push("select");
						break;	
						
						default:
							data_types.push($(next).prop("type"));
						break;
					}
				}
			}else if( $(parent).hasClass("checkbox") ){
				var checkbox = $(parent).find("input[type=checkbox]");
				if( $(checkbox).is(":checked")){
					names.push($(checkbox).prop("name"));
					captions.push("чекбокс");
					data.push($(checkbox).val());
					data_types.push("checkbox");
				}
			}
		});
		
		$.post($(form).attr("action"),{names:names,captions:captions, data:data, data_types:data_types, token:token})
		.done(function(data){
						
			var title = $(form).find(".form-result .title").html();
			
			if( data == -1 ){
				var message = "сбой при при обработке формы, повторите, пожалуйста, позже";
			}else{
				if( data.substr(0,5) == "debug" ){
					message = data;
				}else{
					message = $(form).find(".form-result .message[code="+data+"]").html();
				}
				
			}
			showMessage(modal_id,message, title);
			
			if( data == 0 ){
				form.reset();
				$(form).find('[originallydisabled]').prop('disabled',true);
				if(form.hasAttribute("nextpage")){
					if(modal_id){
						$(modal_id).on('hidden.bs.modal', function (e) {
							location = form.getAttribute("nextpage");
						})
					}else{
						location = form.getAttribute("nextpage");
					}
				}
			}
		})
	}
	