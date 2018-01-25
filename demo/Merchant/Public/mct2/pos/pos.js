$(function(){
	$("#sys_original_amount").keyup(function(){$(this).val($(this).val().replace(/[^.1234567890]+/g,''));new_amount(0);})
	$("#sys_discount_amount").keyup(function(){$(this).val($(this).val().replace(/[^.1234567890]+/g,''));new_amount(0);})
	$("#sys_ignore_amount").keyup(function(){$(this).val($(this).val().replace(/[^.1234567890]+/g,''));new_amount(0);})
	$("#sys_no_discount_amount").keyup(function(){$(this).val($(this).val().replace(/[^.1234567890]+/g,''));new_amount(0);})
	$("#sys_ignore_amount").keyup(function(){$(this).val($(this).val().replace(/[^.1234567890]+/g,''));new_amount(0);})
	$("#sys_trade_amount").keyup(function(){new_amount(1);})		
	$("#paylist label").click(function(){
		paytype($(this).find('input').attr('pmt_type'));
		$("#pmt_name").val(($(this).text()).replace(/[\n]/ig,''));
		})
	$("#paylist ins").click(function(){paytype($(this).parent().find('input').attr('pmt_type'));})
	$(".paytype_2 label").click(function(){paytype($(this).find('input').attr('pmt_type'));})
	$(".paytype_2 ins").click(function(){paytype($(this).parent().find('input').attr('pmt_type'));})	
})

//重新计算金额
function new_amount(t){
	var discount_amount	=$("input[name='discount_amount']").val();//折扣金额
	var original_amount	=$("input[name='original_amount']").val();//订单金额
	var ignore_amount	=$("input[name='ignore_amount']").val();//抹零金额
	var trade_amount	=$("input[name='trade_amount']").val();//实收金额
	if(original_amount==""){original_amount=0;}		
	if(discount_amount==""){discount_amount=0;}
	if(ignore_amount==""){ignore_amount=0;}
	if(trade_amount==""){trade_amount=0;}
	discount_amount	=parseFloat(discount_amount);
	original_amount	=parseFloat(original_amount);
	ignore_amount	=parseFloat(ignore_amount);
	trade_amount	=parseFloat(trade_amount);
	if(discount_amount<0){discount_amount=0;}
	if(original_amount<0){original_amount=0;}	
	if(ignore_amount<0){ignore_amount=0;}
	if(trade_amount<0){trade_amount=0;}
	if(t==0){
		trade_amount=(original_amount-discount_amount-ignore_amount).toFixed(2);
		if(trade_amount=='NaN' || trade_amount<0){
			trade_amount=0;
		}
		$("input[name='trade_amount']").val(trade_amount);
	}else{
		discount_amount=(original_amount-trade_amount-ignore_amount).toFixed(2);
		if(discount_amount=='NaN' || discount_amount<0){
			discount_amount=0;
		}
		$("input[name='discount_amount']").val(discount_amount);			
	}
}
//付款方式切换
function paytype(obj){
	if(obj=='A'){
		$(".paytype_2").show();	
		$(".paytype_2_2").hide();
	}else if(obj=='B'){
		$(".paytype_2").show();	
		$(".paytype_2_2").show();		
	}else{
		$(".paytype_1").hide();	
		$(".paytype_2").hide();	
		$(".paytype_2_2").hide();
		$(".paytype_"+obj).show();
		if(obj=="2"){
			if($("#sys_qrcode:checked").val()=='2'){
				$(".paytype_2_2").show();	
			}
		}		
	}
	$("#sys_trade_account").val('');
	$("#sys_trade_no").val('');
	$("#sys_auth_code").val('');
}
//抹零
function ignore(t){
	var trade_amount=$("input[name='trade_amount']").val();//实收金额
	var index=trade_amount.indexOf('.')+1;
	var ignore_amount=trade_amount.substr(index,2);
	if(t==0){
		ignore_amount='0.0'+ignore_amount.substr(1,1);
	}else{
		ignore_amount='0.'+ignore_amount;
	}
	$("input[name='ignore_amount']").val(ignore_amount);
	new_amount(0);
	
}

function open_cardbag(){
	if($("#sys_original_amount").val()==""){
		layer.msg('交易金额不能为空，请重新打开券包');
		setTimeout(function(){parent.layer.closeAll();},2000)
		return false;
	}
	layer.open({
	  type: 2,
	  title: '卡券包',
	  skin: 'layui-layer-lan',
	  shadeClose: false,
	  shade: 0.8,
	  area: ['860px','365px'],
	  content: '/posapp/cardbag_index?ord_no='+$('#sys_ord_no').val()
	});
}

function cardbag_result(){
	var data='ord_no='+$('#sys_ord_no').val();
	var url='/posapp/cardbag_result';
	$.ajax({ 
		type: 'post', 
		url: url, 
		cache: false,
		data: data,
		async: true,
		dataType: 'json', 
		success: function (data) {
			if(data['errcode']==0){
				
				var sys_discount_amount=parseFloat($("#sys_discount_amount").attr("init_val"));
				sys_discount_amount+=parseFloat(data['data']['discount']);
				sys_discount_amount=sys_discount_amount/100;
				layer.alert(sys_discount_amount);
				$("#sys_discount_amount").val(sys_discount_amount);
				$("#sys_discount_amount").keyup();
				$("#cardbag_app_para").html(data['data']['textarea']);
				$("#cardbag_str").html(data['data']['str']);
			}
		}, 
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			layer.alert('服务器繁忙，请稍候再试！<br/>'+textStatus+'：'+errorThrown);
		} 
	});	
}


