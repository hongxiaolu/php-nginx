$(function(){
	$(".marquee").each(function() {
		var p_length=$(this).find('p').length;
		if(p_length>1){
			$(this).find('p:gt(0)').hide();
			marquee_show($(this),p_length-1,p_length-1);	
		}
    });
});
function marquee_show(obj,index,p_length){
	var new_index=index+1;
	if(new_index>p_length){
		new_index=0;	
	}
	$(obj).find('p').eq(index).fadeOut(function(){
		$(obj).find('p').eq(new_index).fadeIn(function(){
			setTimeout(function(){
				marquee_show(obj,new_index,p_length);
			},2000);			
		});
	});

}