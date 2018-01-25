function checkboxall(obj,int){
	if(int==1){
		if($(obj).attr('checked')){
			$(obj).parents('tr').find("input").attr('checked',true);
			$(obj).parents('tr').find('.icheckbox_minimal-orange').addClass('checked');
		}else{
			$(obj).parents('tr').find("input").attr('checked',false);
			$(obj).parents('tr').find('.icheckbox_minimal-orange').removeClass('checked');		
		}
	}
	var roles='';
	$(obj).parents('tr').find("input:checked").each(function(){
		roles+=$(this).val()+',';	
	});
	$(obj).parents('tr').find("#roles").val(roles);
	
}
$(function(){
	$(".role_tr").each(function(){
		$(this).html(role_str);	
		$(this).find("input").attr('onclick','checkboxall(this,2)');	
		var roles=$(this).parents('tr').find('#roles').val();
		if(roles!=""){
			arr_roles=roles.split(',');
			$(this).parents('tr').find("input").each(function(){
				for(i=0;i<arr_roles.length;i++){
					if(arr_roles[i]!="" && arr_roles[i]==$(this).val()){
						$(this).attr('checked',true);
					}
				}
			})
		}
	});
})