/*
	str+='<textarea style="display:none" name="app_para[]">{:json_encode($vo_trade)}</textarea>';
	str_html+='<strong>交易号</strong>：{$vo_trade['trade_no']} <strong>交易金额</strong>：￥{$vo_trade['amount']/100}<switch name="vo_trade['type']" ><case value="2" break="1"> <strong>折扣金额</strong>：￥{$vo_trade['discount']/100}</case><case value="5" break="1"> <strong>抵现金额</strong>：￥{$vo_trade['discount']/100}</case><case value="3" break="1"><strong> 本次积分</strong>：{$vo_trade['discount']}</case><case value="4" break="1"><strong> 礼品</strong>：{$vo_trade['remark']}</case></switch><strong> 备注</strong>：{$vo_trade['remark']}<br/>';

	*/
/*function app_check(){
	var str='';
	var errstr='';
	$("#app_area tr").each(function(){
		this_type=$(this).find('input').attr('type');
		required=$(this).attr('required');
		if(this_type==undefined){
			obj=$(this).find('select');
			if(typeof obj=="object"){
				this_type='select';
			}else{
				return;	
			}
		}
		
		if(this_type=='select'){
			this_value=$(this).find('select').val();
			if(required && this_value==''){
				errstr+=$(this).find('select').attr('placeholder')+'不能为空<br/>';	
				return;
			}
			str+='&'+$(this).find('select').attr('id')+'='+encodeURI(this_value);	
		}else if(this_type=='checkbox' || this_type=='radio'){
			str1='';
			$(this).find('input').each(function(){
				if($(this).attr('checked')=='checked'){
					str1+='&'+$(this).attr('id')+'='+encodeURI($(this).attr('value'));	
				}
			});
			if(required && str1==''){
				errstr+=$(this).find('input').attr('placeholder')+'不能为空<br/>';	
				return;
			}			
			str+=str1;
		}else if(this_type!='button' && this_type!='submit'){
			this_value=$(this).find('input').val();
			if(required && this_value==''){
				errstr+=$(this).find('input').attr('placeholder')+'不能为空<br/>';	
				return;
			}
			if(this_type=='password'){
				encry=$(this).attr('encry');
				if(encry=='md5'){
					this_value=hex_md5(this_value);
				}else if(encry=='sha1'){
					this_value=hex_sha1(this_value);
				}
			}
			str+='&'+$(this).find('input').attr('id')+'='+encodeURI(this_value);					
		}
	});
	if(errstr!=''){
		ui.alert(errstr);
		return false;
	}
	if(str!=''){
		url='/Posapp/cashier_entry?t='+Math.random()+str;
		$.ajax({ 
			type: 'get', 
			url: url, 
			cache: true,
			async: false,
			dataType: 'json', 
			timeout:10000,
			success: function (data) {
				if(data['errcode']!='0'){
					ui.alert(data['msg']);
					return;
				}else{
					if($("#trade_type").val()=='query'){
						if(data['data']['status']!=1){
							ui.alert(data['remark']);
							return;
						}
						$(".ui_box span").click();
						app(str.replace("trade_type=query","trade_type=trade"),$(".ui_box_title strong").text());
					}else if($("#trade_type").val()=='trade'){
							if(data['data']['status']!=2){
								ui.alert(data['remark']);
								return;
							}
							str='<li><div class="name">'+$(".ui_box_title strong").text()+'：</div>';
							$(".ui_box span").click();
							str+='<strong>交易号</strong>：'+data['data']['trade_no'];	
							str+='<br/><strong>交易金额</strong>：￥'+(parseFloat(data['data']['amount'])/100);						
							if(data['data']['type']==2){
								str+='<br/><strong>折扣金额</strong>：￥'+(parseFloat(data['data']['discount'])/100);
								var sys_discount_amount=parseFloat($("#sys_discount_amount").val());
								sys_discount_amount+=(parseFloat(data['data']['discount'])/100);
								$("#sys_discount_amount").val(sys_discount_amount);
								$("#sys_discount_amount").keyup();
							}else if(data['data']['type']==5){
								str+='<br/><strong>抵现金额</strong>：￥'+(parseFloat(data['data']['discount'])/100);
								var sys_discount_amount=parseFloat($("#sys_discount_amount").val());
								sys_discount_amount+=(parseFloat(data['data']['discount'])/100);
								$("#sys_discount_amount").val(sys_discount_amount);
								$("#sys_discount_amount").keyup();
							}else if(data['data']['type']==3){
								str+='<br/><strong>本次积分</strong>：'+data['data']['discount'];
							}else if(data['data']['type']==4){
								str+='<br/><strong>礼品</strong>：'+data['data']['remark'];
							}
							str+='<br/><strong>备注</strong>：'+data['data']['remark'];
							var str_json=JSON.stringify(data['data']);
							str+='<textarea style="display:none" name="app_para[]">'+str_json+'</textarea></li>';
							$("#kaqubao").after(str);
					}
				};
			}, 
			error: function (XMLHttpRequest, textStatus, errorThrown) { 
				ui.alert('服务器繁忙');
			} 
		});
		
	}
}
function app(query,app_name){
	if($("#sys_original_amount").val()==""){
		ui.alert("订单金额不能为空");
		return false;	
	}
	var b=ui.box(app_name,'/posapp/cashier_app',query,true,function(a,b){
        b.innerHTML=a;
		$("#ord_no").val($("#sys_ord_no").val());
		$("#original_amount").val(parseFloat($("#sys_original_amount").val())*100);
		$("#no_discount_amount").val(parseFloat($("#sys_no_discount_amount").val())*100);
		$("#discount_amount").val(parseFloat($("#sys_discount_amount").val())*100);
		$("#trade_amount").val(parseFloat($("#sys_trade_amount").val())*100);
	});
}

function checkpos(){
		
}*/