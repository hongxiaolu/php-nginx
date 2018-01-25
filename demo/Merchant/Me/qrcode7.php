<?php
function qrcode_draw($result,$savepath){

	$qrcode_num=7;
	#生成文件保存路径
	$qrcode_path=$savepath.'/qrcode_'.$result['shop_no'].'_'.$qrcode_num.'_template.png';

	#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
	if(!file_exists($qrcode_path) || time()-filectime($qrcode_path)>60){
		if(file_exists($qrcode_path)){
			unlink($qrcode_path);	
		}
		#纯二维码本地保存路径
		$qrcode_savepath=$savepath.'/qrcode_'.$result['shop_no'].'_'.$qrcode_num.'_qrcode.png';

		//生成二维码图片 
		Vendor('phpqrcode.phpqrcode');
		$errorCorrectionLevel ='H';//容错级别 
		$matrixPointSize = 17;//生成图片大小 
		$QRcode = new \QRcode();
		$QRcode->png($result['server'].$result['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize, 0);
//		$QRcode->png($result['data']['qrpay'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize, 0);

		unset($QRcode);
		
		#缩放二维码图片
		$image = new \Think\Image(); 
		$image->open($qrcode_savepath)->thumb(600,600);
		#水印二维码上的LOGO，尺寸200*200
		if(file_exists('../Merchant/Me/qrcode'.$qrcode_num.'_logo.png')){
			$image->water('../Merchant/Me/qrcode'.$qrcode_num.'_logo.png',array(220,220),100);#水印位置
		}
		$image->save($qrcode_savepath);	
		unset($image);

		#图片宽度
		$picwidth=1299;
		#字体
		$font=THINK_PATH.'font/microsoft.ttf';
		#图像宽1410，高2000
		$image = new \Think\Image();

		$image->open('../Merchant/Me/qrcode'.$qrcode_num.'.png');

		#水印二维码
		$image->water($qrcode_savepath,array(350,470),100);#水印位置

		#门店名称
		$shopname=mb_substr($result['shop_full_name'],0,17);
		$image->text($shopname,$font,40,'#000000',array(450,1355));

		#门店地址
		$autoHeight=0;
		$address=L('_SHOP_ADDRESS_').'：'.$result['address'];
		$image->text(mb_substr($address,0,20),$font,30,'#000000',array(450,1440)); 
		if(mb_strlen($address)>20){
			$autoHeight=50;
			$image->text(mb_substr($address,20,20),$font,30,'#000000',array(450,1440+$autoHeight)); 	
		}
		
		#门店电话
		if(!empty($result['tel'])){
			$address=mb_substr(L('_SHOP_TEL_').'：'.$result['tel'],0,26);
			$image->text($address,$font,30,'#000000',array(450,1500+$autoHeight));
		}
		#服务热线
		if(!empty($result['tel'])){
			$server_tel=mb_substr(L('_SHOP_SERVICE_TEL_').'：'.$result['org_services_tel'],0,26);
			$image->text($server_tel,$font,30,'#000000',array(450,1560+$autoHeight));
		}

		$image->save($qrcode_path);	
		unlink($qrcode_savepath);
		unset($image);
	}
	return array(true,$qrcode_path);
}