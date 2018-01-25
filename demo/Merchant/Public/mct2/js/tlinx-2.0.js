// JavaScript Document
	
	var ttgWeb = {
		//用户信息下拉
		userShow : function(){
		   $("span.user ").hover(function(){
			    $(this).find(".show_user").slideDown(100);
			},function(){
				$(this).find(".show_user").slideUp(100);
			});
		},
		//语言下拉
		userEn : function(){
		   $("span.en ").hover(function(){
			    $(this).find(".show_en").slideDown(100);
			},function(){
				$(this).find(".show_en").slideUp(100);
			});
		},
		//隔行变色
		oddColor : function(){
			$(".tr_color tbody tr:odd").css({"background-color":"#efefef"});
		},
		//左右高度相等
		lautoh : function(){
			var h = $(".content").height();
			$("menu").css("height",h + "px");
		}
		
	}
$(function(){
//IE6、7提示
	if (window.ActiveXObject) {
		var ua = navigator.userAgent.toLowerCase();
		var ie=ua.match(/msie ([\d.]+)/)[1]
		if(ie==6.0|ie==7.0){
		$("body").prepend("<div id='popDiv'>您使用的是较低版本浏览器，建议您使用IE8.0以上版本浏览器或者<a href='http://www.firefox.com.cn/download/'>firefox</a>、<a href='http://www.google.cn/intl/zh-CN/chrome/browser/desktop/index.html'>chrome</a>、<a href='http://chrome.360.cn/'>360极速</a>浏览器，获得更好的操作体验。<span></span></div>");
		}
	 }	
	 $("#popDiv span").click(function(){
	 	$("#popDiv").hide();
	 });


});


$(window).load(function() {  
	ttgWeb.userShow();
	ttgWeb.userEn();
	ttgWeb.lautoh();
});
