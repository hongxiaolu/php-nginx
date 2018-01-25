<?php
namespace Mct2\Controller;
use Think\Controller;
class ShopController extends Controller {
	#门店列表
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(110); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('shop',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('header',L('_PAGE_HEADER_'));
			$Page->setConfig('theme', '<div class="fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </div><span>%HEADER%</span>');
			foreach($map as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}
			// 赋值分页输出
			$this->page=$Page->show();
			$this->list=$result['data']['list'];
		}else{
			$this->page=null;
			$this->list=null;
		}
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}	
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',4);
		$this->display();
    }
	
	#修改主管密码
    public function password(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('shop/detail',$map);
		$this->rows=$result['data'];
		$this->assign('menuid',4);	
		$this->assign('left_menuid',4);
		$this->display();
    }	
	
	#修改主管密码
    public function password_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('post.');//取得所有post参数
		if($map['password']!=$map['password2']){
			$this->error(L('_SHOP_EDIT_PASS_ERR_'));
			exit();
		}
		$result=$Tlinx->get('shop/password',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/shop');
		}else{
			$this->error($result['msg']);
		}
    }	
	
	#查看门店
    public function detail(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('shop/detail',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
		$this->assign('menuid',4);	
		$this->assign('left_menuid',4);
		$this->display();
    }	
		
	#自定义付款方式
    public function diypayment(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('shop/diypayment',$map);
		$this->rows=$result['data'];
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',4);
		$this->display();
    }	
	
	#添加自定义付款方式
    public function diypayment_add(){
		C('SHOW_PAGE_TRACE',false);
		$map=I('');
		$this->map=$map;
		$this->display();
    }	
		
	#修改自定义付款方式
    public function diypayment_edit(){
		C('SHOW_PAGE_TRACE',false);
		$map=I('');
		$this->map=$map;
		$this->display();
    }	
		
	#添加自定义付款方式
    public function diypayment_addsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('post.');//取得所有post参数
		$result=$Tlinx->get('shop/diypayment_add',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);
		}else{
			$this->error($result['msg']);
		}
    }
	
	#删除自定义付款方式
    public function diypayment_delete(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('get.');//取得所有post参数
		$result=$Tlinx->get('shop/diypayment_delete',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);
		}else{
			$this->error($result['msg']);
		}
    }
	
	#修改自定义付款方式
    public function diypayment_editsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('post.');//取得所有post参数
		$result=$Tlinx->get('shop/diypayment_edit',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);
		}else{
			$this->error($result['msg']);
		}
    }		
	
	#二维码收款
    public function qrcode(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(110); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		$map['payment']=1;
		//调接口取数据
		$result=$Tlinx->get('shop',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			exit();
		}
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('header',L('_PAGE_HEADER_'));
			$Page->setConfig('theme', '<div class="fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </div><span>%HEADER%</span>');
			foreach($map as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}
			// 赋值分页输出
			$this->page=$Page->show();
			$this->list=$result['data']['list'];
		}else{
			$this->page=null;
			$this->list=null;
		}
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}	
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',9);
		$this->assign('qrcode_list',C('mct_qrcode_list'));
		$this->display();
    }	
		
	#获取文字坐标
	private function font_x($picwidth,$text,$size,$font){
		$info = imagettfbbox($size, 0,$font, $text);
        $minx = min($info[0], $info[2], $info[4], $info[6]); 
        $maxx = max($info[0], $info[2], $info[4], $info[6]); 
        $miny = min($info[1], $info[3], $info[5], $info[7]); 
        $maxy = max($info[1], $info[3], $info[5], $info[7]); 
        /* 计算文字初始坐标和尺寸 */
        $x = $minx;
        $y = abs($miny);
        $w = $maxx - $minx;
        $h = $maxy - $miny;
		$x += ($picwidth - $w)/2;
		return array($x,$w);
	}	
	
	#二维码收款图片
    public function qrcode_down(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(111); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('shop/qrcode',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			exit();
		}
		
		#输出风格	
		if(empty($map['sizetype'])){
			$map['sizetype']=1;
		}
		$map['sizetype']=(int)$map['sizetype'];
		
		#本地保存路径
		$savepath=C('TEMP_PATH').date('Ymd');
		if(!file_exists($savepath)&&!mkdir($savepath,0777,true)){
			$this->error('服务器无法写入文件');
			exit();
		}
		#本地保存文件地址
		$qrcode_path=$savepath.'/qrcode_'.$result['data']['open_id'].'_'.$map['sizetype'].'.png';
		
		#二维码处理函数
		$qrcode_function='./Me/qrcode'.$map['sizetype'].'.php';
		if(file_exists($qrcode_function)){
			include($qrcode_function);
			$draw_result=qrcode_draw($result['data'],$savepath);
		}else{
			#二维码文件名保存路径
			if($map['sizetype']==1){
				$draw_result=$this->qrcode_draw_1($result,$savepath);	
			}else if($map['sizetype']==2){
				$draw_result=$this->qrcode_draw_2($result,$savepath);	
			}else if($map['sizetype']==3){
				$draw_result=$this->qrcode_draw_3($result,$savepath);	
			}else if($map['sizetype']==4){
				$draw_result=$this->qrcode_draw_4($result,$savepath);	
			}else if($map['sizetype']==5){
				$draw_result=$this->qrcode_draw_5($result,$savepath);			
			}else if($map['sizetype']==6){
				$draw_result=$this->qrcode_draw_6($result,$savepath);					
			}else{
				$this->error('error module');
				exit();			
			}
		}
		if(!($draw_result[0])){
			$this->error($draw_result[1]);
			exit();					
		}
		if($draw_result[1]!='ok'){
			$qrcode_path=$draw_result[1];
		}
		if(empty($map['preview'])){
			header("Content-type: octet/stream");
			header("Content-disposition:attachment;filename=".mb_convert_encoding(L("_SYS_QRCODE_PAY_"),'gbk','utf8')."(".mb_convert_encoding($result['data']['shop_name'],'gbk','utf8').")".".png;");
			header("Content-Length:".filesize($qrcode_path));
			readfile($qrcode_path);
		}else{
			#预览
			$info=getimagesize($qrcode_path);
			$file=fread(fopen($qrcode_path,'rb'),filesize($qrcode_path));
			header("content-type:".$info['mime']);
			echo $file;
		}
    }	
	
	#模块1样式
	private function qrcode_draw_1($result,$savepath){	
		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_1.png';			
		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
	
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_1_qrcode.png';	
			#二维码尺寸	
			$qrcode_size=700;
	
			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';//容错级别 
				$matrixPointSize = 17;//生成图片大小 
				$QRcode = new \QRcode();
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize, 0); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode1_logo.png')){
					$image->water('./Me/qrcode1_logo.png',array(250,250),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}

			#图片宽度
			$picwidth=1410;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode.png');
			#水印二维码
			$image->water($qrcode_savepath,array(355,725),100);#水印位置
			
			#门店名称
			$shopname=$result['data']['shop_full_name'];
			$x=self::font_x($picwidth,$shopname,32,$font);
			$image->text($shopname,$font,32,'#FFFFFF',array($x[0],1490)); 
			
			#门店地址
			$address=L('_SHOP_ADDRESS_').' | '.$result['data']['address'];
			$x=self::font_x($picwidth,$address,32,$font);
			$image->text($address,$font,32,'#FFFFFF',array($x[0],1550)); 
			
			#门店电话
			if(!empty($result['data']['tel'])){
				$tel_name	=L('_SHOP_TEL_').' | ';
				$tel_num	=$result['data']['tel'];
				$tel_name_w =self::font_x($picwidth,$tel_name,32,$font);
				$tel_num_w  =self::font_x($picwidth,$tel_num,60,$font);
				$x=($picwidth-($tel_name_w[1]+$tel_num_w[1]))/2;
				#获得电话左边位置
				$image->text($tel_name,$font,32,'#FFFFFF',array($x,1630)); 
				$image->text($tel_num,$font,60,'#FFFFFF',array($x+$tel_name_w[1],1620)); 
			}	
			#服务热线
			if(!empty($result['data']['tel'])){
				$server_tel=mb_substr(L('_SHOP_SERVICE_TEL_').' | '.$result['data']['org_services_tel'],0,22);
				$image->text($server_tel,$font,30,'#FFFFFF',array(626,1835));
			}
			
			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
		}
		return array(true,'ok');
	}
	
	#模块2样式
	private function qrcode_draw_2($result,$savepath){

		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_2.png';			

		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
			#运营平台LOGO本地保存路径
			$org_logo_savepath=$savepath.'/org_logo_'.$result['data']['org_no'].'.png';	
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_2_qrcode.png';
			#二维码尺寸	
			$qrcode_size=700;
			#运营平台LOGO尺寸
			$org_logo_size=350;

			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';//容错级别 
				$matrixPointSize = 17;//生成图片大小 
				$QRcode = new \QRcode();
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize, 0); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode2_logo.png')){
					$image->water('./Me/qrcode2_logo.png',array(250,250),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}
			
			#判断运营平台LOGO是否存在，如果不存在就下载
			if(!file_exists($org_logo_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				ob_start(); 
				readfile($result['data']['org_logo']); 
				$img = ob_get_contents(); 
				if(!$img){
					return array(false,'加载运营平台LOGO出错'.$result['data']['org_logo']);
					exit();
				}
				ob_end_clean(); 
				$size = strlen($img); 
				$fp2 = fopen($org_logo_savepath , "a"); 
				fwrite($fp2, $img); 
				fclose($fp2);
			}
			
			#图片宽度
			$picwidth=1410;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode2.png');
			#水印二维码
			$image->water($qrcode_savepath,array(354,510),100);#水印位置
			
			#本例LOGO路径
			$image->water($org_logo_savepath,array(55,1527),100);#水印位置
			#门店名称
			$shopname=mb_substr($result['data']['shop_full_name'],0,17);
			$image->text($shopname,$font,40,'#000000',array(450,1550)); 
			
			#门店地址
			$autoHeight=0;
			$address=L('_SHOP_ADDRESS_').'：'.$result['data']['address'];
			$image->text(mb_substr($address,0,24),$font,30,'#000000',array(450,1630)); 
			if(mb_strlen($address)>24){
				$autoHeight=50;
				$image->text(mb_substr($address,24,24),$font,30,'#000000',array(450,1630+$autoHeight)); 	
			}
			
			#门店电话
			if(!empty($result['data']['tel'])){
				$address=mb_substr(L('_SHOP_TEL_').'：'.$result['data']['tel'],0,26);
				$image->text($address,$font,30,'#000000',array(450,1690+$autoHeight));
			}
			#服务热线
			if(!empty($result['data']['tel'])){
				$server_tel=mb_substr(L('_SHOP_SERVICE_TEL_').'：'.$result['data']['org_services_tel'],0,26);
				$image->text($server_tel,$font,30,'#000000',array(450,1750+$autoHeight));
			}
			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
			unset($image);
		}
		return array(true,'ok');
	}
	
	#模块3样式
	private function qrcode_draw_3($result,$savepath){

		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_3.png';			

		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_3_qrcode.png';

			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				#二维码尺寸	
				$qrcode_size=800;
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';// 纠错级别：L、M、Q、H
				$matrixPointSize = 20;//生成图片大小 
				$QRcode = new \QRcode();
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize, 0); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode3_logo.png')){
					$image->water('./Me/qrcode3_logo.png',array(300,300),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}
			
			
			
			#图片宽度
			$picwidth=2000;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode3.png');
			$image->water($qrcode_savepath,array(600,1090),100);#水印位置
			
			#门店编号
			$shop_no='NO.'.$result['data']['shop_no'];
			$x=self::font_x($picwidth,$shop_no,46,$font);
			$image->text($shop_no,$font,46,'#8c8c8c',array($x[0],1918)); 

			
			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
			unset($image);
		}
		return array(true,'ok');
	}
	
	#模块4样式
	private function qrcode_draw_4($result,$savepath){	
		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_4.png';			
		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
	
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_4_qrcode.png';	
			#二维码尺寸	
			$qrcode_size=700;
	
			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';//容错级别 
				$matrixPointSize = 17;//生成图片大小 
				$QRcode = new \QRcode();
				$color['fore_color']=array(242,112,0);#二维码颜色
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize,1,0,$color); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode4_logo.png')){
					$image->water('./Me/qrcode4_logo.png',array(250,250),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}

			#图片宽度
			$picwidth=1412;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode4.png');
			#水印二维码
			$image->water($qrcode_savepath,array(355,635),100);#水印位置
			
			#门店名称
			$shopname=$result['data']['shop_full_name'];
			$x=self::font_x($picwidth,$shopname,30,$font);
			$image->text($shopname,$font,30,'#FFFFFF',array($x[0],1395)); 
			
			#门店地址
			$address=L('_SHOP_ADDRESS_').' : '.$result['data']['address'];
			$x=self::font_x($picwidth,$address,30,$font);
			$image->text($address,$font,30,'#FFFFFF',array($x[0],1455)); 
			
			#门店电话
			if(!empty($result['data']['tel'])){
		
				$tel_name	=L('_SHOP_TEL_').' : ';
				$tel_num	=$result['data']['tel'];
				$tel_name_w =self::font_x($picwidth,$tel_name,30,$font);
				$tel_num_w  =self::font_x($picwidth,$tel_num,45,$font);
				$x=($picwidth-($tel_name_w[1]+$tel_num_w[1]))/2;
				#获得电话左边位置
				$image->text($tel_name,$font,30,'#FFFFFF',array($x,1525)); 
				$image->text($tel_num,$font,45,'#FFFFFF',array($x+$tel_name_w[1],1520)); 
			}
			
			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
		}
		return array(true,'ok');
	}
	
	#模块5样式
	private function qrcode_draw_5($result,$savepath){	
		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_5.png';			
		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
	
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_5_qrcode.png';	
			#二维码尺寸	
			$qrcode_size=700;
	
			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';//容错级别 
				$matrixPointSize = 14;//生成图片大小 
				$QRcode = new \QRcode();
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize,1,0,$color); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode5_logo.png')){
					$image->water('./Me/qrcode5_logo.png',array(220,220),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}

			#图片宽度
			$picwidth=1412;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode5.png');
			#水印二维码
			$image->water($qrcode_savepath,array(405,382),100);#水印位置
			
			#门店名称
			$shopname=$result['data']['shop_full_name'];
			$x=self::font_x($picwidth,$shopname,40,$font);
			$image->text($shopname,$font,40,'#FFFFFF',array($x[0],1160)); 
			
			#门店编号
			$shopID='ID:'.$result['data']['shop_no'];
			$x=self::font_x($picwidth,$shopID,40,$font);
			$image->text($shopID,$font,40,'#FFFFFF',array($x[0],1250)); 

			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
		}
		return array(true,'ok');
	}
	
	#模块6样式
	private function qrcode_draw_6($result,$savepath){	
		#物料保存路径
		$wuliao_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_6.png';			
		#判断物料图片否存在或者创建时间是否大于600秒，如果不存在就创建
		if(!file_exists($wuliao_savepath) || time()-filectime($wuliao_savepath)>600){
			if(file_exists($wuliao_savepath)){
				unlink($wuliao_savepath);	
			}
	
			#纯二维码本地保存路径
			$qrcode_savepath=$savepath.'/qrcode_'.$result['data']['open_id'].'_6_qrcode.png';	
			#二维码尺寸	
			$qrcode_size=700;
	
			#判断二维码图片是否存在或者创建时间是否大于600秒，如果不存在就创建
			if(!file_exists($qrcode_savepath) || time()-filectime($qrcode_savepath)>600){
				//生成二维码图片 
				Vendor('phpqrcode.phpqrcode');
				$errorCorrectionLevel ='H';//容错级别 
				$matrixPointSize = 14;//生成图片大小 
				$QRcode = new \QRcode();
				$QRcode->png($result['data']['server'].$result['data']['open_id'], $qrcode_savepath, $errorCorrectionLevel, $matrixPointSize,1,0,$color); 
				unset($QRcode);
				#缩放二维码图片
				$image = new \Think\Image(); 
				$image->open($qrcode_savepath)->thumb($qrcode_size,$qrcode_size);
				#水印二维码上的LOGO，尺寸200*200
				if(file_exists('./Me/qrcode6_logo.png')){
					$image->water('./Me/qrcode6_logo.png',array(220,220),100);#水印位置	
				}
				$image->save($qrcode_savepath);;		
				unset($image);
			}

			#图片宽度
			$picwidth=1412;
			#字体
			$font=THINK_PATH.'font/microsoft.ttf';
			#图像宽1410，高2000
			$image = new \Think\Image(); 
			$image->open('./Me/qrcode6.png');
			#水印二维码
			$image->water($qrcode_savepath,array(405,575),100);#水印位置
			
			#门店名称
			$shopname=$result['data']['shop_full_name'];
			$x=self::font_x($picwidth,$shopname,40,$font);
			$image->text($shopname,$font,40,'#FFFFFF',array($x[0],1330)); 
			
			#门店编号
			$shopID='ID:'.$result['data']['shop_no'];
			$x=self::font_x($picwidth,$shopID,40,$font);
			$image->text($shopID,$font,40,'#FFFFFF',array($x[0],1420)); 

			$image->save($wuliao_savepath);	
			unlink($qrcode_savepath);
		}
		return array(true,'ok');
	}	
}