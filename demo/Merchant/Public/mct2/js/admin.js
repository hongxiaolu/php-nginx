$(function(){
	//权限详情
	json=eval(json);
	$(".perm_list li label span").hover(function(){
		if($(this).parent().parent().find('.show_perm').length<=0){
			var ids=$(this).attr("id").split(',');
			var strhtml='<div class="show_perm"><b></b>';
			$.each(json,function(name,value) {
				$.each(value,function(name2,value2) {
					if(typeof value2=="object"){
						strhtml+='<ul class="clearfix">';
						$.each(value2,function(name3,value3) {
							$.each(ids,function(name4,value4) {
								if(name3==value4){
									strhtml+='<li>'+value3+'</li>';
								}
							});
						});
						strhtml+='</ul>';
					}else{ 
						strhtml+='<h4>'+value2+'</h4><ul class="clearfix">';
					}
				});
			});
			strhtml+='</div>';
			$(this).parent().after(strhtml);	
		}
		$(this).parent().next(".show_perm").show();	
	},function(){
		$(this).parent().next(".show_perm").hide();	
	});
	setTimeout(function(){
		$("#checked_all_list li:eq(0) *").click(function(){
			if($("#checked_all_list input:eq(0)").attr("checked")=="checked"){
				$("#checked_all_list input").attr("checked","checked");
				$("#checked_all_list .icheckbox_minimal-orange").addClass("checked");	
			}else{
				$("#checked_all_list input").removeAttr("checked","checked");
				$("#checked_all_list .icheckbox_minimal-orange").removeClass("checked");					
			}
		})
		
		$("#checked_all_list li:gt(0) *").click(function(){
			if($("#checked_all_list input").length-$("#checked_all_list input:checked").length==1){
				$("#checked_all_list input:eq(0)").attr("checked","checked");
				$("#checked_all_list .icheckbox_minimal-orange:eq(0)").addClass("checked");				
			}else{
				$("#checked_all_list input:eq(0)").removeAttr("checked","checked");
				$("#checked_all_list .icheckbox_minimal-orange:eq(0)").removeClass("checked");				
			}
		})
	},1000);
	//全选效果
	if($("#checked_all_list input").length-$("#checked_all_list input:checked").length==1){
		$("#checked_input").attr("checked",true);
	}
});