$(function(){
	 //自定义单选、多选按钮
	 setTimeout(function(){
		if($('.checkbox input').length>0){ 
			$('.checkbox input').iCheck({
				checkboxClass: 'icheckbox_minimal-orange',
				radioClass: 'iradio_minimal-orange',
				increaseArea: '20%'
			});
		}
	},10);
	
	//checkbox特效
	if($('.skin-minimal').length>0){
		$('.skin-minimal input').iCheck({checkboxClass: 'icheckbox_minimal-orange',radioClass: 'iradio_minimal-orange',increaseArea: '20%'});
	}
	
	//checkbox特效
	if($('.sel select').length>0){
		$(".sel select").selectbox();
	}
	
	//消息心跳
	if($(".newmessage").length>0){
		setInterval('heartbeat()',30000);
	}
	
})

//消息心跳
function heartbeat(){
		$.ajax({ 
			type: 'get', 
			url: '/message/heartbeat', 
			cache: false,
			async: true,
			success: function (data) {
				if(data=="0"){
					$(".newmessage a").html($(".newmessage").text());
				}else{
					$(".newmessage a").html($(".newmessage").text()+"<b></b>");
				}
			}
		});	

}


//对话框
function actbox_confirm(url,actname,stay){
	layer.alert(actname, {
		icon:3,
		time: 0, //不自动关闭
		btn: [btn_ok, btn_cancel],
		yes: function(index){
			layer.load();
			if(stay){
				$.get(url,function(data){
					layer.msg(data['info'],{time: 4000,shift:4});
					if(data['status']==1){
					setTimeout(function(){parent.document.location.reload();},1000);
					}
				});
			}else{
				document.location.href=url;	
			}
			layer.closeAll();
			
		}
	});
}
//弹窗
function actbox(title,url,width,height){
	if(width == "" || width == undefined || width == null){
		width='860px';
		height='365px';
	}
	layer.open({
	  type: 2,
	  title: title,
	  skin: 'layui-layer-lan',
	  shadeClose: false,
	  shade: 0.4,
	  area: [width,height],
	  content: url
	});
}
//刷新验证码
function veryficodepic(){
	var url=$("#veryficodepic").attr("src");
	url=url.substr(0,url.indexOf('='))+'='+Math.random();
	$("#veryficodepic").attr("src",url);
}

//表单提交
function ttgValidata(form){
	//验证必填项不能为空
	var findError=false;
	$(".required").each(function(){
		if($(this).val()==''){
			
			if($('#GO_NOW').length>0 || $('#PARENT_RELOAD').length>0 || $('#DOCUMENT_RELOAD').length>0){
				layer.tips(input_tips($(this).attr('title')),$(this),{tipsMore: true,tips: [2, '#000000']});
			}else{
				layer.msg(input_tips($(this).attr('title')));
			}
			findError=true;
			return false;
		}
	});
	if(findError){return false;}
	$("input[type='password']").each(function(){
		var password_str=$(this).val();
		if(password_str!=''){
			var Regx = /^[A-Za-z0-9]*$/;
            if (Regx.test(password_str) && password_str.length>=6) {
            	$(this).val(hex_sha1(password_str));
            }else{
				findError=true;
				$("input[type='password']").each(function(){$(this).val('');});
				layer.msg(str_password);
                return false;
            }
		}
	});
	//return true; 
	if(findError){return false;}
	if($('#GO_SUBMIT').length>0){
		return true;
	}
	if($(form).attr('ajax')=='true'){
		layer.msg('Please wait...');
		return false;	
	}
	$(form).attr('ajax','true');
	layer.load();
	data=$(form).serialize();
	url=$(form).attr('action');
	$.ajax({ 
		type: 'post', 
		url: url, 
		data:data, 
		cache: false,
		async: true,
		dataType: 'json', 
		success: function (data) {
			$(form).removeAttr('ajax');
			layer.closeAll(); 
			if(data['status']==1){
				if($('#GO_NOW').length>0){
					document.location.href=data['url'];
				}else if($('#PARENT_RELOAD').length>0){
					layer.msg(data['info'],{time: 0,shift:4});
					setTimeout(function(){parent.document.location.reload();},1000);
				}else if($('#DOCUMENT_RELOAD').length>0){
					layer.msg(data['info'],{time: 0,shift:4});
					setTimeout(function(){document.location.reload();},1000);	
				}else if($('#RESULT_DATA').length>0){
					try{ttgResultdata(data);}catch(e){alert('no function found')};	
				}else if($('#GO_BACK').length>0){
					layer.msg(data['info'],{time: 0,shift:4});
					setTimeout(function(){window.history.back();},1000);						
				}else{
					layer.msg(data['info'],{time: 0,shift:4});
					setTimeout(function(){document.location.href=data['url'];},1000);						
				}
			}else if(data['status']==0){
				layer.msg(data['info']);
				//重置密码
				$("input[type='password']").each(function(){$(this).val('');});
				//重置验证码
				if($('#veryficodepic').length>0){veryficodepic();}
			}else{
				try{
					initInfo(data);
				}catch(e){
					layer.msg(data['info']);
					//重置密码
					$("input[type='password']").each(function(){$(this).val('');});
					//重置验证码
					if($('#veryficodepic').length>0){veryficodepic();}					
				};
			}
			return false;
		}, 
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			$(form).removeAttr('ajax');
			layer.closeAll(); 
			layer.alert('Server is busy, please try again later!');
			//重置密码
			$("input[type='password']").each(function(){$(this).val('');});
			//重置验证码
			if($('#veryficodepic').length>0){veryficodepic();}
			return false;
		} 
	});			
	return false;
}